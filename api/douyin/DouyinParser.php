<?php
/**
 * @Author: JH-Ahua
 * @CreateTime: 2026/2/12 下午9:47
 * @email: admin@bugpk.com
 * @blog: www.jiuhunwl.cn
 * @Api: api.bugpk.com
 * @tip: 整合视频、图文、图集、实况解析
 */

class DouyinParser
{
    private $headers;
    private $cookie;
    private $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    /**
     * 原画播放接口（与 cloudflare workers 版保持一致，唯一原画接口）【其实原画接口100个以上】
     */
    private $originalPlayEndpoint = 'https://aweme.snssdk.com/aweme/v1/play/';

    /**
     * 批量转换 vid 的单次上限（与 workers 版一致，超出的回退拼接地址）
     */
    private $vidResolveMaxCount = 30;

    public function __construct()
    {
        $this->headers = [
            'User-Agent: ' . $this->userAgent,
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
        ];
        // 默认 Cookie，可通过 setCookie 方法覆盖
        $this->cookie = "";
    }

    /**
     * 设置Cookie
     */
    public function setCookie($cookie)
    {
        $this->cookie = $cookie;
    }

    /**
     * 统一输出函数
     */
    private function output($code, $msg, $data = [])
    {
        return json_encode([
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ], 480);
    }

    /**
     * 发送HTTP请求
     */
    private function request($url, $customHeaders = [], $returnHeader = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $headers = array_merge($this->headers, $customHeaders);
        if ($this->cookie) {
            curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($returnHeader) {
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
        }

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return false;
        }
        return $response;
    }

    /**
     * 获取重定向后的真实链接
     */
    private function getRealUrl($url)
    {
        // 方案一：优先使用 get_headers
        stream_context_set_default([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: " . $this->userAgent
            ]
        ]);

        $headers = @get_headers($url, 1);

        if (isset($headers['Location'])) {
            $location = $headers['Location'];
            if (is_array($location)) {
                // 优先寻找包含 video/note/modal_id 等特征的链接
                foreach ($location as $loc) {
                    if ($this->extractId($loc)) {
                        return $loc;
                    }
                }
                return $location[0];
            }
            return $location;
        }

