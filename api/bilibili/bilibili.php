<?php
/**
 * @Author: JH-Ahua
 * @CreateTime: 2025/12/29 下午6:52
 * @email: admin@bugpk.com
 * @blog: www.jiuhunwl.cn
 * @Api: api.bugpk.com
 * @tip: bilibili解析-旧版
 */
//这里填写你的B站cookie(不填解析不到1080P以上) 格式为_uuid=XXXXX
$cookie = '';
$headers = ['Content-type: application/json;charset=UTF-8'];
$useragent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.81 Safari/537.36';
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
$urls = isset($_GET['url']) ? $_GET['url'] : '';
if (empty($urls)) {
    exit(json_encode(['code' => 201, 'msg' => '链接不能为空！'], 480));
}
$urls = cleanUrlParameters($urls);
$array = parse_url($urls);
if (empty($array)) {
    exit(json_encode(['code' => -1, 'msg' => "视频链接不正确"], 480));
} elseif ($array['host'] == 'b23.tv') {
    $header = get_headers($urls, true);
    // 修复点：处理可能返回数组的Location头
    $redirectUrl = is_array($header['Location']) ? end($header['Location']) : $header['Location'];
    $array = parse_url($redirectUrl);  // 现在确保传入字符串
    $bvid = rtrim($array['path'], '/');
} elseif ($array['host'] == 'www.bilibili.com') {
    $bvid = $array['path'];
} elseif ($array['host'] == 'm.bilibili.com') {
    $bvid = $array['path'];
} else {
    exit(json_encode(['code' => -1, 'msg' => "视频链接好像不太对！"], 480));
}
if (strpos($bvid, '/video/') === false) {
    exit(json_encode(['code' => -1, 'msg' => "好像不是视频链接"], 480));
}

$bvid = str_replace("/video/", "", $bvid);
if (!str_starts_with($bvid, 'BV')) {
    $bilibilihtml = getFinalHtmlContent($urls);
    $bvid = extractBilibiliBVId($bilibilihtml);
}
if (empty($bvid)) {
    echo json_encode(['code' => 404, 'msg' => "获取id失败"], 480);
    exit;
}

//获取解析需要的cid值和图片以及标题
$json1 = bilibili(
    'https://api.bilibili.com/x/web-interface/view?bvid=' . $bvid
    , $headers
    , $useragent
    , $cookie
);
$array = json_decode($json1, true);
if ($array['code'] == '0') {
    $title = $array['data']['title'];
    $cover = $array['data']['pic'];
    $desc = $array['data']['desc'];
    $owner = $array['data']['owner'];

    $videos = [];

    // 循环获取所有分P的视频信息
    foreach ($array['data']['pages'] as $index => $page) {
        // 请求视频直链API
        $apiUrl = "https://api.bilibili.com/x/player/playurl?otype=json&fnver=0&fnval=3&player=3&qn=112&bvid=" . $bvid . "&cid=" . $page['cid'] . "&platform=html5&high_quality=1";
        $jsonResponse = bilibili($apiUrl, $headers, $useragent, $cookie);

        // 解析API返回的JSON数据
        $videoInfo = json_decode($jsonResponse, true);

        // 检查API响应是否正常
        if (isset($videoInfo['data']['durl'][0]['url'])) {
            $videoUrl = $videoInfo['data']['durl'][0]['url'];

            // 提取真实视频地址（去除镜像前缀）
            $realVideoUrl = preg_replace('/^https?:\/\/[^\/]+\//', 'https://upos-sz-mirrorhw.bilivideo.com/', $videoUrl);

            $videos[] = [
                'title' => $page['part'],
                'duration' => $page['duration'],
                'durationFormat' => gmdate('H:i:s', $page['duration'] - 1),
                'url' => $realVideoUrl,
                'index' => $index + 1
            ];
        } else {
            // 记录获取失败的分P
            $videos[] = [
                'title' => $page['part'],
                'error' => '无法获取视频链接',
                'index' => $index + 1
            ];
        }
    }
    if ($index > 0) {
        // 构建最终返回的JSON数据
        $JSON = [
            'code' => 200,
            'msg' => '解析成功！',
            'data' => [
                'title' => $title,
                'cover' => $cover,
                'description' => $desc,
                'url' => $realVideoUrl ?? null,
                'user' => [
                    'name' => $owner['name'],
                    'avatar' => $owner['face']
                ],
                'videos' => $videos,
                'totalVideos' => count($videos)
            ]
        ];
    } else {
        $JSON = array(
            'code' => 200,
            'msg' => '解析成功！',
            'data' => array(
                'title' => $title,
                'cover' => $cover,
                'description' => $desc,
                'url' => $realVideoUrl ?? null,
                'user' => [
                    'name' => $owner['name'],
                    'avatar' => $owner['face']
                ])
        );
    }

} else {
    $JSON = ['code' => 0, 'msg' => "解析失败！"];
}
echo json_encode($JSON, 480);
/**
 * 获取网页代码，自动跟随重定向直到最终页面
 *
 * @param string $url 目标网页URL
 * @param int $timeout 超时时间（秒），默认30秒
 * @param int $maxRedirects 最大重定向次数，默认10次
 * @return string|false 成功返回HTML代码，失败返回false
 */
