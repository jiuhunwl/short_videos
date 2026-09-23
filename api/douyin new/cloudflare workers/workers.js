/**
 *@Author: JH-Ahua
 *@CreateTime: 2026/4/14 10:41
 *@email: admin@bugpk.com
 *@blog: www.jiuhunwl.cn
 *@Api: api.bugpk.com
 *@tip: 抖音解析cloudflare workers版
 */
const DEFAULT_UA =
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36";

const REDIRECT_DONE_HOSTS = {
    "www.douyin.com": true,
};

const PLATFORM_HOSTS = {
    "www.douyin.com": true,
    "www.iesdouyin.com": true,
};

const CORS_HEADERS = {
    "access-control-allow-origin": "*",
    "access-control-allow-methods": "GET,POST,OPTIONS",
    "access-control-allow-headers": "Content-Type",
    "cache-control": "no-cache, no-store, must-revalidate",
    "content-type": "application/json; charset=utf-8",
};

// 原画播放地址模板：传入 vid 拼接后 302 即为原画直链
const ORIGINAL_PLAY_ENDPOINT = "https://aweme.snssdk.com/aweme/v1/play/";
// 批量转换 vid 时的并发数与单次上限（防止超出 Workers 子请求配额，超出的回退拼接地址）
const VID_RESOLVE_CONCURRENCY = 8;
const VID_RESOLVE_MAX_COUNT = 30;

export default {
    async fetch(request) {
        if (request.method === "OPTIONS") {
            return new Response(null, {status: 204, headers: CORS_HEADERS});
        }

        try {
            const input = await getInputUrl(request);
            if (!input) {
                return jsonResponse(400, "请输入抖音链接");
            }

            const result = await parseShareText(input);
            return new Response(JSON.stringify(result), {
                status: result.code >= 500 ? 500 : 200,
                headers: CORS_HEADERS,
            });
        } catch (error) {
            return new Response(
                JSON.stringify(output(500, `服务异常：${safeErrorMessage(error)}`)),
                {
                    status: 500,
                    headers: CORS_HEADERS,
                }
            );
        }
    },
};

async function getInputUrl(request) {
    const url = new URL(request.url);
    const fromQuery = url.searchParams.get("url");
    if (fromQuery && fromQuery.trim()) {
        return fromQuery.trim();
    }

    if (request.method === "POST") {
        const contentType = request.headers.get("content-type") || "";

        if (contentType.includes("application/json")) {
            const body = await request.json().catch(() => null);
            if (body && typeof body.url === "string" && body.url.trim()) {
                return body.url.trim();
            }
        }

        if (
            contentType.includes("application/x-www-form-urlencoded") ||
            contentType.includes("multipart/form-data")
        ) {
            const form = await request.formData().catch(() => null);
            const value = form?.get("url");
            if (typeof value === "string" && value.trim()) {
                return value.trim();
            }
        }

        if (contentType.startsWith("text/plain")) {
            const text = (await request.text().catch(() => "")).trim();
            if (text) {
                return text;
            }
        }
    }

    return "";
}

async function parseShareText(text) {
    const shareUrl = extractFirstUrl(text);
    if (!shareUrl) {
        return output(400, "未在文本中识别到有效链接");
    }

    const resolved = await followRedirectsToPlatform(shareUrl);
    if (!resolved) {
        return output(400, "无法解析重定向或链接不属于抖音");
    }

    const resolvedHost = host(resolved);
    if (!resolvedHost || !PLATFORM_HOSTS[resolvedHost]) {
        return output(400, "该链接不是抖音网页链接");
    }

    const realUrl = extractVideoAddress(resolved);
    const awemeId = getVideoId(realUrl);
    if (!awemeId) {
        return output(400, `链接格式错误，无法提取ID。处理后的链接: ${realUrl}`);
    }

    const detailResult = await fetchAwemeDetail(awemeId);
    if (!detailResult.ok) {
        const data = detailResult.reason ? {reason: detailResult.reason} : {};
        return output(500, detailResult.reason ? `请求失败（${detailResult.reason}）` : "请求失败", data);
    }

    const payload = await buildLegacyFormatData(detailResult.detail.aweme_detail, awemeId);
    const ok =
        (payload.type === "video" && payload.url) ||
        ((payload.type === "image" || payload.type === "live") &&
            Array.isArray(payload.images) &&
            payload.images.length > 0);

    if (!ok) {
        return output(404, "解析失败，未找到有效内容", []);
    }

    return output(200, "解析成功", payload);
}

function output(code, msg, data = []) {
    return {code, msg, data};
}

function jsonResponse(code, msg, data = []) {
    return new Response(JSON.stringify(output(code, msg, data)), {
        status: code >= 500 ? 500 : 200,
        headers: CORS_HEADERS,
    });
}

function safeErrorMessage(error) {
    if (error instanceof Error && error.message) {
        return error.message;
    }
    return String(error || "unknown");
}

