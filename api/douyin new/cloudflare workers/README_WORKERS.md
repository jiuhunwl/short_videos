# Douyin Parser (Cloudflare Workers) / 抖音解析（Cloudflare Workers）

## 简介（中文）

这是一个基于 Cloudflare Workers 的抖音链接解析接口实现，对齐本项目 `douyinnew/workers.js` 的逻辑。

它做的事情：

- 接收用户传入的 `url`（可以是抖音分享文案/短链/网页链接）
- 从文本中提取第一个 URL
- 跟随短链跳转，归一到抖音网页域名
- 提取 `aweme_id`
- 获取 `ttwid`
- 生成 `a_bogus`
- 调用抖音 `aweme/detail` 接口
- 将结果整理为统一 JSON：`{ code, msg, data }`

注意：本 Workers 版本**不包含**任何第三方接口/远程镜像兜底逻辑，只保留原始解析主链路。

---

## Overview (English)

This is a Douyin (TikTok CN) URL parser API implemented on Cloudflare Workers, matching the logic in
`douyinnew/workers.js`.

What it does:

- Accepts a user-provided `url` (share text / short link / web link)
- Extracts the first URL from the text
- Follows redirects and normalizes to Douyin web domains
- Extracts `aweme_id`
- Fetches `ttwid`
- Generates `a_bogus`
- Calls Douyin `aweme/detail`
- Outputs a unified JSON response: `{ code, msg, data }`

Note: This Workers version **does not** include any third-party fallback/proxy logic. Only the original core parsing
flow is kept.

---

## 接口说明 / API

### 请求 / Request

- **Method**: `GET` / `POST` / `OPTIONS`
- **Query Param**: `url` (required)
- **CORS**: `Access-Control-Allow-Origin: *`

Workers 会按以下优先级读取 `url`：

1. `GET ?url=...`
2. `POST application/json`：`{ "url": "..." }`
3. `POST application/x-www-form-urlencoded` 或 `multipart/form-data`：字段 `url=...`
4. `POST text/plain`：直接把 body 当作文本（会从中提取第一个 URL）

The Worker reads `url` in the following order:

1. `GET ?url=...`
2. `POST application/json`: `{ "url": "..." }`
3. `POST application/x-www-form-urlencoded` or `multipart/form-data`: field `url=...`
4. `POST text/plain`: treat the body as text and extract the first URL

---

## 使用示例 / Examples

将下面的 `<WORKER_URL>` 替换成你的 Workers 访问地址。

Replace `<WORKER_URL>` with your deployed Worker URL.

### 1) GET 方式 / GET

```bash
curl "<WORKER_URL>?url=https%3A%2F%2Fv.douyin.com%2Fxxxxxx%2F"
```

### 2) POST JSON / POST JSON

```bash
curl -X POST "<WORKER_URL>" \
  -H "Content-Type: application/json" \
  -d "{\"url\":\"https://v.douyin.com/xxxxxx/\"}"
```

### 3) POST 表单 / POST form

```bash
curl -X POST "<WORKER_URL>" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  --data-urlencode "url=https://v.douyin.com/xxxxxx/"
```

### 4) POST 纯文本（分享文案）/ POST plain text (share text)

```bash
curl -X POST "<WORKER_URL>" \
  -H "Content-Type: text/plain; charset=utf-8" \
  --data "复制此链接，打开抖音查看： https://v.douyin.com/xxxxxx/ "
```

---

## 响应格式 / Response format

### 统一结构 / Unified envelope

所有请求都会返回 JSON：

```json
{
  "code": 200,
  "msg": "解析成功",
  "data": {}
}
```

- **code**:
    - `200`: success
    - `400`: invalid input / not a valid Douyin link
    - `404`: parsed but no playable content found
    - `500`: request/signing/runtime failure
- **msg**: human-readable message
- **data**: payload (empty array/object on failure)

### data 字段（成功时）/ `data` fields (on success)

`data` 会尽量对齐项目现有 PHP 版返回结构，常见字段：

- **type**: `"video"` / `"image"` / `"live"` / `"unknown"`
- **title**: 标题（一般取 `desc`）
- **desc**: 同 `title`
- **author**:
    - `name`: 作者昵称
    - `id`: 作者 id（uid/unique_id/short_id）
    - `avatar`: 头像
- **cover**: 封面 URL
- **quality**: 画质标记（见下方「原画说明」）
- **music**:
    - `title`
    - `author`
    - `url`
    - `cover`
