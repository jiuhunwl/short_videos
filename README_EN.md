<div align="center">
  <img width="100px" alt="logo" src="img/icon.png"/>
  <h1>Short Video Watermark Removal & Parsing API</h1>
  <p><em>Multi-platform parsing · Cookie-free · Watermark-free direct links · Ready to deploy</em></p>
  <div>
    <a href="https://github.com/jiuhunwl/short_videos/blob/main/LICENSE">
      <img src="https://img.shields.io/github/license/jiuhunwl/short_videos" alt="License" />
    </a>
    <a href="https://php.net">
      <img src="https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=flat&logo=php&logoColor=white" alt="PHP Version" />
    </a>
    <a href="https://github.com/jiuhunwl/short_videos">
      <img src="https://img.shields.io/github/stars/jiuhunwl/short_videos?style=social" alt="GitHub Stars" />
    </a>
  </div>
  <br/>
  <p>
    <a href="#-about">About</a> •
    <a href="#-important-notice">Important Notice</a> •
    <a href="#-features">Features</a> •
    <a href="#-supported-platforms">Platforms</a> •
    <a href="#-project-structure">Structure</a> •
    <a href="#-installation">Installation</a> •
    <a href="#-usage">Usage</a> •
    <a href="#-api-reference">API Reference</a> •
    <a href="#-contributing">Contributing</a> •
    <a href="#-contact">Contact</a>
  </p>
</div>

<div align="center">
  <p><small>Sponsored Advertisement</small></p>
  <a href="https://www.hncloud.com/" target="_blank" rel="noopener noreferrer">
    <img src="img/hncloud_ad.jpg" alt="Hncloud - Visit Official Website" width="728" />
  </a>
  <p><a href="https://www.hncloud.com/" target="_blank" rel="noopener noreferrer">Hncloud | Visit Official Website →</a></p>
</div>

---

## 🎯 About

This tool parses share links from major short video platforms and returns watermark-free direct links with full metadata:

- **Watermark-free video / image-gallery / live-photo direct links** (Douyin supports original-quality `original` parsing)
- **Original images** from galleries and image+video pairs for Live Photos
- Cover images, author info, background music, duration, publish time and more
- All responses are returned in structured JSON format for easy integration

> Douyin parsing uses a **cookie-free detail API** — no login, no cookie extraction required. Videos, galleries and live photos are all supported.

---

## 📢 Important Notice

This project is open-source software under the MIT License. Any individual or organization may use, modify, and
distribute the source code in compliance with the license terms and applicable laws and regulations.

This project is provided for technical research and API demonstration purposes only. **The code is for learning,
communication, and technical testing only, and must not be used for any illegal activities.**

All rights and interests related to parsed content (including but not limited to videos, image collections, music,
and other media assets) belong to the original platforms and rightful owners/authors.

Users are solely responsible for obtaining necessary permissions and ensuring lawful, compliant, and reasonable use
of any parsed content. Any direct or indirect consequences arising from the use of this project shall be borne by the
user.

If any rights holder believes there is infringement or improper use, please contact us and we will cooperate promptly.

---

## ✨ Features

- **Multi-Platform Support**: Douyin / Kuaishou / Xiaohongshu / Bilibili / Weibo / Toutiao and more
- **Cookie-Free Parsing**: Douyin videos, galleries and live photos require no login or cookies
- **Original Quality**: Douyin resolves original quality via vid 302 redirect (`quality=original`) with automatic bitrate fallback
- **Normalized Direct URLs**: Douyin video URLs are automatically given `mime_type=video_mp4` (never duplicated) so players and downloaders recognize the payload as MP4
- **All Content Types**: Videos, image galleries and live photos share one unified response contract
- **Dual Runtime**: PHP API + Cloudflare Workers edition (Douyin) — pick whichever fits your deployment
- **Aggregated Entry**: `api/sv.php` single aggregate endpoint — auto-detects the platform and dispatches to the matching per-platform API
- **Zero-Dependency Deployment**: Pure PHP, no Composer required — upload and run

---

## 🌐 Supported Platforms

> Douyin content parsing ships as **two integrated editions only** — the PHP API and the Cloudflare Workers edition (videos / galleries / live photos behind one entry). Home-feed batch parsing is a separate API that requires a login cookie.