        // 方案二：cURL 备选
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_exec($ch);
        $realUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        return $realUrl ?: $url;
    }

    /**
     * 拼接原画播放地址（唯一原画接口）【其实原画接口100个以上】
     */
    private function buildOriginalPlayUrl($vid, $ratio = 'default')
    {
        if (empty($vid)) {
            return null;
        }
        return $this->originalPlayEndpoint . '?video_id=' . rawurlencode((string)$vid) . '&ratio=' . $ratio . '&line=0';
    }

    /**
     * 单次请求，仅跟踪 302 Location（最多 $max 跳），无重定向返回 null
     */
    private function followLocation($url, $max = 3)
    {
        $current = $url;
        $redirected = false;

        for ($i = 0; $i < $max; $i++) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $current,
                CURLOPT_USERAGENT => $this->userAgent,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_HEADER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ]);
            $response = curl_exec($ch);
            $error = curl_error($ch);
            $headerSize = (int)curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($error || $response === false || $httpCode < 300 || $httpCode >= 400) {
                break;
            }

            $headerText = substr($response, 0, $headerSize);
            if (!preg_match('/^location:\s*(\S+)/mi', $headerText, $matches)) {
                break;
            }

            $location = trim($matches[1]);
            // 相对地址转绝对
            if (strpos($location, 'http') !== 0) {
                $parsed = parse_url($current);
                if (!empty($parsed['scheme']) && !empty($parsed['host'])) {
                    $location = $parsed['scheme'] . '://' . $parsed['host']
                        . ($location[0] === '/' ? '' : '/') . $location;
                }
            }

            $current = $location;
            $redirected = true;
        }

        return $redirected ? $current : null;
    }

    /**
     * 传入视频 vid，拿 302 后的原画直链；任一环节失败则回退拼接的原画地址
     * 这个破原画接口竟然还有人卖四位数，真是穷疯了
     * （与 cloudflare workers 版 resolveOriginalVideoUrl 逻辑一致）
     */
    private function resolveOriginalVideoUrl($vid, $ratio = 'default')
    {
        $fallbackUrl = $this->buildOriginalPlayUrl($vid, $ratio);
        if (!$fallbackUrl) {
            return null;
        }

        $resolved = $this->followLocation($fallbackUrl, 3);
        return $resolved ?: $fallbackUrl;
    }

    /**
     * 从 video 信息中提取 vid（优先 play_addr.uri，兼容 playAddr[0].uri 与 video.uri）
     */
    private function extractVidFromVideoInfo($videoInfo)
    {
        if (!is_array($videoInfo)) {
            return null;
        }
        if (!empty($videoInfo['play_addr']['uri'])) {
            return (string)$videoInfo['play_addr']['uri'];
        }
        if (!empty($videoInfo['playAddr'][0]['uri'])) {
            return (string)$videoInfo['playAddr'][0]['uri'];
        }
        if (!empty($videoInfo['uri']) && is_string($videoInfo['uri'])) {
            return $videoInfo['uri'];
        }
        return null;
    }

    /**
     * 批量转换 vid：去重 + 缓存 + 数量上限（超出的 vid 不在结果中，由调用方回退拼接地址）
     */
    private function resolveVidBatch($vidList)
    {
        $cache = [];
        $unique = array_values(array_unique(array_filter($vidList)));
        $limited = array_slice($unique, 0, $this->vidResolveMaxCount);

        foreach ($limited as $vid) {
            $cache[$vid] = $this->resolveOriginalVideoUrl($vid);
        }

        return $cache;
    }

    /**
     * 有界探测主视频真实字节大小：单次 HEAD、不下载正文，拿不到一律 0（契约允许未知）
     */
    private function probeVideoSize($url)
    {
        if (empty($url)) {
            return 0;
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => $this->userAgent,
            CURLOPT_NOBODY => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        curl_exec($ch);
        $error = curl_error($ch);
        $contentType = strtolower((string)curl_getinfo($ch, CURLINFO_CONTENT_TYPE));
        $length = (int)curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD_T);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error || $httpCode < 200 || $httpCode >= 300) {
            return 0;
        }
        if (strpos($contentType, 'video') === false && strpos($contentType, 'octet-stream') === false) {
            return 0;
        }
        return $length > 0 ? $length : 0;
    }

    /**
     * 统一响应契约辅助：1024 进制 B/KB/MB/GB/TB，最多两位小数且不保留尾部 0
     */
    private function formatSizeLabel($bytes)
    {
        if (!is_numeric($bytes) || $bytes <= 0) {
            return '';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $value = (float)$bytes;
        $unit = 0;
        while ($value >= 1024 && $unit < count($units) - 1) {
            $value /= 1024;
            $unit++;
        }
        if ($unit === 0) {
            return round($value) . 'B';
        }
        $label = rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
        return $label . $units[$unit];
    }

    /**
     * 发布时间归一化：秒级直用，毫秒级（>=1e12）除一次 1000，非法/超范围归 0
     */
    private function normalizeCreateTime($value)
    {
        if (!is_numeric($value) || $value <= 0) {
            return 0;
        }
        $ts = (float)$value;
        if ($ts >= 1000000000000) {
            $ts = floor($ts / 1000);
        } else {
            $ts = floor($ts);
        }
        $ts = (int)$ts;
        if ($ts < 1000000000 || $ts > 4102444800) {
            return 0;
        }
        return $ts;
    }

    /**
     * 固定东八区格式化为 YYYY-MM-DD HH:mm:ss，未知返回空串
     */
    private function formatPublishTime($createTime)
    {
        if (empty($createTime)) {
            return '';
        }
        try {
            $dt = new DateTime('@' . $createTime);
            $dt->setTimezone(new DateTimeZone('Asia/Shanghai'));
            return $dt->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * 提取ID
     */
    private function extractId($url)
    {
        // 匹配 URL 中的数字 ID (通常是 video/xxx 或 modal_id=xxx)
        if (preg_match('/\/video\/(\d+)/', $url, $matches)) {
            return $matches[1];
        }
        if (preg_match('/modal_id=(\d+)/', $url, $matches)) {
            return $matches[1];
        }
        if (preg_match('/note\/(\d+)/', $url, $matches)) {
            return $matches[1];
        }
        // 尝试匹配纯数字 (防止某些短链解开后直接是ID)
        if (preg_match('/^(\d+)$/', $url, $matches)) {
            return $matches[1];
        }
        if (preg_match('/note\/(\d+)/', $url, $matches)) {
            return $matches[1];
        }
        // 匹配 share/slides/xxx (新增)
        if (preg_match('/\/share\/slides\/(\d+)/', $url, $matches)) {
            return $matches[1];
        }
        // 匹配 share/video/xxx (新增)
        if (preg_match('/\/share\/video\/(\d+)/', $url, $matches)) {
            return $matches[1];
        }
        // 尝试匹配纯数字 (防止某些短链解开后直接是ID)
        return null;
    }

    /**
     * 主解析方法
     */
    public function parse($url)
    {
        if (empty($url)) {
            return $this->output(400, '请输入抖音链接');
        }

        // 预处理域名
        $domain = parse_url($url, PHP_URL_HOST);
        // 如果是短链接域名或不包含 video/modal_id 等特征，尝试获取重定向链接
        if ($domain == 'v.douyin.com' || strpos($url, 'douyin.com') === false || !$this->extractId($url)) {
            $url = $this->getRealUrl($url);
        }

        $id = $this->extractId($url);
        if (!$id) {
            return $this->output(400, '链接格式错误，无法提取ID。处理后的链接: ' . $url);
        }

        // 使用 dylive.php 中的 API 接口方式获取数据 (通常比页面解析更稳定)
        // 注意：这里需要有效的 Cookie
        $apiUrl = 'https://www.douyin.com/user/self?modal_id=' . $id . '&showTab=like';
        $response = $this->request($apiUrl);
        if (!$response) {
            return $this->output(500, '请求失败');
        }

        $data = $this->extractJson($response);
        if ($data) {
            return $this->formatData($data);
        }

        return $this->output(404, '解析失败，未找到有效内容');
    }

    /**
     * 提取并解析 JSON 数据
     */
    private function extractJson($html)
    {
        $startStr = '<script id="RENDER_DATA" type="application/json">';
        $endStr = '</script>';

        $posStart = strpos($html, $startStr);
        if ($posStart === false) {
            // 尝试另一种模式 (douyin.php 中的模式)
            $pattern = '/window\._ROUTER_DATA\s*=\s*(.*?)\<\/script>/s';
            if (preg_match($pattern, $html, $matches)) {
                $json = json_decode($matches[1], true);
                if (isset($json['loaderData'])) {
                    // 需要根据 loaderData 结构提取 videoDetail
                    // 这里的 key 可能是动态的，如 video_(id)/page
                    foreach ($json['loaderData'] as $key => $value) {
                        if (strpos($key, 'video_') === 0 && isset($value['videoInfoRes']['item_list'][0])) {
                            return $value['videoInfoRes']['item_list'][0];
                        }
                    }
                }
            }
            return null;
        }

        $jsonStr = substr($html, $posStart + strlen($startStr));
        $posEnd = strpos($jsonStr, $endStr);
        if ($posEnd === false) {
            return null;
        }

        $jsonStr = substr($jsonStr, 0, $posEnd);
        $jsonStr = urldecode($jsonStr); // 抖音 RENDER_DATA 通常经过 URL 编码
        $data = json_decode($jsonStr, true);

        if (isset($data['app']['videoDetail'])) {
            return $data['app']['videoDetail'];
        }

        return null;
    }

    /**
     * 格式化数据 (统一为小红书格式)
     */
    private function formatData($detail)
    {
        // 契约要求秒：抖音原始 duration 为毫秒，>=1000 视为毫秒归一为秒
        $duration = $detail['video']['duration'] ?? null;
        if (is_numeric($duration) && $duration >= 1000) {
            $duration = round($duration / 1000, 2);
        }
        $createTime = $this->normalizeCreateTime($detail['create_time'] ?? ($detail['createTime'] ?? 0));

        $result = [
            'type' => 'unknown',
            'title' => $detail['desc'] ?? '',
            'desc' => $detail['desc'] ?? '',
            'author' => [
                'name' => $detail['authorInfo']['nickname'] ?? ($detail['author']['nickname'] ?? ''),
                'id' => $detail['authorInfo']['uid'] ?? ($detail['author']['uid'] ?? ''),
                'avatar' => $detail['authorInfo']['avatarUri'] ?? ($detail['author']['avatar_thumb']['url_list'][0] ?? ''),
            ],
            'cover' => '',
            'url' => null, // 视频链接
            'quality' => '',
            'duration' => $duration,
            'size' => 0,
            'size_label' => '',
            'create_time' => $createTime,
            'publish_time' => $this->formatPublishTime($createTime),
            'video_backup' => null,
            'images' => [],
            'live_photo' => [],
            'music' => [
                'title' => $detail['music']['musicName'] ?? ($detail['music']['title'] ?? ''),
                'author' => $detail['music']['ownerNickname'] ?? ($detail['music']['author'] ?? ''),
                'url' => $detail['music']['playUrl']['uri'] ?? ($detail['music']['play_url']['uri'] ?? ''),
                'cover' => $detail['music']['coverThumb']['urlList'][0] ?? ($detail['music']['cover_thumb']['url_list'][0] ?? '')
            ],
            'extra' => (object)[]
        ];

        // 提取封面 (尝试多种字段)
        $cover = '';
        // 1. 尝试 originCover (原图封面)
        if (isset($detail['video']['originCover']['urlList'][0])) {
            $cover = $detail['video']['originCover']['urlList'][0];
        } elseif (isset($detail['video']['origin_cover']['url_list'][0])) {
            $cover = $detail['video']['origin_cover']['url_list'][0];
        } elseif (isset($detail['video']['originCover'])) {
            $cover = $detail['video']['originCover'];
        } elseif (isset($detail['video']['originCoverUrlList'][0])) {
            $cover = $detail['video']['originCoverUrlList'][0];
        }

        // 2. 尝试 cover (普通封面)
        if (!$cover) {
            // 某些情况下结构可能是 cover.url_list，也可能是 cover.urlList
            $cover = $detail['video']['cover']['urlList'][0] ?? ($detail['video']['cover']['url_list'][0] ?? '');

            // 如果 cover 是字符串 (直接是 URL)
            if (!$cover && isset($detail['video']['cover']) && is_string($detail['video']['cover'])) {
                $cover = $detail['video']['cover'];
            }
        }

        // 补充：检查是否直接在 detail.cover 字段 (某些图文类型)
        if (!$cover && isset($detail['cover']['url_list'][0])) {
            $cover = $detail['cover']['url_list'][0];
        }

        // 3. 尝试 dynamicCover (动态封面)
        if (!$cover) {
            $cover = $detail['video']['dynamicCover']['urlList'][0] ?? ($detail['video']['dynamic_cover']['url_list'][0] ?? '');
        }

        // 4. 尝试 douyin.php 中的路径逻辑 (针对 loaderData/videoInfoRes 结构)
        if (!$cover && isset($detail['videoInfoRes']['item_list'][0]['video']['cover']['url_list'][0])) {
            $cover = $detail['videoInfoRes']['item_list'][0]['video']['cover']['url_list'][0];
        }

        $result['cover'] = $cover;

        // 判断类型和提取资源
        $images = $detail['images'] ?? [];
        if (!empty($images)) {
            // 图文/图集/实况
            $result['type'] = 'image';
            $liveCandidates = [];

            foreach ($images as $img) {
                // 提取图片 URL
                $imgUrl = $img['urlList'][0] ?? ($img['url_list'][0] ?? '');
                if ($imgUrl) {
                    $result['images'][] = $imgUrl;
                }

                // 提取实况视频 (Live Photo)
                // 抖音实况通常在 images 列表的 item 中包含 video 字段 (与普通图文不同)
                $liveVideoUrl = null;
                $videoInfo = $img['video'] ?? [];

                // 1. 尝试 playAddr (对象数组结构，如 dylive.json)
                if (isset($videoInfo['playAddr']) && is_array($videoInfo['playAddr'])) {
                    $liveVideoUrl = null;
                    $v26Candidate = null;
                    // 优先匹配包含 v3-web 的链接
                    foreach ($videoInfo['playAddr'] as $addr) {
                        if (isset($addr['src'])) {
                            if (strpos($addr['src'], 'v3-web') !== false) {
                                $liveVideoUrl = $addr['src'];
                                break;
                            }
                            if (strpos($addr['src'], 'v26-web') !== false) {
                                $v26Candidate = $addr['src'];
                            }
                        }
                    }

                    if (!$liveVideoUrl && $v26Candidate) {
                        $liveVideoUrl = preg_replace('/:\/\/([^\/]+)/', '://v26-luna.douyinvod.com', $v26Candidate);
                    }

                    // 没找到 v3-web，则回退到备用逻辑 (优先取第二个，没有则第一个)
                    if (!$liveVideoUrl) {
                        $liveVideoUrl = $videoInfo['playAddr'][1]['src'] ?? ($videoInfo['playAddr'][0]['src'] ?? null);
                    }
                }

                // 2. 尝试 play_addr.url_list (字符串数组结构)
                if (!$liveVideoUrl && isset($videoInfo['play_addr']['url_list'])) {
                    $urlList = $videoInfo['play_addr']['url_list'];
                    $v26Candidate = null;
                    // 优先匹配包含 v3-web 的链接
                    foreach ($urlList as $url) {
                        if (strpos($url, 'v3-web') !== false) {
                            $liveVideoUrl = $url;
                            break;
                        }
                        if (strpos($url, 'v26-web') !== false) {
                            $v26Candidate = $url;
                        }
                    }

                    if (!$liveVideoUrl && $v26Candidate) {
                        $liveVideoUrl = preg_replace('/:\/\/([^\/]+)/', '://v26-luna.douyinvod.com', $v26Candidate);
                    }

                    // 没找到 v3-web，则回退到备用逻辑
                    if (!$liveVideoUrl) {
                        $liveVideoUrl = $urlList[1] ?? ($urlList[0] ?? null);
                    }
                }

                // 3. 尝试 playApi
                if (!$liveVideoUrl) {
                    $liveVideoUrl = $videoInfo['playApi'] ?? null;
                }

                if ($liveVideoUrl) {
                    $liveVideoUrl = str_replace('playwm', 'play', $liveVideoUrl);
                    if ($imgUrl) {
                        $liveCandidates[] = [
                            'image' => $imgUrl,
                            'fallbackUrl' => $liveVideoUrl,
                            'vid' => $this->extractVidFromVideoInfo($videoInfo),
                        ];
                    }
                }
            }

            // 多视频 vid 全部转 302 后的原画直链，拿不到的回退原地址
            $vidMap = $this->resolveVidBatch(array_column($liveCandidates, 'vid'));
            foreach ($liveCandidates as $item) {
                $resolved = ($item['vid'] && isset($vidMap[$item['vid']])) ? $vidMap[$item['vid']] : null;
                $result['live_photo'][] = [
                    'image' => $item['image'],
                    'video' => $resolved ?: $item['fallbackUrl'],
                ];
            }

            // 如果提取到了实况视频，修正类型为实况
            if (!empty($result['live_photo'])) {
                $result['type'] = 'live';
            }
        } else {
            // 视频
            $result['type'] = 'video';

            // 使用新逻辑提取最高画质视频
            $videoInfo = $this->extractHighestQualityVideo($detail);

            $playUri = $this->extractVidFromVideoInfo($detail['video'] ?? null);
            $main = $videoInfo['url'];
            if ($main) {
                $main = str_replace('playwm', 'play', $main);
            }

            // 原画优先：拿 vid 302 后的原画直链作为主地址；失败则回退原来的 main
            $resolvedOriginal = $this->resolveOriginalVideoUrl($playUri);
            if ($resolvedOriginal) {
                if ($main && $main !== $resolvedOriginal) {
                    array_unshift($videoInfo['backup'], $main);
                }
                $main = $resolvedOriginal;
                // 只要主链接是原画 302 出来的，画质统一标 original
                $result['quality'] = 'original';
            } elseif (!empty($videoInfo['gearName'])) {
                $result['quality'] = $videoInfo['gearName'];
            }

            $backups = [];
            foreach ($videoInfo['backup'] as $candidate) {
                $converted = str_replace('playwm', 'play', $candidate);
                if ($converted && $converted !== $main && !in_array($converted, $backups)) {
                    $backups[] = $converted;
                }
            }

            $result['url'] = $main;
            if ($main) {
                $result['size'] = $this->probeVideoSize($main);
                $result['size_label'] = $this->formatSizeLabel($result['size']);
            }
            $result['video_backup'] = $backups;
            $result['video_id'] = $playUri ?: ($detail['video']['uri'] ?? '');

            // 【合并】多档清晰度选项（原画/高清/标清等）
            $result['video_options'] = $this->extractVideoOptions($detail);
        }

        return $this->output(200, '解析成功', $result);
    }

    /**
     * 提取最高画质视频链接
     */
    private function extractHighestQualityVideo($detail)
    {
        $url = null;
        $gearName = '';
        $backup = [];

        // 尝试从 bitRateList 中提取
        if (isset($detail['video']['bitRateList']) && is_array($detail['video']['bitRateList'])) {
            $bitRateList = $detail['video']['bitRateList'];

            // 按 bitRate 降序排序
            usort($bitRateList, function ($a, $b) {
                return ($b['bitRate'] ?? 0) - ($a['bitRate'] ?? 0);
            });

            // 遍历寻找合适的链接
            foreach ($bitRateList as $rateItem) {
                $playAddr = $rateItem['playAddr'][0]['src'] ?? ($rateItem['play_addr']['url_list'][0] ?? null);
                if ($playAddr) {
                    // 检查是否包含 v3-web 域名 (通常更稳定)
                    // 如果 playAddr 是数组，尝试找到 v3-web 的链接
                    $candidates = [];
                    if (isset($rateItem['playAddr']) && is_array($rateItem['playAddr'])) {
                        foreach ($rateItem['playAddr'] as $pa) {
                            if (isset($pa['src'])) $candidates[] = $pa['src'];
                        }
                    } elseif (isset($rateItem['play_addr']['url_list'])) {
                        $candidates = $rateItem['play_addr']['url_list'];
                    }

                    if (empty($candidates)) continue;

                    // 1. 在当前画质中选择最佳 URL
                    $currentBestUrl = null;
                    $v3Link = null;
                    $v26Link = null;

                    foreach ($candidates as $candidate) {
                        if (strpos($candidate, 'v3-web') !== false) {
                            $v3Link = $candidate;
                            break; // 找到 v3 优先使用
                        }
                        if (strpos($candidate, 'v26-web') !== false) {
                            $v26Link = $candidate;
                        }
                    }

                    if ($v3Link) {
                        $currentBestUrl = $v3Link;
                    } elseif ($v26Link) {
                        $currentBestUrl = preg_replace('/:\/\/([^\/]+)/', '://v26-luna.douyinvod.com', $v26Link);
                    } else {
                        $currentBestUrl = $candidates[0];
                    }

                    // 2. 如果全局 URL 尚未设置，使用当前最佳
                    if (!$url) {
                        $url = $currentBestUrl;
                        $gearName = (string)($rateItem['gearName'] ?? ($rateItem['gear_name'] ?? ''));
                    }

                    // 3. 将所有非主 URL 的链接加入备用
                    foreach ($candidates as $candidate) {
                        // 如果是 v26 链接，也进行域名替换，保持一致性
                        if (strpos($candidate, 'v26-web') !== false) {
                            $candidate = preg_replace('/:\/\/([^\/]+)/', '://v26-luna.douyinvod.com', $candidate);
                        }

                        // 排除已选用的主 URL
                        if ($candidate !== $url && !in_array($candidate, $backup)) {
                            $backup[] = $candidate;
                        }
                    }
                }

                if ($url && !empty($backup)) break; // 找到主备链接后停止
            }
        }

        // 如果 bitRateList 没找到，尝试旧逻辑
        if (!$url) {
            $uri = $detail['video']['uri'] ?? '';
            $playApi = $detail['video']['playApi'] ?? ($detail['video']['play_addr']['url_list'][0] ?? '');

            if ($playApi) {
                $url = str_replace('playwm', 'play', $playApi);
            } elseif ($uri) {
                // 原画兜底地址：formatData 会用 vid 走 resolveOriginalVideoUrl 做 302 解析，失败回退此地址
                $url = $this->buildOriginalPlayUrl($uri);
            }

            // 备用
            $urlList = $detail['video']['play_addr']['url_list'] ?? [];
            if (count($urlList) > 1) {
                foreach ($urlList as $index => $link) {
                    if ($index === 0) continue;
                    $backup[] = str_replace('playwm', 'play', $link);
                }
            }
        }

        return ['url' => $url, 'backup' => $backup];
    }

    /**
     * 【合并新增】提取多档清晰度视频选项（原画/高清/标清/流畅等）
     *
     * 说明：抖音 bitRateList 自带各档清晰度信息（qualityType/gearName/bitRate/size），
     * 这里把每一档都提取出来并带可读名称，供前端做"清晰度选择"。
     * 数据结构随抖音改版可能变化，字段取不到时按码率兜底生成名称。
     */
    private function extractVideoOptions($detail)
    {
        $options = [];

        if (!isset($detail['video']['bitRateList']) || !is_array($detail['video']['bitRateList'])) {
            return $options; // 无多档数据，返回空数组
        }

        foreach ($detail['video']['bitRateList'] as $rateItem) {
            // 当前档位的清晰度信息
            $quality  = $rateItem['qualityType'] ?? null;   // 数值档位
            $gearName = $rateItem['gearName'] ?? '';         // 档位名称（如"原画""高清"）
            $bitRate  = $rateItem['bitRate'] ?? 0;           // 码率
            $size     = $rateItem['size'] ?? null;           // 文件大小（字节）

            // 提取当前档位的可用播放地址
            $candidates = [];
            if (isset($rateItem['playAddr']) && is_array($rateItem['playAddr'])) {
                foreach ($rateItem['playAddr'] as $pa) {
                    if (isset($pa['src'])) $candidates[] = $pa['src'];
                }
            } elseif (isset($rateItem['play_addr']['url_list'])) {
                $candidates = $rateItem['play_addr']['url_list'];
            }
            if (empty($candidates)) continue;

            // 优先 v3-web，其次 v26-web（替换域名），最后兜底
            $bestUrl = null;
            $v26Link = null;
            foreach ($candidates as $candidate) {
                if (strpos($candidate, 'v3-web') !== false) { $bestUrl = $candidate; break; }
                if (strpos($candidate, 'v26-web') !== false) $v26Link = $candidate;
            }
            if (!$bestUrl && $v26Link) {
                $bestUrl = preg_replace('/:\/\/([^\/]+)/', '://v26-luna.douyinvod.com', $v26Link);
            }
            if (!$bestUrl) $bestUrl = $candidates[0];

            // 清晰度名称兜底（没有 gearName 时按码率给可读标签）
            if (!$gearName) {
                if ($bitRate >= 3000000)      $gearName = '原画/超清';
                elseif ($bitRate >= 1500000)  $gearName = '高清';
                elseif ($bitRate >= 800000)   $gearName = '标清';
                else                          $gearName = '流畅';
            }

            $options[] = [
                'quality' => $quality,
                'name'    => $gearName,
                'bitrate' => $bitRate,
                'size'    => $size,
                'url'     => $bestUrl,
            ];
        }

        // 按码率从高到低排序（原画在前）
        usort($options, function ($a, $b) {
            return ($b['bitrate'] ?? 0) - ($a['bitrate'] ?? 0);
        });

        return $options;
    }
}