function getFinalHtmlContent($url, $timeout = 30, $maxRedirects = 10)
{
// 验证URL格式
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        trigger_error('无效的URL地址', E_USER_WARNING);
        return false;
    }

// 初始化curl
    $ch = curl_init();
    if (!$ch) {
        trigger_error('curl初始化失败', E_USER_WARNING);
        return false;
    }

// 设置curl选项
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,                      // 请求的URL
        CURLOPT_RETURNTRANSFER => true,           // 将响应内容以字符串返回，而不是直接输出
        CURLOPT_FOLLOWLOCATION => true,           // 启用跟随重定向
        CURLOPT_MAXREDIRS => $maxRedirects,       // 最大重定向次数
        CURLOPT_TIMEOUT => $timeout,              // 请求超时时间
        CURLOPT_CONNECTTIMEOUT => $timeout,       // 连接超时时间
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36', // 模拟浏览器UA
        CURLOPT_SSL_VERIFYPEER => false,          // 忽略SSL证书验证（如果目标网站是HTTPS）
        CURLOPT_SSL_VERIFYHOST => false,          // 忽略SSL主机验证
        CURLOPT_HEADER => false,                  // 不返回响应头
        CURLOPT_ENCODING => '',                   // 支持所有编码格式
    ]);

// 执行请求并获取响应内容
    $htmlContent = curl_exec($ch);

// 检查是否有错误发生
    if (curl_errno($ch)) {
        trigger_error('curl请求错误: ' . curl_error($ch), E_USER_WARNING);
        $htmlContent = false;
    }

// 获取最终的URL（确认是否重定向）
    $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    if ($finalUrl !== $url) {
// 可选：输出重定向信息，方便调试
// echo "原URL: $url -> 最终URL: $finalUrl\n";
    }

// 关闭curl资源
    curl_close($ch);

    return $htmlContent;
}

/**
 * 从B站视频页面的meta标签中提取BV号
 *
 * @param string $html 包含目标meta标签的HTML字符串
 * @return string|null 提取到的BV号，匹配失败返回null
 */
function extractBilibiliBVId($html)
{
    // 空值检查
    if (empty($html)) {
        echo "错误：输入的HTML字符串为空\n";
        return null;
    }

    // 正则表达式匹配规则：
    // 1. 匹配包含 itemprop="url" 的meta标签
    // 2. 捕获 content 属性中 https://www.bilibili.com/video/ 后的BV号
    // 3. BV号格式：以BV开头，后跟10位字母/数字组合（B站BV号固定12位：BV + 10位字符）
    $pattern = '/<meta\s+[^>]*itemprop="url"\s+[^>]*content="https:\/\/www\.bilibili\.com\/video\/(BV[a-zA-Z0-9]{10})\/?"[^>]*>/i';

    // 执行正则匹配
    $matchResult = preg_match($pattern, $html, $matches);

    // 检查匹配结果
    if ($matchResult === 1 && isset($matches[1])) {
        return $matches[1]; // 返回提取到的BV号
    } elseif ($matchResult === 0) {
        echo "提示：未匹配到B站BV号\n";
        return null;
    } else {
        echo "错误：正则表达式匹配失败 - " . preg_last_error_msg() . "\n";
        return null;
    }
}

function bilibili($url, $header, $user_agent, $cookie)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function cleanUrlParameters($url)
{
    // Step 1: 分解URL结构
    $parsed = parse_url($url);

    // Step 2: 构建基础组件（自动解码编码字符）
    $scheme = isset($parsed['scheme']) ? $parsed['scheme'] . '://' : '';
    $host = $parsed['host'] ?? '';
    $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
    $path = isset($parsed['path']) ? rawurldecode($parsed['path']) : '';
    $fragment = isset($parsed['fragment']) ? '#' . rawurldecode($parsed['fragment']) : '';

    // Step 3: 处理国际化域名（Punycode转中文）
    if (function_exists('idn_to_utf8') && preg_match('/^xn--/', $host)) {
        $host = idn_to_utf8($host, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
    }

    // Step 4: 移除认证信息（如 user:pass@）
    $host = preg_replace('/^.*@/', '', $host);

    // 去掉路径末尾的斜杠
    $path = rtrim($path, '/');

    // Step 5: 拼接最终URL
    return $scheme . $host . $port . $path . $fragment;
}

?>