| Platform | API File | Features | Login | Status |
|----------|----------|----------|:---:|:---:|
| **Douyin** Video/Gallery/Live Photo | `api/douyin/douyin.php` | Original-quality direct link, gallery original images, Live Photo, BGM, multi-gear backup links | Cookie-free | ✅ Active |
| **Douyin** Cloudflare Workers Edition | `api/douyin-cloudflare workers/workers.js` | Original-quality direct link, gallery original images, Live Photo, BGM, multi-gear backup links | Cookie-free | ✅ Active |
| **Douyin Home Feed** Batch Parsing | `api/douyin-home/home.php` | Batch home-feed list (feed API) | Login cookie required | ✅ Active |
| **Kuaishou** | `api/kuaishou/ksjx.php` | Video/image direct links, cover, author info | Cookie-free | ✅ Active |
| **Xiaohongshu** (Little Red Book) | `api/xiaohongshu/xhsjx.php` | Watermark-free video, gallery original images, Live Photo | Cookie-free | ✅ Active |
| **Bilibili** | `api/bilibili/bilibili.php` | Video direct link, multi-part (P) list, duration, cover | Cookie-free | ✅ Active |
| **Weibo** (Interface Version) | `api/weibo.php` | Multi-quality selection, backup links, engagement stats, cover | Cookie-free | ✅ Active |
| **Weibo** (Spider Version) | `api/weibo_v.php` | Video direct link, cover | Cookie-free | ✅ Active |
| **Toutiao** | `api/toutiao.php` | Watermark-free video, BGM, cover | Cookie-free | ✅ Active |
| **Pipigx** (Pipi Comedy) | `api/pipigx.php` | Watermark-free video, cover | Cookie-free | ✅ Active |
| **Pipixia** (Pipi Shrimp) | `api/ppxia.php` | Watermark-free video, gallery images, cover, author info | Cookie-free | ✅ Active |
| **Zuiyou** | `api/zuiyou.php` | Watermark-free video, cover, author info | Cookie-free | ✅ Active |
| **NetEase Cloud Music** | `api/wyy.py` (Python/Flask) | Song direct-link parsing | Cookie-free | ✅ Active |
| **Doubao AI** Video/Chat Images | `api/doubao/video/api.php` | Watermark-free AI-generated video links | Cookie-free | ✅ Active |
| **Jimeng AI** Video | `api/jimengai/index.php` | Watermark-free AI-generated video links | Cookie-free | ✅ Active |

> If a direct video link returns **HTTP 403** (hotlink protection), proxy it through `api/svproxyurl.php` (PHP) or `cf_sv_proxy/worker.js` (Cloudflare Workers) — for the latter, see [cf_sv_proxy/README.md](cf_sv_proxy/README.md) for deployment and API docs.

---

## 📦 Project Structure

```
short_videos/
├── api/                            # Per-platform parsing APIs
│   ├── douyin/                     # Douyin (cookie-free: video/gallery/live)
│   │   ├── douyin.php              #   Entry point
│   │   └── DouyinParser.php        #   Core parser (detail API + original quality 302)
│   ├── douyin-cloudflare workers/     # Douyin Cloudflare Workers edition
│   │   ├── workers.js              #   Worker script
│   │   └── README.md               #   Deployment guide
│   ├── douyin-home/                # Douyin home-feed batch parsing (login cookie required)
│   │   ├── home.php                #   Entry point
│   │   └── DouyinHomeParser.php    #   Home-feed parser
│   ├── kuaishou/                   # Kuaishou
│   ├── xiaohongshu/                # Xiaohongshu
│   ├── bilibili/                   # Bilibili
│   ├── doubao/                     # Doubao AI (video / chat image extraction)
│   ├── jimengai/                   # Jimeng AI video
│   ├── weibo.php / weibo_v.php     # Weibo (interface / spider)
│   ├── toutiao.php                 # Toutiao
│   ├── pipigx.php / ppxia.php      # Pipigx / Pipixia
│   ├── zuiyou.php                  # Zuiyou
│   ├── sv.php                      # Aggregate parser (auto platform detection, dispatches to per-platform APIs)
│   ├── wyy.py                      # NetEase Cloud Music (Python/Flask)
│   └── svproxyurl.php              # 403 hotlink proxy
├── cf_sv_proxy/                    # 403 proxy (Cloudflare Workers edition, see README inside)
├── img/                            # Documentation assets
├── docs/                           # Specifications
│   ├── 视频解析统一JSON格式3.0.md    #   Unified JSON format spec (Chinese)
│   └── Unified-Video-Parsing-JSON-Format-3.0.md   #   Same spec (English)
├── README.md / README_EN.md        # Documentation
└── LICENSE                         # MIT License
```

---

## 📥 Installation

### Requirements

| Item | Requirement |
|------|-------------|
| PHP | 8.0+ (curl & json extensions enabled) |
| Web Server | Apache / Nginx |
| Dependencies | None — no Composer needed |

### 1. Download the Code

```bash
git clone https://github.com/jiuhunwl/short_videos.git
cd short_videos
```

### 2. Deploy to Server

Deploy the whole directory to your web root (or any subdirectory). No configuration needed.