- **duration**: 时长（秒，毫秒已归一；可能为 `null`）
- **size**: 主视频真实字节大小（HEAD 探测，拿不到为 `0`）
- **size_label**: 由 `size` 派生的标签（如 `2.62MB`；`size=0` 时为空串）
- **create_time**: 作品发布时间（Unix 秒；毫秒自动归一；未知为 `0`）
- **publish_time**: 与 `create_time` 同一刻，固定东八区 `YYYY-MM-DD HH:mm:ss`；未知为空串
- **extra**: 预留扩展对象（当前为 `{}`）

当 `type=video`：

- **url**: 主视频直链（已尝试将 `playwm` 替换为 `play`）
- **video_backup**: 备选直链数组
- **video_id**: 视频 id（或 play_addr.uri / fallback）

当 `type=image`：

- **images**: 图片 URL 数组

当 `type=live`（实况图/动态照片）：

- **images**: 图片 URL 数组
- **live_photo**: 实况数组，每项：
    - `image`
    - `video`

---

## 原画说明 / Original quality

本项目使用**唯一原画接口**【其实原画接口100个以上】：

```
https://aweme.snssdk.com/aweme/v1/play/?video_id={vid}&ratio=default&line=0
```

This project uses **the single original-quality endpoint** (in fact there are 100+ original-quality endpoints out there):

解析流程 / How it works:

1. 从详情数据提取视频 `vid`（`play_addr.uri` 等）
2. 请求上述原画端点，跟随 302 重定向，取得原画 CDN 直链（`douyinvod.com` 等）
3. **任一环节失败（无重定向/网络异常）自动回退**拼接的原画地址或原主链接，不影响解析成功

1. Extract the video `vid` from the detail data (e.g. `play_addr.uri`)
2. Request the endpoint above and follow the 302 redirect to get the original-quality CDN URL
3. **On any failure it automatically falls back** to the constructed URL or the previous main link, so parsing never breaks

画质标记 / Quality label:

- 只要主视频链接是原画 302 出来的，`data.quality` 统一标记为 **`"original"`**
- 非 302 主链接（bit_rate 最高档）则标记对应档位 `gear_name`，未知为 `""`
- 图集/实况类型按契约 `quality` 为 `""`

- If the main video URL comes from the original-quality 302 resolution, `data.quality` is set to **`"original"`**
- Otherwise it carries the selected `gear_name` from `bit_rate` (empty when unknown)
- For `image`/`live` types, `quality` is `""` per the unified contract

多视频 / Multiple videos:

- 实况图集（live_photo）中的**每个实况视频**都会做同样的 vid → 302 原画转换
- 批量转换带去重缓存、并发限制与单次上限（30 个），超出上限或失败的条目回退原地址

- Every live-photo video is resolved the same way (vid → 302)
- Batch conversion applies dedup cache, concurrency limits and a per-request cap (30); items beyond the cap or failing fall back to their original URLs

---

## 部署说明 / Deployment

本仓库只提供 `workers.js` 单文件实现，你可以用以下方式部署：

- **Cloudflare Dashboard**: Workers & Pages → Workers → Create → 复制粘贴 `douyinnew/workers.js` 内容并保存部署
- **Wrangler**: 将 `workers.js` 作为入口文件部署（按你项目的 Wrangler 配置为准）

This repo provides a single-file `workers.js`. You can deploy via:

- **Cloudflare Dashboard**: Workers → Create → paste `douyinnew/workers.js` and deploy
- **Wrangler**: deploy using `workers.js` as the entry file (depending on your Wrangler setup)

---

## 常见问题 / FAQ

### 1) 为什么会返回 400？

可能原因：

- `url` 参数为空
- 传入文本里没有识别到 `http/https` 链接
- 短链跳转后不属于抖音网页域名（`www.douyin.com` / `www.iesdouyin.com`）

### 2) 为什么会返回 500？

可能原因：

- `ttwid` 获取失败或抖音风控导致接口返回异常
- `aweme/detail` 返回 HTML（WAF/风控）导致 JSON 解析失败
- 目标接口非 200

### 3) code=404 是什么情况？

表示已拿到详情结构，但没有组装出可播放的视频直链或有效图片列表。

### 4) 有没有兜底/代理？

没有。`douyinnew/workers.js` 明确移除了远程 douyin.php 镜像等兜底逻辑，只保留原始主解析链路。

---

## 文件位置 / File

- `douyinnew/workers.js`: Cloudflare Workers 入口与全部逻辑
- `douyinnew/README_WORKERS.md`: 本文档

