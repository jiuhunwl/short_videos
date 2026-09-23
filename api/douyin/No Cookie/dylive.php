<?php
/**
 * @Author: JH-Ahua
 * @CreateTime: 2025/6/17 下午5:00
 * @email: admin@bugpk.com
 * @blog: www.jiuhunwl.cn
 * @Api: api.bugpk.com
 * @tip: 抖音视频图集去水印解析
 */
header("Access-Control-Allow-Origin: *");
header('Content-type: application/json');
function douyin($url)
{

    // 构造请求数据
    $header = array(
        "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36",
        "Cookie: ttwid=1%7CdjUiwt-8iojVf89TbwdaPcsDLpn1fU00mKYaYCBRiHg%7C1710746734%7Ccd7960b547be86bd14c56832ffea3ec035af1704696960274f2ba4017cb0c420; bd_ticket_guard_client_web_domain=2; xgplayer_user_id=300976970825; odin_tt=cd1484c33777a5b6033eb2d704acf1325c6fa8f87f298761b34d502b2bc72e0e063bb76cafae6eda77504b92388a945495fa1bee99afaece54fadb48bd1e2eef65754e14fcd52875cf4e859f9f2797a1; xgplayer_device_id=33693820609; SEARCH_RESULT_LIST_TYPE=%22single%22; s_v_web_id=verify_lwllt9d5_131z6m2c_JOGv_4TDi_Aoje_kOMojbHCPw0e; passport_csrf_token=acfb568a5e849c00aae32c504ddcf720; passport_csrf_token_default=acfb568a5e849c00aae32c504ddcf720; UIFID_TEMP=c4683e1a43ffa6bc6852097c712d14b81f04bc9b5ca6d30214b0e66b4e3852802afe10dc759a4840b81140431eb63f5b7b9bf48388d5b2ea51d2c5499bf93eed4f464fc4a76e1d4f480f11523a92ed21; FORCE_LOGIN=%7B%22videoConsumedRemainSeconds%22%3A180%7D; fpk1=U2FsdGVkX1+zE2LbMIyeNz1bUAgXGI+GV9C9WyJchdXBQ+btbZOeBnttBI4FeWUjU8NDIweP6c2iFxNRAl9NzA==; fpk2=5f4591689f71924dbd1e95e47aec4ed7; UIFID=c4683e1a43ffa6bc6852097c712d14b81f04bc9b5ca6d30214b0e66b4e3852802afe10dc759a4840b81140431eb63f5b25c36f37f88bb35edf57e7b457b5f0552d48a4805370c354b88614ee3785e7a8d8360ba6238aea0fe85f7065584d0a57c40df70e202458dc7c81352a7d3040448ff6ed7106b36bc97733c48387da93953c97d5d7d7e128afc2d0497e2a51e4da5cae0c627ce32ce055c1b4e50a7c6b2f; vdg_s=1; pwa2=%220%7C0%7C3%7C0%22; download_guide=%223%2F20240702%2F1%22; douyin.com; device_web_cpu_core=12; device_web_memory_size=8; architecture=amd64; strategyABtestKey=%221719937555.264%22; csrf_session_id=6a4f4bf33581bf51380386b4904f13f7; __live_version__=%221.1.2.1533%22; live_use_vvc=%22false%22; webcast_leading_last_show_time=1719937582984; webcast_leading_total_show_times=1; webcast_local_quality=sd; xg_device_score=7.666140284295324; dy_swidth=1920; dy_sheight=1080; stream_recommend_feed_params=%22%7B%5C%22cookie_enabled%5C%22%3Atrue%2C%5C%22screen_width%5C%22%3A1920%2C%5C%22screen_height%5C%22%3A1080%2C%5C%22browser_online%5C%22%3Atrue%2C%5C%22cpu_core_num%5C%22%3A12%2C%5C%22device_memory%5C%22%3A8%2C%5C%22downlink%5C%22%3A10%2C%5C%22effective_type%5C%22%3A%5C%224g%5C%22%2C%5C%22round_trip_time%5C%22%3A50%7D%22; stream_player_status_params=%22%7B%5C%22is_auto_play%5C%22%3A0%2C%5C%22is_full_screen%5C%22%3A0%2C%5C%22is_full_webscreen%5C%22%3A0%2C%5C%22is_mute%5C%22%3A1%2C%5C%22is_speed%5C%22%3A1%2C%5C%22is_visible%5C%22%3A0%7D%22; WallpaperGuide=%7B%22showTime%22%3A1719918712666%2C%22closeTime%22%3A0%2C%22showCount%22%3A1%2C%22cursor1%22%3A35%2C%22cursor2%22%3A0%7D; live_can_add_dy_2_desktop=%221%22; msToken=wBlz-TD-Cxna5YP6Y4ev4-eiEy-vGNFvolT7yI6yCKrpljM0RfSXq2FE3zJSO3S19IL12WpOk-iQJCiau92GwBq0S2mK0PAxO0gIC4_EorlQk9_QAPsv; __ac_nonce=06684349d007e745bd7f4; __ac_signature=_02B4Z6wo00f01WoVPKAAAIDBXTH4.RkCqt1qNTgAADwF7SNYjgKYp2UYvulOkhbQ86-sAkiKejYGuMUddCSw4ObrljbN7dHpr-y5cdIiQpGVmJnE4aFoBhAVrazgiovkBqJ-ktLn2BQRGzSV1b; x-web-secsdk-uid=2e929dd5-0973-4520-846d-9417b0badc6f; home_can_add_dy_2_desktop=%221%22; IsDouyinActive=true; volume_info=%7B%22isUserMute%22%3Afalse%2C%22isMute%22%3Afalse%2C%22volume%22%3A0.943%7D; biz_trace_id=c3335c50; bd_ticket_guard_client_data=eyJiZC10aWNrZXQtZ3VhcmQtdmVyc2lvbiI6MiwiYmQtdGlja2V0LWd1YXJkLWl0ZXJhdGlvbi12ZXJzaW9uIjoxLCJiZC10aWNrZXQtZ3VhcmQtcmVlLXB1YmxpYy1rZXkiOiJCQXpEQjRsSlMvUndUZkg0RC9MN2RCTnduN1ZRdStjU0J1YUsvQTVzZ2YyamovaWlzakpVWWgzRDY0QUE4eit5Smx5T0hmOGF6aEFWWWhEbGhRbmE3Y0E9IiwiYmQtdGlja2V0LWd1YXJkLXdlYi12ZXJzaW9uIjoxfQ%3D%3D");
    // 尝试从 URL 中获取视频 ID
    $id = extractId($url);
    // 检查 ID 是否有效
    if (empty($id)) {
        return array('code' => 400, 'msg' => '无法解析视频 ID');
    }

    // 发送请求获取视频信息
    $response = curl('https://www.douyin.com/user/self?modal_id=' . $id . '&showTab=like', $header);

    $start_str = '<script id="RENDER_DATA" type="application/json">';
    $end_str = '</script>';

// 查找起始位置
    $pos_start = strpos($response, $start_str);
    if ($pos_start === false) {
        // 如果没找到起始位置，返回错误响应，这里调用create_standard_response函数，假设其已正确定义
        return ['code' => 404, 'msg' => '无法找到数据'];
    }

    // 获取起始位置之后的文本内容
    $json_str = substr($response, $pos_start + strlen($start_str));
    // 查找结束位置
    $pos_end = strpos($json_str, $end_str);
    if ($pos_end === false) {
        // 如果没找到结束位置，返回错误响应
        return ['code' => 404, 'msg' => '无法正确提取JSON数据，未找到结束标签'];
    }

// 截取中间的JSON字符串部分
    $json_str = substr($json_str, 0, $pos_end);
// 对URL编码的内容进行解码（类似Python的requests.utils.unquote）
    $json_str = urldecode($json_str);

// 将JSON字符串解析为PHP数组或对象（根据实际JSON结构和后续使用需求）
    $data = json_decode($json_str, true);
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        // 如果JSON解析失败，返回错误响应
        return ['code' => 404, 'msg' => 'JSON解析失败：' . json_last_error_msg()];
    }
    $videoDetail = $data['app']['videoDetail'];
    $imgjson = $videoDetail['images'];
    $images = [];
    $uri = $data['app']['videoDetail']['video']['uri'];
    if (isset($uri) && preg_match('/^[a-zA-Z0-9]+$/', $uri)) {
        $url = $videoDetail['video']['playApi'];
        if (empty($url)) {
            // 原画接口（与 cloudflare workers 版保持一致，唯一原画接口）【其实原画接口100个以上】
            $url = 'https://aweme.snssdk.com/aweme/v1/play/?video_id=' . $data['app']['videoDetail']['video']['uri'] . '&ratio=default&line=0';
//            $url = 'https://svproxy.168299.xyz/?proxyurl=' . base64_encode($url);
        }
    } else {
        $url = [];
        if (is_array($imgjson) && isset($imgjson[0])) {
            // 遍历 JSON 数组
            foreach ($imgjson as $item) {
                // 检查当前元素是否包含 url_list 标签
                if (isset($item['urlList']) && is_array($item['urlList']) && count($item['urlList']) > 0) {
                    // 将 url_list 的第一个值添加到 $imgurl 数组中
                    $images[] = $item['urlList'][0];
                    if (!empty($item['video']['playApi'])) {
                        $url[] = $item['video']['playApi'];
                    }
                }
            }
        }
    }

    $arr = array(
        'code' => 200,
        'msg' => '解析完成',
        'data' => [
            'auther' => $videoDetail['authorInfo']['nickname'],
            'uid' => $videoDetail['authorInfo']['uid'],
            'followerCount' => $videoDetail['authorInfo']['followerCount'],
            'totalFavorited' => $videoDetail['authorInfo']['totalFavorited'],
            'avatar' => $videoDetail['authorInfo']['avatarUri'],
            'title' => $videoDetail['desc'],
            'cover' => $videoDetail['video']['cover'],
            'images' => $images,
            'url' => $url,
            'music' => [
                'title' => $videoDetail['music']['musicName'] ?? null,
                'author' => $videoDetail['music']['ownerNickname'] ?? null,
                'avatar' => $videoDetail['music']['avatarThumb']['urlList'][0] ?? null,
                'url' => $videoDetail['video']['uri'] ?? $videoDetail['music']['playUrl']['uri'] ?? null,
            ],
        ]
    );
    return $arr;
}