### 3. Douyin Workers Edition (Optional)

To run Douyin parsing on Cloudflare Workers, see [api/douyin-cloudflare workers/README.md](api/douyin-cloudflare%20workers/README.md) and deploy `workers.js`.

---

## 🚀 Usage

### Basic Usage

Access the API directly via URL with the share link (full share text supported):

```plaintext
https://your-server-domain/api/douyin/douyin.php?url=VIDEO_LINK
```

### Example Request

```bash
curl "https://api.bugpk.com/api/douyin/douyin.php?url=https://v.douyin.com/xxxx/"
```

> The Douyin API accepts full share texts with copywriting, e.g.:
> `?url=7.79 Copy and open Douyin... https://v.douyin.com/xxxx/`

### Aggregate Parsing (Auto Dispatch)

Use the aggregate endpoint `api/sv.php` when you don't want to distinguish platforms — it auto-detects the platform and forwards to the matching per-platform API:

```bash
curl "https://your-server-domain/api/sv.php?url=https://v.douyin.com/xxxx/"
```

- Platform keywords and per-platform API paths are maintained in the config block at the top of `api/sv.php`. By default it calls the per-platform APIs deployed **on the same site**; change a path to a full URL to forward to another host;
- Aggregate forwarding requires a concurrency-capable web server (Nginx/Apache + PHP-FPM both work). The built-in `php -S` server is single-process and blocks self-requests — use it only for debugging single-platform APIs.

### Example Response

```json
{
  "code": 200,
  "msg": "Parsing successful",
  "data": {
    "type": "video",
    "title": "Video Title",
    "desc": "Video description content",
    "author": {
      "name": "Author Name",
      "id": "123456789",
      "avatar": "https://example.com/avatar.jpg"
    },
    "cover": "https://example.com/cover.jpg",
    "url": "https://example.com/original_video.mp4",
    "quality": "original",
    "duration": 80.01,
    "size": 1823456,
    "size_label": "1.74MB",
    "create_time": 1766503530,
    "publish_time": "2025-12-23 23:25:30",
    "video_backup": [
      "https://example.com/backup_1.mp4"
    ],
    "images": [],
    "live_photo": [],
    "music": {
      "title": "Music Title",
      "author": "Music Author",
      "url": "https://example.com/music.mp3",
      "cover": "https://example.com/music_cover.jpg"
    },
    "video_id": "v0200fg10000xxxx"
  }
}
```

---

## 📖 API Reference

> 📘 The response data of every platform API follows a unified contract. See **[Unified Video Parsing JSON Format 3.0](docs/Unified-Video-Parsing-JSON-Format-3.0.md)** ([中文](docs/视频解析统一JSON格式3.0.md)) for the full field definitions and empty-value conventions.

### Request Parameters

| Parameter | Type   | Description                                                | Required |
|-----------|--------|------------------------------------------------------------|:---:|
| `url`     | String | Share link, or share text containing the link              | ✅ Yes    |

### Response Format

| Field                | Type         | Description                                                        |
|----------------------|--------------|--------------------------------------------------------------------|
| `code`               | Integer      | Business status code (`200` success, `400/404/500` failure)        |
| `msg`                | String       | Response message (human-readable)                                  |
| `data`               | Object/Array | Payload (empty array on failure)                                   |
| `data.type`          | String       | Content type: `video` / `image` / `live` / `unknown`               |
| `data.title`         | String       | Title (usually same as `desc`)                                     |
| `data.desc`          | String       | Description text                                                   |
| `data.author`        | Object       | Author info (`name` / `id` / `avatar`)                             |
| `data.cover`         | String       | Cover image URL                                                    |
| `data.url`           | String/Null  | Watermark-free direct URL (`type=video`); Douyin URLs carry `mime_type=video_mp4` |
| `data.quality`       | String       | Quality: `original` for Douyin original, otherwise the bitrate gear|
| `data.duration`      | Number/Null  | Duration in seconds (two decimals)                                 |
| `data.size`          | Integer      | Main URL size in bytes (`0` when probe fails)                      |
| `data.size_label`    | String       | Human-readable size (e.g. `1.74MB`)                                |
| `data.create_time`   | Integer      | Publish timestamp in seconds                                       |
| `data.publish_time`  | String       | Publish time (UTC+8, `Y-m-d H:i:s`)                                |
| `data.video_backup`  | Array        | Backup video URL list (`type=video`)                               |
| `data.video_id`      | String       | Video vid (`type=video`)                                           |
| `data.images`        | Array        | Original image URL list (`type=image/live`)                        |
| `data.live_photo`    | Array        | Live-photo list (`type=live`, each item has `image` and `video`)   |
| `data.music`         | Object       | Background music (`title` / `author` / `url` / `cover`)            |