function extractFirstUrl(text) {
    const match = text.match(
        /\bhttps?:\/\/(?:www\.|[-a-zA-Z0-9.@:%_+~#=]{1,256}\.[a-zA-Z0-9()]{1,6})\b(?:[-a-zA-Z0-9()@:%_+.~#?&/=]*)?/i
    );
    return match ? match[0] : null;
}

async function followRedirectsToPlatform(startUrl, max = 8) {
    let current = startUrl;
    for (let i = 0; i < max; i += 1) {
        const currentHost = host(current);
        if (currentHost && REDIRECT_DONE_HOSTS[currentHost]) {
            return current;
        }

        const next = await requestLocation(current);
        if (!next) {
            break;
        }
        current = next;
    }

    const finalHost = host(current);
    return finalHost && PLATFORM_HOSTS[finalHost] ? current : null;
}

async function requestLocation(url) {
    const response = await fetch(url, {
        method: "GET",
        redirect: "manual",
        headers: {
            "user-agent": DEFAULT_UA,
            accept: "text/html,application/xhtml+xml;q=0.9,*/*;q=0.8",
            "accept-language": "zh-CN,zh;q=0.9",
        },
    }).catch(() => null);

    if (!response || response.status < 300 || response.status >= 400) {
        return null;
    }

    const location = response.headers.get("location");
    if (!location) {
        return null;
    }

    try {
        return new URL(location, url).toString();
    } catch {
        return null;
    }
}

function buildOriginalPlayUrl(vid, ratio = "default") {
    if (!vid) {
        return null;
    }
    return `${ORIGINAL_PLAY_ENDPOINT}?video_id=${encodeURIComponent(String(vid))}&ratio=${ratio}&line=0`;
}

// 原画核心逻辑：传入视频 vid，请求原画播放端点，302 直出原画 CDN 直链
// 这个破原画接口竟然还有人卖四位数，真是穷疯了
async function resolveOriginalVideoUrl(vid, ratio = "default") {
    const fallbackUrl = buildOriginalPlayUrl(vid, ratio);
    if (!fallbackUrl) {
        return null;
    }

    let current = fallbackUrl;
    try {
        for (let i = 0; i < 3; i += 1) {
            const next = await requestLocation(current);
            if (!next) {
                break;
            }
            current = next;
        }
    } catch {
        return fallbackUrl;
    }

    return current === fallbackUrl ? fallbackUrl : toHttps(current) || fallbackUrl;
}

// 从 video 信息中提取 vid（优先 play_addr.uri，兼容 playAddr[0].uri 与 video.uri）
function extractVidFromVideoInfo(videoInfo) {
    if (!isObject(videoInfo)) {
        return null;
    }
    if (isObject(videoInfo.play_addr) && videoInfo.play_addr.uri) {
        return String(videoInfo.play_addr.uri);
    }
    if (Array.isArray(videoInfo.playAddr) && isObject(videoInfo.playAddr[0]) && videoInfo.playAddr[0].uri) {
        return String(videoInfo.playAddr[0].uri);
    }
    if (typeof videoInfo.uri === "string" && videoInfo.uri) {
        return videoInfo.uri;
    }
    return null;
}

// 批量转换 vid：去重 + 缓存 + 并发控制 + 数量上限，超出上限的 vid 不在结果中（由调用方回退拼接地址）
async function resolveVidBatch(vidList) {
    const uniqueVids = [...new Set(vidList.filter(Boolean))];
    const limitedVids = uniqueVids.slice(0, VID_RESOLVE_MAX_COUNT);
    const cache = new Map();
    if (!limitedVids.length) {
        return cache;
    }

    let index = 0;
    const workers = Array.from(
        {length: Math.min(VID_RESOLVE_CONCURRENCY, limitedVids.length)},
        async () => {
            while (index < limitedVids.length) {
                const vid = limitedVids[index];
                index += 1;
                cache.set(vid, await resolveOriginalVideoUrl(vid));
            }
        }
    );
    await Promise.all(workers);
    return cache;
}

function extractVideoAddress(url) {
    let parsed;
    try {
        parsed = new URL(url);
    } catch {
        return url;
    }

    const cleanPath = parsed.pathname.replace(/\/+$/, "");
    const normalized = `${parsed.protocol}//${parsed.host}${cleanPath}`;

    if (
        parsed.host === "www.douyin.com" ||
        parsed.host === "www.iesdouyin.com"
    ) {
        const modalId = parsed.searchParams.get("modal_id");
        if (modalId) {
            return `${normalized}?modal_id=${encodeURIComponent(modalId)}`;
        }
    }

    return normalized;
}

function getVideoId(url) {
    let parsed;
    try {
        parsed = new URL(url);
    } catch {
        return null;
    }

    for (const key of ["vid", "id", "modal_id", "v", "s", "pid"]) {
        const value = parsed.searchParams.get(key);
        if (value) {
            return value;
        }
    }

    const parts = parsed.pathname.split("/").filter(Boolean);
    let last = parts[parts.length - 1];
    if (!last) {
        return null;
    }
    if (last.endsWith(".html")) {
        last = last.slice(0, -5);
    }
    return last || null;
}

async function fetchAwemeDetail(awemeId) {
    const refererBase = `https://www.douyin.com/video/${awemeId}`;

    await httpGet(`${refererBase}?previous_page=web_code_link`, defaultBrowserHeaders(`${refererBase}?previous_page=web_code_link`)).catch(
        () => null
    );

    let lastFailureHint = "";

    for (let attempt = 0; attempt < 2; attempt += 1) {
        let ttwid = await getTtwid();
        if (!ttwid) {
            ttwid =
                "1%7CvDWCB8tYdKPbdOlqwNTkDPhizBaV9i91KjYLKJbqurg%7C1723536402%7C314e63000decb79f46b8ff255560b29f4d8c57352dad465b41977db4830b4c7e";
        }

        const msToken = randomMsToken(107);
        const search = new URLSearchParams({
            device_platform: "webapp",
            aid: "6383",
            channel: "channel_pc_web",
            aweme_id: awemeId,
            msToken,
        });
        const query = search.toString();
        const aBogus = generate_a_bogus(query, DEFAULT_UA);

        if (!aBogus) {
            return {ok: false, reason: "a_bogus 签名失败"};
        }

        const finalUrl = `https://www.douyin.com/aweme/v1/web/aweme/detail/?${query}&a_bogus=${encodeURIComponent(
            aBogus
        )}`;

        let body;
        try {
            body = await httpGet(finalUrl, [
                ...defaultBrowserHeaders(`${refererBase}?previous_page=web_code_link`),
                ["cookie", `ttwid=${ttwid}`],
            ]);
        } catch (error) {
            lastFailureHint = safeErrorMessage(error);
            continue;
        }

        let json;
        try {
            json = JSON.parse(body);
        } catch {
            lastFailureHint = "详情接口返回非 JSON（可能被 WAF 返回 HTML 或编码异常）";
            continue;
        }

        if (!json || !json.aweme_detail) {
            const apiMsg = json?.status_msg || json?.statusMsg || "";
            if (typeof apiMsg === "string" && apiMsg) {
                lastFailureHint = apiMsg;
            } else if (json && "status_code" in json) {
                lastFailureHint = `status_code=${json.status_code}，无 aweme_detail`;
            } else {
                lastFailureHint = "接口未返回 aweme_detail";
            }
            continue;
        }

        return {ok: true, detail: json};
    }

    return {ok: false, reason: lastFailureHint || "请求失败"};
}

function defaultBrowserHeaders(referer) {
    return [
        ["accept", "application/json, text/plain, */*"],
        ["accept-language", "zh-CN,zh;q=0.9,en;q=0.8"],
        ["referer", referer],
        ["user-agent", DEFAULT_UA],
        ["sec-ch-ua", '"Google Chrome";v="123", "Not:A-Brand";v="8", "Chromium";v="123"'],
        ["sec-ch-ua-mobile", "?0"],
        ["sec-ch-ua-platform", '"Windows"'],
        ["sec-fetch-dest", "empty"],
        ["sec-fetch-mode", "cors"],
        ["sec-fetch-site", "same-origin"],
    ];
}

async function getTtwid() {
    const response = await fetch("https://ttwid.bytedance.com/ttwid/union/register/", {
        method: "POST",
        headers: {
            "content-type": "application/json",
            "user-agent": DEFAULT_UA,
        },
        body: JSON.stringify({
            region: "cn",
            aid: 6383,
            need_t: 1,
            service: "www.douyin.com",
            migrate_priority: 0,
            cb_url_protocol: "https",
            domain: ".douyin.com",
        }),
    }).catch(() => null);

    if (!response) {
        return null;
    }

    const setCookie = response.headers.get("set-cookie") || "";
    const match = setCookie.match(/(?:^|,\s*)ttwid=([^;\s]+)/i);
    return match ? decodeURIComponent(match[1]) : null;
}

async function httpGet(url, headersList) {
    const headers = new Headers();
    for (const [key, value] of headersList) {
        headers.set(key, value);
    }

    const response = await fetch(url, {
        method: "GET",
        redirect: "follow",
        headers,
    });

    if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
    }

    return response.text();
}

async function buildLegacyFormatData(detail, fallbackVideoId) {
    const title = stringValue(detail?.desc);

    const authorArr = isObject(detail?.author) ? detail.author : {};
    const avatarThumb = isObject(authorArr.avatar_thumb) ? authorArr.avatar_thumb : {};
    const avatars = Array.isArray(avatarThumb.url_list) ? avatarThumb.url_list : [];
    const author = {
        name: stringValue(authorArr.nickname),
        id: stringValue(authorArr.uid || authorArr.unique_id || authorArr.short_id),
        avatar: stringValue(avatars[0]),
    };

    const music = isObject(detail?.music) ? detail.music : {};
    const musicPlay = isObject(music.play_url) ? music.play_url : {};
    const musicCoverSource = isObject(music.cover_thumb)
        ? music.cover_thumb
        : isObject(music.cover_thumb_medium)
            ? music.cover_thumb_medium
            : {};
    const musicOut = {
        title: stringValue(music.title || music.music_name),
        author: stringValue(music.author || music.owner_nickname),
        url: toHttps(stringValue(Array.isArray(musicPlay.url_list) ? musicPlay.url_list[0] : "")) || "",
        cover:
            toHttps(
                stringValue(
                    Array.isArray(musicCoverSource.url_list) ? musicCoverSource.url_list[0] : ""
                )
            ) || "",
    };

    const video = isObject(detail?.video) ? detail.video : null;
    // 契约要求秒：抖音原始 duration 为毫秒，>=1000 视为毫秒归一为秒
    let duration = video && Number.isFinite(Number(video.duration))
        ? Number(video.duration)
        : null;
    if (duration !== null && duration >= 1000) {
        duration = Math.round(duration / 1000 * 100) / 100;
    }

    const createTime = normalizeCreateTime(detail?.create_time ?? detail?.createTime);

    const result = {
        type: "unknown",
        title,
        desc: title,
        author,
        cover: "",
        url: null,
        quality: "",
        duration,
        size: 0,
        size_label: "",
        create_time: createTime,
        publish_time: formatPublishTime(createTime),
        video_backup: [],
        images: [],
        live_photo: [],
        music: musicOut,
        extra: {},
    };

    let images = Array.isArray(detail?.images) ? detail.images : [];
    if (!images.length && Array.isArray(detail?.image_list)) {
        images = detail.image_list;
    }

    if (images.length) {
        result.type = "image";
        const liveCandidates = [];
        for (const img of images) {
            if (!isObject(img)) {
                continue;
            }

            const imgUrl = pickImageListItemUrl(img);
            if (imgUrl) {
                result.images.push(imgUrl);
            }

            const videoInfo = isObject(img.video) ? img.video : {};
            let liveVideoUrl = extractLivePhotoVideoUrl(videoInfo);
            if (liveVideoUrl) {
                liveVideoUrl = toHttps(liveVideoUrl.replace(/playwm/g, "play")) || "";
            }
            if (imgUrl && liveVideoUrl) {
                liveCandidates.push({
                    image: imgUrl,
                    fallbackUrl: liveVideoUrl,
                    vid: extractVidFromVideoInfo(videoInfo),
                });
            }
        }

        // 多视频 vid 全部转 302 后的原画直链，拿不到的回退原地址
        const vidMap = await resolveVidBatch(liveCandidates.map((item) => item.vid));
        for (const item of liveCandidates) {
            const resolved = item.vid ? vidMap.get(item.vid) : null;
            result.live_photo.push({image: item.image, video: resolved || item.fallbackUrl});
        }

        if (result.live_photo.length) {
            result.type = "live";
        }
    } else {
        result.type = "video";
        const videoInfo = extractHighestQualityVideo(detail);

        let playUri = extractVidFromVideoInfo(video);
        let main = videoInfo.url;
        if (main) {
            main = toHttps(main.replace(/playwm/g, "play"));
        }

        // 原画优先：拿 vid 302 后的原画直链作为主地址；失败则回退原来的 main
        const resolvedOriginal = await resolveOriginalVideoUrl(playUri);
        if (resolvedOriginal) {
            if (main && main !== resolvedOriginal) {
                videoInfo.backup.unshift(main);
            }
            main = resolvedOriginal;
            // 只要主链接是原画 302 出来的，画质统一标 original
            result.quality = "original";
        } else if (videoInfo.gearName) {
            result.quality = videoInfo.gearName;
        }

        if (main) {
            result.url = main;
            result.size = await probeVideoSize(main);
            result.size_label = formatSizeLabel(result.size);
        }

        const backups = [];
        for (const candidate of videoInfo.backup) {
            const converted = toHttps(candidate.replace(/playwm/g, "play"));
            if (converted && converted !== result.url && !backups.includes(converted)) {
                backups.push(converted);
            }
        }
        result.video_backup = backups;

        result.video_id = playUri || fallbackVideoId;
    }

    const cover = pickCover(detail);
    result.cover = cover ? toHttps(cover) || "" : "";

    return result;
}

function pickImageListItemUrl(img) {
    let raw = "";
    if (Array.isArray(img.urlList) && typeof img.urlList[0] === "string") {
        raw = img.urlList[0];
    } else if (Array.isArray(img.url_list) && typeof img.url_list[0] === "string") {
        raw = img.url_list[0];
    } else if (Array.isArray(img.url_list) && img.url_list.length) {
        const last = img.url_list[img.url_list.length - 1];
        raw = typeof last === "string" ? last : "";
    }

    return raw ? toHttps(raw) : null;
}

function extractLivePhotoVideoUrl(videoInfo) {
    let liveVideoUrl = null;
    let v26Candidate = null;

    if (Array.isArray(videoInfo.playAddr)) {
        for (const addr of videoInfo.playAddr) {
            if (!isObject(addr) || typeof addr.src !== "string") {
                continue;
            }
            if (addr.src.includes("v3-web")) {
                liveVideoUrl = addr.src;
                break;
            }
            if (!v26Candidate && addr.src.includes("v26-web")) {
                v26Candidate = addr.src;
            }
        }

        if (!liveVideoUrl && v26Candidate) {
            liveVideoUrl = v26Candidate.replace(/:\/\/([^/]+)/, "://v26-luna.douyinvod.com");
        }

        if (!liveVideoUrl) {
            if (isObject(videoInfo.playAddr[1]) && typeof videoInfo.playAddr[1].src === "string") {
                liveVideoUrl = videoInfo.playAddr[1].src;
            } else if (isObject(videoInfo.playAddr[0]) && typeof videoInfo.playAddr[0].src === "string") {
                liveVideoUrl = videoInfo.playAddr[0].src;
            }
        }
    }

    if (!liveVideoUrl && isObject(videoInfo.play_addr) && Array.isArray(videoInfo.play_addr.url_list)) {
        let v26 = null;
        for (const candidate of videoInfo.play_addr.url_list) {
            if (typeof candidate !== "string") {
                continue;
            }
            if (candidate.includes("v3-web")) {
                liveVideoUrl = candidate;
                break;
            }
            if (!v26 && candidate.includes("v26-web")) {
                v26 = candidate;
            }
        }

        if (!liveVideoUrl && v26) {
            liveVideoUrl = v26.replace(/:\/\/([^/]+)/, "://v26-luna.douyinvod.com");
        }

        if (!liveVideoUrl) {
            if (typeof videoInfo.play_addr.url_list[1] === "string") {
                liveVideoUrl = videoInfo.play_addr.url_list[1];
            } else if (typeof videoInfo.play_addr.url_list[0] === "string") {
                liveVideoUrl = videoInfo.play_addr.url_list[0];
            }
        }
    }

    if (!liveVideoUrl && typeof videoInfo.playApi === "string" && videoInfo.playApi) {
        liveVideoUrl = videoInfo.playApi;
    }

    return liveVideoUrl || null;
}

function collectPlayUrlCandidatesFromBitRateItem(rateItem) {
    const candidates = [];

    if (Array.isArray(rateItem.playAddr)) {
        for (const item of rateItem.playAddr) {
            if (isObject(item) && typeof item.src === "string" && item.src) {
                candidates.push(item.src);
            }
        }
    }

    if (!candidates.length && isObject(rateItem.play_addr) && Array.isArray(rateItem.play_addr.url_list)) {
        for (const item of rateItem.play_addr.url_list) {
            if (typeof item === "string" && item) {
                candidates.push(item);
            }
        }
    }

    return candidates;
}

function extractHighestQualityVideo(detail) {
    const video = isObject(detail?.video) ? detail.video : {};
    let bitRateList = null;

    if (Array.isArray(video.bitRateList) && video.bitRateList.length) {
        bitRateList = [...video.bitRateList];
    } else if (Array.isArray(video.bit_rate) && video.bit_rate.length) {
        bitRateList = [...video.bit_rate];
    }

    let url = null;
    let gearName = "";
    const backup = [];

    if (bitRateList) {
        bitRateList.sort((a, b) => {
            const ba = isObject(a) ? Number(a.bitRate || a.bit_rate || 0) : 0;
            const bb = isObject(b) ? Number(b.bitRate || b.bit_rate || 0) : 0;
            return bb - ba;
        });

        for (const rateItem of bitRateList) {
            if (!isObject(rateItem)) {
                continue;
            }

            const candidates = collectPlayUrlCandidatesFromBitRateItem(rateItem);
            if (!candidates.length) {
                continue;
            }

            let v3Link = null;
            let v26Link = null;
            for (const candidate of candidates) {
                if (candidate.includes("v3-web")) {
                    v3Link = candidate;
                    break;
                }
                if (!v26Link && candidate.includes("v26-web")) {
                    v26Link = candidate;
                }
            }

            const currentBestUrl = v3Link
                ? v3Link
                : v26Link
                    ? v26Link.replace(/:\/\/([^/]+)/, "://v26-luna.douyinvod.com")
                    : candidates[0];

            if (!url) {
                url = currentBestUrl;
                gearName = stringValue(rateItem.gearName || rateItem.gear_name || "");
            }

            for (let candidate of candidates) {
                if (candidate.includes("v26-web")) {
                    candidate = candidate.replace(/:\/\/([^/]+)/, "://v26-luna.douyinvod.com");
                }
                if (candidate !== url && !backup.includes(candidate)) {
                    backup.push(candidate);
                }
            }

            if (url && backup.length) {
                break;
            }
        }
    }

    if (!url) {
        let uri = stringValue(video.uri);
        if (!uri && isObject(video.play_addr) && video.play_addr.uri) {
            uri = String(video.play_addr.uri);
        }

        let playApi =
            typeof video.playApi === "string" && video.playApi
                ? video.playApi
                : isObject(video.play_addr) && Array.isArray(video.play_addr.url_list) && typeof video.play_addr.url_list[0] === "string"
                    ? video.play_addr.url_list[0]
                    : "";

        if (playApi) {
            url = playApi.replace(/playwm/g, "play");
        } else if (uri) {
            // 原画兜底地址：buildLegacyFormatData 会用 vid 走 resolveOriginalVideoUrl 做 302 解析，失败回退此地址
            url = buildOriginalPlayUrl(uri);
        }

        const urlList = isObject(video.play_addr) && Array.isArray(video.play_addr.url_list)
            ? video.play_addr.url_list
            : [];
        if (urlList.length > 1) {
            urlList.forEach((link, index) => {
                if (index === 0 || typeof link !== "string") {
                    return;
                }
                const converted = link.replace(/playwm/g, "play");
                if (converted && converted !== url && !backup.includes(converted)) {
                    backup.push(converted);
                }
            });
        }
    }

    return {url, backup, gearName};
}

// 统一响应契约辅助：1024 进制 B/KB/MB/GB/TB，最多两位小数且不保留尾部 0
function formatSizeLabel(bytes) {
    const size = Number(bytes);
    if (!Number.isFinite(size) || size <= 0) {
        return "";
    }
    const units = ["B", "KB", "MB", "GB", "TB"];
    let value = size;
    let unit = 0;
    while (value >= 1024 && unit < units.length - 1) {
        value /= 1024;
        unit += 1;
    }
    if (unit === 0) {
        return `${Math.round(value)}B`;
    }
    const label = value.toFixed(2).replace(/\.?0+$/, "");
    return `${label}${units[unit]}`;
}

// 发布时间归一化：秒级直用，毫秒级（>=1e12）除一次 1000，非法/超范围归 0
function normalizeCreateTime(value) {
    let ts = Number(value);
    if (!Number.isFinite(ts) || ts <= 0) {
        return 0;
    }
    if (ts >= 1000000000000) {
        ts = Math.floor(ts / 1000);
    } else {
        ts = Math.floor(ts);
    }
    if (ts < 1000000000 || ts > 4102444800) {
        return 0;
    }
    return ts;
}

// 固定东八区格式化为 YYYY-MM-DD HH:mm:ss，未知返回空串
function formatPublishTime(createTime) {
    if (!createTime) {
        return "";
    }
    try {
        const formatter = new Intl.DateTimeFormat("zh-CN", {
            timeZone: "Asia/Shanghai",
            year: "numeric",
            month: "2-digit",
            day: "2-digit",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hour12: false,
        });
        const parts = formatter.formatToParts(new Date(createTime * 1000));
        const get = (type) => (parts.find((p) => p.type === type) || {}).value || "";
        return `${get("year")}-${get("month")}-${get("day")} ${get("hour")}:${get("minute")}:${get("second")}`;
    } catch {
        return "";
    }
}

// 有界探测主视频真实字节大小：单次 HEAD、不下载正文，拿不到一律 0（契约允许未知）
async function probeVideoSize(url) {
    if (!url) {
        return 0;
    }
    try {
        const response = await fetch(url, {
            method: "HEAD",
            headers: {"user-agent": DEFAULT_UA},
        });
        if (!response.ok) {
            return 0;
        }
        const contentType = (response.headers.get("content-type") || "").toLowerCase();
        if (!contentType.includes("video") && !contentType.includes("octet-stream")) {
            return 0;
        }
        const length = Number(response.headers.get("content-length"));
        return Number.isFinite(length) && length > 0 ? Math.floor(length) : 0;
    } catch {
        return 0;
    }
}

function pickCover(detail) {
    const video = isObject(detail?.video) ? detail.video : null;

    if (video) {
        if (Array.isArray(video.originCover?.urlList) && video.originCover.urlList[0]) {
            return String(video.originCover.urlList[0]);
        }
        if (Array.isArray(video.origin_cover?.url_list) && video.origin_cover.url_list[0]) {
            return String(video.origin_cover.url_list[0]);
        }
        if (typeof video.originCover === "string") {
            return video.originCover;
        }
        if (Array.isArray(video.originCoverUrlList) && video.originCoverUrlList[0]) {
            return String(video.originCoverUrlList[0]);
        }

        if (isObject(video.cover)) {
            const line = video.cover.urlList?.[0] || video.cover.url_list?.[0] || "";
            if (typeof line === "string" && line) {
                return line;
            }
        }

        if (typeof video.cover === "string") {
            return video.cover;
        }
    }

    if (Array.isArray(detail?.cover?.url_list) && detail.cover.url_list[0]) {
        return String(detail.cover.url_list[0]);
    }

    if (video) {
        if (Array.isArray(video.dynamicCover?.urlList) && video.dynamicCover.urlList[0]) {
            return String(video.dynamicCover.urlList[0]);
        }
        if (Array.isArray(video.dynamic_cover?.url_list) && video.dynamic_cover.url_list[0]) {
            return String(video.dynamic_cover.url_list[0]);
        }
    }

    if (detail?.videoInfoRes?.item_list?.[0]?.video?.cover?.url_list?.[0]) {
        return String(detail.videoInfoRes.item_list[0].video.cover.url_list[0]);
    }

    let images = Array.isArray(detail?.images) ? detail.images : [];
    if (!images.length && Array.isArray(detail?.image_list)) {
        images = detail.image_list;
    }
    if (images.length && isObject(images[0]) && Array.isArray(images[0].url_list) && images[0].url_list[0]) {
        return String(images[0].url_list[0]);
    }

    return null;
}

function randomMsToken(length) {
    const base = "ABCDEFGHIGKLMNOPQRSTUVWXYZabcdefghigklmnopqrstuvwxyz0123456789=";
    let out = "";
    for (let i = 0; i < length; i += 1) {
        out += base.charAt(Math.floor(Math.random() * base.length));
    }
    return out;
}

function toHttps(url) {
    if (!url) {
        return null;
    }
    return url.startsWith("http://") ? `https://${url.slice(7)}` : url;
}

function host(url) {
    try {
        return new URL(url).host.toLowerCase();
    } catch {
        return null;
    }
}

function isObject(value) {
    return value !== null && typeof value === "object" && !Array.isArray(value);
}

function stringValue(value) {
    return typeof value === "string" ? value : value == null ? "" : String(value);
}

function rc4_encrypt(plaintext, key) {
    const s = [];
    for (let i = 0; i < 256; i += 1) {
        s[i] = i;
    }
    let j = 0;
    for (let i = 0; i < 256; i += 1) {
        j = (j + s[i] + key.charCodeAt(i % key.length)) % 256;
        const temp = s[i];
        s[i] = s[j];
        s[j] = temp;
    }

    let i = 0;
    j = 0;
    const cipher = [];
    for (let k = 0; k < plaintext.length; k += 1) {
        i = (i + 1) % 256;
        j = (j + s[i]) % 256;
        const temp = s[i];
        s[i] = s[j];
        s[j] = temp;
        const t = (s[i] + s[j]) % 256;
        cipher.push(String.fromCharCode(s[t] ^ plaintext.charCodeAt(k)));
    }
    return cipher.join("");
}

function le(e, r) {
    return ((e << (r % 32)) | (e >>> (32 - (r % 32)))) >>> 0;
}

function de(e) {
    if (e >= 0 && e < 16) {
        return 2043430169;
    }
    if (e >= 16 && e < 64) {
        return 2055708042;
    }
    throw new Error("invalid j for constant Tj");
}

function pe(e, r, t, n) {
    if (e >= 0 && e < 16) {
        return (r ^ t ^ n) >>> 0;
    }
    if (e >= 16 && e < 64) {
        return ((r & t) | (r & n) | (t & n)) >>> 0;
    }
    throw new Error("invalid j for bool function FF");
}

function he(e, r, t, n) {
    if (e >= 0 && e < 16) {
        return (r ^ t ^ n) >>> 0;
    }
    if (e >= 16 && e < 64) {
        return ((r & t) | (~r & n)) >>> 0;
    }
    throw new Error("invalid j for bool function GG");
}

class SM3 {
    constructor() {
        this.reg = [];
        this.chunk = [];
        this.size = 0;
        this.reset();
    }

    reset() {
        this.reg[0] = 1937774191;
        this.reg[1] = 1226093241;
        this.reg[2] = 388252375;
        this.reg[3] = 3666478592;
        this.reg[4] = 2842636476;
        this.reg[5] = 372324522;
        this.reg[6] = 3817729613;
        this.reg[7] = 2969243214;
        this.chunk = [];
        this.size = 0;
    }

    write(input) {
        const bytes =
            typeof input === "string"
                ? Array.from(
                    encodeURIComponent(input).replace(/%([0-9A-F]{2})/g, (_, hex) =>
                        String.fromCharCode(Number(`0x${hex}`))
                    ),
                    (ch) => ch.charCodeAt(0)
                )
                : input;

        this.size += bytes.length;
        let free = 64 - this.chunk.length;

        if (bytes.length < free) {
            this.chunk = this.chunk.concat(bytes);
            return;
        }

        this.chunk = this.chunk.concat(bytes.slice(0, free));
        while (this.chunk.length >= 64) {
            this._compress(this.chunk);
            if (free < bytes.length) {
                this.chunk = bytes.slice(free, Math.min(free + 64, bytes.length));
            } else {
                this.chunk = [];
            }
            free += 64;
        }
    }

    sum(input, format) {
        if (input) {
            this.reset();
            this.write(input);
        }
        this._fill();

        for (let i = 0; i < this.chunk.length; i += 64) {
            this._compress(this.chunk.slice(i, i + 64));
        }

        let result;
        if (format === "hex") {
            result = "";
            for (let i = 0; i < 8; i += 1) {
                result += se(this.reg[i].toString(16), 8, "0");
            }
        } else {
            result = new Array(32);
            for (let i = 0; i < 8; i += 1) {
                let c = this.reg[i];
                result[4 * i + 3] = (c & 255) >>> 0;
                c >>>= 8;
                result[4 * i + 2] = (c & 255) >>> 0;
                c >>>= 8;
                result[4 * i + 1] = (c & 255) >>> 0;
                c >>>= 8;
                result[4 * i] = (c & 255) >>> 0;
            }
        }

        this.reset();
        return result;
    }

    _compress(t) {
        if (t.length < 64) {
            throw new Error("compress error: not enough data");
        }

        const w = new Array(132);
        for (let i = 0; i < 16; i += 1) {
            w[i] = (t[4 * i] << 24) | (t[4 * i + 1] << 16) | (t[4 * i + 2] << 8) | t[4 * i + 3];
            w[i] >>>= 0;
        }

        for (let i = 16; i < 68; i += 1) {
            let a = w[i - 16] ^ w[i - 9] ^ le(w[i - 3], 15);
            a = a ^ le(a, 15) ^ le(a, 23);
            w[i] = (a ^ le(w[i - 13], 7) ^ w[i - 6]) >>> 0;
        }

        for (let i = 0; i < 64; i += 1) {
            w[i + 68] = (w[i] ^ w[i + 4]) >>> 0;
        }

        const state = this.reg.slice(0);
        for (let i = 0; i < 64; i += 1) {
            let ss1 = le((((le(state[0], 12) + state[4] + le(de(i), i)) >>> 0) & 0xffffffff) >>> 0, 7);
            const ss2 = (ss1 ^ le(state[0], 12)) >>> 0;
            let tt1 = pe(i, state[0], state[1], state[2]);
            tt1 = (tt1 + state[3] + ss2 + w[i + 68]) >>> 0;
            let tt2 = he(i, state[4], state[5], state[6]);
            tt2 = (tt2 + state[7] + ss1 + w[i]) >>> 0;

            state[3] = state[2];
            state[2] = le(state[1], 9);
            state[1] = state[0];
            state[0] = tt1;
            state[7] = state[6];
            state[6] = le(state[5], 19);
            state[5] = state[4];
            state[4] = (tt2 ^ le(tt2, 9) ^ le(tt2, 17)) >>> 0;
        }

        for (let i = 0; i < 8; i += 1) {
            this.reg[i] = (this.reg[i] ^ state[i]) >>> 0;
        }
    }

    _fill() {
        const totalBits = 8 * this.size;
        let mod = this.chunk.push(128) % 64;

        if (64 - mod < 8) {
            mod -= 64;
        }
        while (mod < 56) {
            this.chunk.push(0);
            mod += 1;
        }

        for (let i = 0; i < 4; i += 1) {
            const high = Math.floor(totalBits / 4294967296);
            this.chunk.push((high >>> (8 * (3 - i))) & 255);
        }
        for (let i = 0; i < 4; i += 1) {
            this.chunk.push((totalBits >>> (8 * (3 - i))) & 255);
        }
    }
}

function se(value, width, fill) {
    let output = String(value);
    while (output.length < width) {
        output = fill + output;
    }
    return output;
}

function result_encrypt(long_str, num = null) {
    const s_obj = {
        s0: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
        s1: "Dkdpgh4ZKsQB80/Mfvw36XI1R25+WUAlEi7NLboqYTOPuzmFjJnryx9HVGcaStCe=",
        s2: "Dkdpgh4ZKsQB80/Mfvw36XI1R25-WUAlEi7NLboqYTOPuzmFjJnryx9HVGcaStCe=",
        s3: "ckdp1h4ZKsUB80/Mfvw36XIgR25+WQAlEi7NLboqYTOPuzmFjJnryx9HVGDaStCe",
        s4: "Dkdpgh2ZmsQB80/MfvV36XI1R45-WUAlEixNLwoqYTOPuzKFjJnry79HbGcaStCe",
    };

    const constant = {
        0: 16515072,
        1: 258048,
        2: 4032,
        str: s_obj[num],
    };

    let result = "";
    let round = 0;
    let longInt = get_long_int(round, long_str);
    for (let i = 0; i < (long_str.length / 3) * 4; i += 1) {
        if (Math.floor(i / 4) !== round) {
            round += 1;
            longInt = get_long_int(round, long_str);
        }

        const key = i % 4;
        let tempInt = 0;
        switch (key) {
            case 0:
                tempInt = (longInt & constant[0]) >> 18;
                result += constant.str.charAt(tempInt);
                break;
            case 1:
                tempInt = (longInt & constant[1]) >> 12;
                result += constant.str.charAt(tempInt);
                break;
            case 2:
                tempInt = (longInt & constant[2]) >> 6;
                result += constant.str.charAt(tempInt);
                break;
            case 3:
                tempInt = longInt & 63;
                result += constant.str.charAt(tempInt);
                break;
            default:
                break;
        }
    }
    return result;
}

function get_long_int(round, long_str) {
    const offset = round * 3;
    return (
        (long_str.charCodeAt(offset) << 16) |
        (long_str.charCodeAt(offset + 1) << 8) |
        long_str.charCodeAt(offset + 2)
    );
}

function gener_random(random, option) {
    return [
        ((random & 255 & 170) | (option[0] & 85)) >>> 0,
        ((random & 255 & 85) | (option[0] & 170)) >>> 0,
        (((random >> 8) & 255 & 170) | (option[1] & 85)) >>> 0,
        (((random >> 8) & 255 & 85) | (option[1] & 170)) >>> 0,
    ];
}

function generate_rc4_bb_str(
    url_search_params,
    user_agent,
    window_env_str,
    suffix = "cus",
    Arguments = [0, 1, 14]
) {
    const sm3 = new SM3();
    const start_time = Date.now();
    const url_search_params_list = sm3.sum(sm3.sum(url_search_params + suffix));
    const cus = sm3.sum(sm3.sum(suffix));
    const ua = sm3.sum(
        result_encrypt(
            rc4_encrypt(user_agent, String.fromCharCode.apply(null, [0.00390625, 1, 14])),
            "s3"
        )
    );
    const end_time = Date.now();
    const b = {
        8: 3,
        10: end_time,
        15: {
            aid: 6383,
            pageId: 6241,
            boe: false,
            ddrt: 7,
            paths: {
                include: [{}, {}, {}, {}, {}, {}, {}],
                exclude: [],
            },
            track: {
                mode: 0,
                delay: 300,
                paths: [],
            },
            dump: true,
            rpU: "",
        },
        16: start_time,
        18: 44,
        19: [1, 0, 1, 5],
    };

    b[20] = (b[16] >> 24) & 255;
    b[21] = (b[16] >> 16) & 255;
    b[22] = (b[16] >> 8) & 255;
    b[23] = b[16] & 255;
    b[24] = Math.floor(b[16] / 256 / 256 / 256 / 256);
    b[25] = Math.floor(b[16] / 256 / 256 / 256 / 256 / 256);

    b[26] = (Arguments[0] >> 24) & 255;
    b[27] = (Arguments[0] >> 16) & 255;
    b[28] = (Arguments[0] >> 8) & 255;
    b[29] = Arguments[0] & 255;

    b[30] = Math.floor(Arguments[1] / 256) & 255;
    b[31] = Arguments[1] % 256;
    b[32] = (Arguments[1] >> 24) & 255;
    b[33] = (Arguments[1] >> 16) & 255;

    b[34] = (Arguments[2] >> 24) & 255;
    b[35] = (Arguments[2] >> 16) & 255;
    b[36] = (Arguments[2] >> 8) & 255;
    b[37] = Arguments[2] & 255;

    b[38] = url_search_params_list[21];
    b[39] = url_search_params_list[22];
    b[40] = cus[21];
    b[41] = cus[22];
    b[42] = ua[23];
    b[43] = ua[24];

    b[44] = (b[10] >> 24) & 255;
    b[45] = (b[10] >> 16) & 255;
    b[46] = (b[10] >> 8) & 255;
    b[47] = b[10] & 255;
    b[48] = b[8];
    b[49] = Math.floor(b[10] / 256 / 256 / 256 / 256);
    b[50] = Math.floor(b[10] / 256 / 256 / 256 / 256 / 256);

    b[51] = b[15].pageId;
    b[52] = (b[15].pageId >> 24) & 255;
    b[53] = (b[15].pageId >> 16) & 255;
    b[54] = (b[15].pageId >> 8) & 255;
    b[55] = b[15].pageId & 255;

    b[56] = b[15].aid;
    b[57] = b[15].aid & 255;
    b[58] = (b[15].aid >> 8) & 255;
    b[59] = (b[15].aid >> 16) & 255;
    b[60] = (b[15].aid >> 24) & 255;

    const window_env_list = [];
    for (let i = 0; i < window_env_str.length; i += 1) {
        window_env_list.push(window_env_str.charCodeAt(i));
    }
    b[64] = window_env_list.length;
    b[65] = b[64] & 255;
    b[66] = (b[64] >> 8) & 255;

    b[69] = 0;
    b[70] = 0;
    b[71] = 0;

    b[72] =
        b[18] ^
        b[20] ^
        b[26] ^
        b[30] ^
        b[38] ^
        b[40] ^
        b[42] ^
        b[21] ^
        b[27] ^
        b[31] ^
        b[35] ^
        b[39] ^
        b[41] ^
        b[43] ^
        b[22] ^
        b[28] ^
        b[32] ^
        b[36] ^
        b[23] ^
        b[29] ^
        b[33] ^
        b[37] ^
        b[44] ^
        b[45] ^
        b[46] ^
        b[47] ^
        b[48] ^
        b[49] ^
        b[50] ^
        b[24] ^
        b[25] ^
        b[52] ^
        b[53] ^
        b[54] ^
        b[55] ^
        b[57] ^
        b[58] ^
        b[59] ^
        b[60] ^
        b[65] ^
        b[66] ^
        b[70] ^
        b[71];

    let bb = [
        b[18], b[20], b[52], b[26], b[30], b[34], b[58], b[38], b[40], b[53], b[42], b[21],
        b[27], b[54], b[55], b[31], b[35], b[57], b[39], b[41], b[43], b[22], b[28], b[32],
        b[60], b[36], b[23], b[29], b[33], b[37], b[44], b[45], b[59], b[46], b[47], b[48],
        b[49], b[50], b[24], b[25], b[65], b[66], b[70], b[71],
    ];

    bb = bb.concat(window_env_list).concat(b[72]);
    return rc4_encrypt(String.fromCharCode.apply(null, bb), String.fromCharCode.apply(null, [121]));
}

function generate_random_str() {
    let random_str_list = [];
    random_str_list = random_str_list.concat(gener_random(Math.random() * 10000, [3, 45]));
    random_str_list = random_str_list.concat(gener_random(Math.random() * 10000, [1, 0]));
    random_str_list = random_str_list.concat(gener_random(Math.random() * 10000, [1, 5]));
    return String.fromCharCode.apply(null, random_str_list);
}

function generate_a_bogus(url_search_params, user_agent) {
    const result_str =
        generate_random_str() +
        generate_rc4_bb_str(
            url_search_params,
            user_agent,
            "1536|747|1536|834|0|30|0|0|1536|834|1536|864|1525|747|24|24|Win32"
        );
    return `${result_encrypt(result_str, "s4")}=`;
}
