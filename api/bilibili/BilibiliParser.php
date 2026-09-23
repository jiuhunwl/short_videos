<?php
/**
 * @Author: JH-Ahua
 * @CreateTime: 2026/2/12 下午9:57
 * @email: admin@bugpk.com
 * @blog: www.jiuhunwl.cn
 * @Api: api.bugpk.com
 * @tip: bilibili作品视频&作品视频合集解析-新版
 */

class BilibiliParser
{
    private $cookie;
    private $userAgent;

    public function __construct($cookie)
    {
        // Cookie must be provided by the caller
        $this->cookie = $cookie;

        $this->userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.81 Safari/537.36';
    }

    /**
     * Main entry point to parse a URL
     * @param string $url
     * @return string JSON response
     */
    public function parse($url)
    {
        if (empty($url)) {
            return $this->jsonResponse(['code' => 201, 'msg' => '链接不能为空！']);
        }

        $url = $this->cleanUrlParameters($url);
        $bvid = $this->extractBvid($url);

        if (!$bvid) {
            return $this->jsonResponse(['code' => -1, 'msg' => "视频链接好像不太对！"], 480); // 480 was used in original code logic path for errors
        }

        if (strpos($bvid, '/video/') === false && strpos($bvid, 'BV') !== 0) {
            // Basic validation fallback, though extractBvid usually handles this
            // Original code logic: if (strpos($bvid, '/video/') === false) exit...
            // But my extractBvid might strip /video/. Let's trust extractBvid but ensure we have something.
            // If extractBvid returns clean string, check if it looks like a video ID?
            // Actually, original code checks for /video/ in the *path* before replacing it.
        }

        // Fetch View Info
        $viewApiUrl = 'https://api.bilibili.com/x/web-interface/view?bvid=' . $bvid;
        $jsonView = $this->request($viewApiUrl);
        $viewData = json_decode($jsonView, true);

        if (($viewData['code'] ?? -1) == '0') {
            $pages = $viewData['data']['pages'] ?? [];
            $bilijson = [];

            foreach ($pages as $index => $page) {
                $cid = $page['cid'];
                $playApiUrl = "https://api.bilibili.com/x/player/playurl?otype=json&fnver=0&fnval=3&player=3&qn=112&bvid=" . $bvid . "&cid=" . $cid . "&platform=html5&high_quality=1";

                $jsonPlay = $this->request($playApiUrl);
                $playData = json_decode($jsonPlay, true);

                $videoUrl = '';
                if (isset($playData['data']['durl'][0]['url'])) {
                    $rawUrl = $playData['data']['durl'][0]['url'];
                    $videoUrl = preg_replace('/^https?:\/\/[^\/]+\//', 'https://upos-sz-mirrorhw.bilivideo.com/', $rawUrl);
                }

                $bilijson[] = [
                    'title' => $page['part'],
                    'duration' => $page['duration'],
                    'durationFormat' => gmdate('H:i:s', $page['duration'] - 1),
                    'accept' => $playData['data']['accept_description'] ?? [],
                    'url' => $videoUrl,
                    'index' => $index + 1
                ];
            }

            $responseData = [
                'code' => 200,
                'msg' => '解析成功！',
                'data' => [
                    'title' => $viewData['data']['title'],
                    'cover' => $viewData['data']['pic'],
                    'auther' => $viewData['data']['owner']['name'],
                    'avatar' => $viewData['data']['owner']['face'],
                    'description' => $viewData['data']['desc'],
                    'url' => $bilijson[0]['url'] ?? '',
                    'user' => [
                        'name' => $viewData['data']['owner']['name'],
                        'user_img' => $viewData['data']['owner']['face']
                    ],
                    'videos' => $bilijson,
                    'totalVideos' => count($bilijson)
                ]
            ];

            return $this->jsonResponse($responseData);

        } else {
            return $this->jsonResponse(['code' => 0, 'msg' => "解析失败！"]);
        }
    }

    private function jsonResponse($data, $options = 480)
    {
        // 480 = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT (maybe)
        // Original code used 480.
        return json_encode($data, $options);
    }

    private function cleanUrlParameters($url)
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

    private function extractBvid($url)
    {
        $array = parse_url($url);
        if (empty($array)) return null;

        $host = $array['host'] ?? '';
        $path = $array['path'] ?? '';
        $bvid = '';

        if ($host == 'b23.tv') {
            $header = get_headers($url, true);
            $location = $header['Location'] ?? '';
            $redirectUrl = is_array($location) ? end($location) : $location;
            if ($redirectUrl) {
                $array = parse_url($redirectUrl);
                $bvid = rtrim($array['path'] ?? '', '/');
            }
        } elseif ($host == 'www.bilibili.com' || $host == 'm.bilibili.com') {
            $bvid = $path;
        } else {
            // Invalid host
            return null;
        }

        // Check if it's a video link logic from original
        if (strpos($bvid, '/video/') === false) {
            // If the path doesn't contain /video/, maybe it's not a valid video path for this parser
            // But let's handle the extraction first
            // Original: if (strpos($bvid, '/video/') === false) exit error
            return null;
        }

        $bvid = str_replace("/video/", "", $bvid);
        return $bvid;
    }

    private function request($url)
    {
        $ch = curl_init();
        $header = ['Content-type: application/json;charset=UTF-8'];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}