### Status Codes

| Code  | Description                     |
|-------|---------------------------------|
| `200` | Parsing successful              |
| `400` | Invalid parameters / link       |
| `404` | Content not found               |
| `500` | Upstream request failed         |

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit issues and pull requests.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 📮 Contact

**Author**: JH-Ahua

**Official Demo Website**: [https://api.bugpk.com/](https://api.bugpk.com/)

**Feedback Email**: [admin@bugpk.com](mailto:admin@bugpk.com)

**GitHub**: [https://github.com/jiuhunwl](https://github.com/jiuhunwl)

---

## 💖 Sponsorship Support

If you find this project helpful, please consider supporting our work:

<div align="center">
  <table style="border-collapse: collapse; margin: 30px auto;">
    <tr>
      <td style="padding: 0 30px; text-align: center;">
        <img src="img/wechatpay.jpg" alt="WeChat Pay" width="200" style="display: block; margin: 0 auto;" />
      </td>
      <td style="padding: 0 30px; text-align: center;">
        <img src="img/alipay.jpg" alt="Alipay" width="200" style="display: block; margin: 0 auto;" />
      </td>
    </tr>
    <tr>
      <td align="center" style="padding: 15px 0;">
        <strong>WeChat Pay</strong>
      </td>
      <td align="center" style="padding: 15px 0;">
        <strong>Alipay</strong>
      </td>
    </tr>
  </table>
  <p style="margin-top: 20px;"><strong>TRC20:</strong></p>
  <p style="font-family: monospace; font-size: 1.1em; word-break: break-all; margin: 10px 0; font-weight: bold; padding: 0 20px;">TMgEhEBSmLjgMkv6vQwLyhkCRQXSDieuSK</p>
  <p style="margin-top: 15px;"><small>Support TRC20 network, thank you for your support, we will continue to improve the project!</small></p>
</div>

---

## 🏆 Sponsor List

Thanks to all of our generous sponsors for keeping this project going!

<details>
<summary><strong>Expand full sponsor list (36 sponsors)</strong></summary>

| # | Sponsor | Amount | Message |
|:---:|:---:|:---:|:---:|
| 1 | 梦安 | ¥60.00 | — |
| 2 | aゞ不忘初心,方得始终 | ¥100.00 | — |
| 3 | 说你愿意 | ¥58.87 | — |
| 4 | apivl.yrain.top \| 夜雨api | ¥6.66 | — |
| 5 | 周路遥 | ¥3.68 | — |
| 6 | LinBai | ¥14.00 | — |
| 7 | 吃馍 | ¥57.20 | — |
| 8 | Chovy | ¥5.00 | — |
| 9 | 芊艺Yqin | ¥18.88 | — |
| 10 | 夜猫子 | ¥9.99 | — |
| 11 | 孜恋 | ¥8.00 | — |
| 12 | 墨白 | ¥30.00 | — |
| 13 | sky | ¥107.76 | — |
| 14 | 笑而不語 | ¥8.88 | — |
| 15 | Jasper | ¥100.00 | — |
| 16 | 弘 | ¥18.88 | — |
| 17 | 零号潮玩舱 | ¥6.66 | — |
| 18 | 乐花趣 | ¥5.00 | — |
| 19 | 天行健 | ¥9.99 | — |
| 20 | Zack | ¥68.00 | — |
| 21 | 人民群众 | ¥100.00 | — |
| 22 | 小渡api | ¥10.00 | — |
| 23 | 鑫 | ¥5.00 | — |
| 24 | Y | ¥742.64 | — |
| 25 | 曲奇 | ¥8.88 | — |
| 26 | 大圣 | ¥8.88 | — |
| 27 | yyzz | ¥899.00 | — |
| 28 | 你好 | ¥8.00 | — |
| 29 | *1 | ¥18.80 | — |
| 30 | *豐 | ¥10.00 | — |
| 31 | 社恐的王同学 | ¥38.88 | — |
| 32 | 小邪 | ¥3584.00 | — |
| 33 | little | ¥100.00 | — |
| 34 | 走过十里坡 | ¥199.00 | — |
| 35 | ɴᴏᴡᴏʀɴᴇᴠᴇʀ | ¥200.00 | — |
| 36 | 路飞 | ¥88.00 | — |
| 37 | *） | ¥88.88 | — |

</details>

---

## 🌟 Star History

[![Star History Chart](https://star-history.dera.page/svg?repos=jiuhunwl/short_videos&type=date&legend=top-left)](https://star-history.dera.page/#jiuhunwl/short_videos&type=date&legend=top-left)

---

<div align="center">
  <p>⭐ If this project helps you, please give it a Star!</p>
</div>

---

*[中文](./README.md)*