function extractId($url)
{
    $headers = get_headers($url, true);
    if ($headers === false) {
        // 如果获取头信息失败，直接使用原始 URL
        $loc = $url;
    } else {
        // 处理重定向头可能是数组的情况
        if (isset($headers['Location']) && is_array($headers['Location'])) {
            $loc = end($headers['Location']);
        } else {
            $loc = $headers['Location'] ?? $url;
        }
    }

    // 确保 $loc 是字符串
    if (!is_string($loc)) {
        $loc = strval($loc);
    }

    preg_match('/[0-9]+|(?<=video\/)[0-9]+/', $loc, $id);
    return !empty($id) ? $id[0] : null;
}


function curl($url, $header = null, $data = null)
{
    $con = curl_init((string)$url);
    curl_setopt($con, CURLOPT_HEADER, false);
    curl_setopt($con, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($con, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($con, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($con, CURLOPT_AUTOREFERER, 1);
    if (isset($header)) {
        curl_setopt($con, CURLOPT_HTTPHEADER, $header);
    }
    if (isset($data)) {
        curl_setopt($con, CURLOPT_POST, true);
        curl_setopt($con, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($con, CURLOPT_TIMEOUT, 5000);
    $result = curl_exec($con);
    if ($result === false) {
        // 处理 curl 错误
        $error = curl_error($con);
        curl_close($con);
        trigger_error("cURL error: $error", E_USER_WARNING);
        return false;
    }
    curl_close($con);
    return $result;
}


// 使用空合并运算符检查 url 参数
$url = $_GET['url'] ?? '';
if (empty($url)) {
    echo json_encode(['code' => 201, 'msg' => 'url为空'], 480);
} else {
    $response = douyin($url);
    if (empty($response) && $response['code'] != 200) {
        echo json_encode(['code' => $response['code'], 'msg' => $response['msg']], 480);
    } else {
        echo json_encode($response, 480);
    }
}
?>