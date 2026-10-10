<div align="center">
  <img width="100px" alt="logo" src="img/icon.png"/>
  <h1>Short Video Watermark Removal & Parsing API</h1>
  <p><em>Source code for short video watermark removal and parsing interface</em></p>
  <div>
    <a href="https://github.com/jiuhunwl/short_videos/blob/main/LICENSE" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">
      <img src="https://img.shields.io/github/license/jiuhunwl/short_videos" alt="License" />
    </a>
    <a href="https://php.net" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">
      <img src="https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat&logo=php&logoColor=white" alt="PHP Version" />
    </a>
    <a href="https://github.com/jiuhunwl/short_videos" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">
      <img src="https://img.shields.io/github/stars/jiuhunwl/short_videos?style=social" alt="GitHub Stars" />
    </a>
  </div>
  <br />
  <div>
    <a href="#about" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">About</a>
    •
    <a href="#important-notice" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">Important Notice</a>
    •
    <a href="#features" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">Features</a>
    •
    <a href="#supported-platforms" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">Platforms</a>
    •
    <a href="#installation" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">Installation</a>
    •
    <a href="#usage" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">Usage</a>
    •
    <a href="#api-reference" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">API Reference</a>
    •
    <a href="#contributing" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">Contributing</a>
    •
    <a href="#license" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">License</a>
    •
    <a href="#contact" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">Contact</a>
  </div>
</div>

<div align="center">
  <p><small>Sponsored Advertisement</small></p>
  <a href="https://www.hncloud.com/" target="_blank" rel="noopener noreferrer">
    <img src="img/hncloud_ad.jpg" alt="Hncloud - Visit Official Website" width="728" />
  </a>
  <p><a href="https://www.hncloud.com/" target="_blank" rel="noopener noreferrer">Hncloud | Visit Official Website →</a></p>
</div>

---

## 📋 Table of Contents

- [About](#about)
- [Important Notice](#important-notice)
- [✨ Features](#features)
- [🌐 Supported Platforms](#supported-platforms)
- [📦 Installation](#installation)
- [🚀 Usage](#usage)
- [📖 API Reference](#api-reference)
- [🤝 Contributing](#contributing)
- [📄 License](#license)
- [📮 Contact](#contact)

---

## 🎯 About

This tool is designed to parse video links from various short video platforms and extract detailed information such as:

- Direct video URLs (without watermarks)
- Cover images
- Author information
- Video metadata

All responses are returned in structured JSON format for easy integration.

---

## Important Notice

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

- **Multi-Platform Support**: Parse video links from various short video platforms
- **Watermark Removal**: Get direct video URLs without watermarks
- **Fast Performance**: Quick response times for parsing requests
- **Structured Data**: Returns well-formatted JSON data
- **Easy Integration**: Simple API interface for seamless integration
- **No Installation Required**: Can be used directly on any PHP server

---

## 🌐 Supported Platforms

| Platform                          | API File       | Status   |
|-----------------------------------|----------------|----------|
| **Douyin** (TikTok China)         | `douyin.php`   | ✅ Active |
| **Kuaishou**                      | `kuaishou.php` | ✅ Active |
| **Kuaishou Images**               | `ksimg.php`    | ✅ Active |
| **Xiaohongshu** (Little Red Book) | `xhs.php`      | ✅ Active |
| **Xiaohongshu Images**            | `xhsimg.php`   | ✅ Active |
| **Xiaohongshu Live**              | `xhsjx.php`    | ✅ Active |
| **Qishui Music**                  | `qsmusic.php`  | ✅ Active |
| **Pipigx**                        | `pipigx.php`   | ✅ Active |
| **Pipixia**                       | `ppxia.php`    | ✅ Active |
| **Bilibili**                      | `bilibili.php` | ✅ Active |
| **Weibo** (Interface Version)     | `weibo.php`    | ✅ Active |
| **Weibo**                         | `weibo_v.php`  | ✅ Active |

---

## 📦 Installation

### Requirements

- **PHP 8.0** or higher
- Web server (Apache/Nginx)
- No additional dependencies required!

### 1. Download the Code

```bash
git clone https://github.com/jiuhunwl/short_videos.git
cd short_videos
```

### 2. Deploy to Server

Upload the PHP files to your web server. No installation or configuration needed!

---

## 🚀 Usage

### Basic Usage

Access the API directly via URL:

```plaintext
http://your-server-domain/api/xxx.php?url=VIDEO_LINK
```

### Example Request

```plaintext
https://api.bugpk.com/api/douyin.php?url=https://v.douyin.com/xxxx/
```

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
    "url": "https://example.com/video.mp4",
    "duration": 15000,
    "video_backup": [
      "https://example.com/video_backup_1.mp4",
      "https://example.com/video_backup_2.mp4"
    ],
    "images": [],
    "live_photo": [],
    "music": {
      "title": "Music Title",
      "author": "Music Author",
      "url": "https://example.com/music.mp3",
      "cover": "https://example.com/music_cover.jpg"
    },
    "video_id": "7489328058390000000"
  }
}
```

### 📱 Douyin Cookie Acquisition Tutorial

**Important Note:** Douyin parsing may require the use of cookies to improve parsing success rate.

#### Acquisition Steps:

1. Open browser and visit Douyin web version
2. Log in to your Douyin account
3. Press F12 to open developer tools
4. Switch to the Network tab
5. Refresh the page and find a request
6. Find the Cookie field in the request headers
7. Copy the complete Cookie value

#### Visual Tutorial:

<div align="center">
  <img src="img/dycookie.jpg" alt="Douyin Cookie Acquisition Tutorial" width="600" />
  <p><em>Douyin Cookie acquisition steps diagram</em></p>
</div>

---

## 📖 API Reference

### Request Parameters

| Parameter | Type   | Description                          | Required |
|-----------|--------|--------------------------------------|----------|
| `url`     | String | Video link from short video platform | ✅ Yes    |

### Response Format

| Field                | Type         | Description                                                      |
|----------------------|--------------|------------------------------------------------------------------|
| `code`               | Integer      | Business status code (`200` success, `400/404/500` failure)      |
| `msg`                | String       | Response message (human-readable)                                |
| `data`               | Object/Array | Payload (may be empty array on failure)                          |
| `data.type`          | String       | Content type: `video` / `image` / `live` / `unknown`             |
| `data.title`         | String       | Title (usually same as `desc`)                                   |
| `data.desc`          | String       | Description text                                                 |
| `data.author`        | Object       | Author object                                                    |
| `data.author.name`   | String       | Author nickname                                                  |
| `data.author.id`     | String       | Author unique identifier                                         |
| `data.author.avatar` | String       | Author avatar URL                                                |
| `data.cover`         | String       | Cover image URL                                                  |
| `data.music`         | Object       | Background music object                                          |
| `data.music.title`   | String       | Music title                                                      |
| `data.music.author`  | String       | Music author                                                     |
| `data.music.url`     | String       | Music direct URL                                                 |
| `data.music.cover`   | String       | Music cover URL                                                  |
| `data.duration`      | Integer/Null | Duration in milliseconds (can be `null`)                         |
| `data.url`           | String/Null  | Main video URL (`type=video`)                                    |
| `data.video_backup`  | Array        | Backup video URL list (`type=video`)                             |
| `data.video_id`      | String       | Video ID (`type=video`)                                          |
| `data.images`        | Array        | Image URL list (`type=image/live`)                               |
| `data.live_photo`    | Array        | Live-photo list (`type=live`, each item has `image` and `video`) |

### Status Codes

| Code  | Description                |
|-------|----------------------------|
| `200` | Parsing successful         |
| `400` | Invalid request parameters |
| `404` | Video not found            |
| `500` | Server error               |

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit issues and pull requests.

### How to Contribute

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the MIT License - see
the [LICENSE](https://github.com/jiuhunwl/short_videos/blob/main/LICENSE) file for details.

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

Thank you to the following generous supporters for your sponsorship! Your support is our motivation to keep improving!

<div align="center">
  <table style="border-collapse: collapse; margin: 25px auto; width: 95%; max-width: 900px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border-radius: 8px; overflow: hidden;">
    <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
      <th style="border: none; padding: 16px; text-align: center; color: white; font-weight: 600; font-size: 1.05em;">No.</th>
      <th style="border: none; padding: 16px; text-align: center; color: white; font-weight: 600; font-size: 1.05em;">Sponsor</th>
      <th style="border: none; padding: 16px; text-align: center; color: white; font-weight: 600; font-size: 1.05em;">Amount</th>
      <th style="border: none; padding: 16px; text-align: center; color: white; font-weight: 600; font-size: 1.05em;">Message</th>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">1</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">梦安</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥60.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">赞助用于开通网易云音...</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">2</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">aゞ不忘初心,方得始终</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥100.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">3</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">说你愿意</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥58.87</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">大义，无条件支持！...</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">4</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">apivl.yrain.top | 夜雨api</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #ff6b6b; font-weight: bold; font-size: 1.1em;">¥6.66</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">5</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">周路遥</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥3.68</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">6</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">LinBai</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥14.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">7</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">吃馍</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥57.20</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">8</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">Chovy</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥5.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">9</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">芊艺Yqin</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥18.88</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">祝BugPK越来越好</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">10</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">夜猫子</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥9.99</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持一下</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">11</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">孜恋</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥8.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">希望可以继续下去</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">12</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">墨白</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥30.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">13</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">sky</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥107.76</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">Support!</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">14</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">笑而不語</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥8.88</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">Support!</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">15</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">Jasper</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥100.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">Support!</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">16</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">弘</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥18.88</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">Support!</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">17</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">零号潮玩舱</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥6.66</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">Support!</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">18</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">乐花趣</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥5.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">Support!</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">19</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">天行健</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥9.99</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">Support!</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">20</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">Zack</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥68.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">Support!</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">21</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">人民群众</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥100.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">Support!</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">22</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">小渡api</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥10.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">Support!</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">23</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">鑫</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥5.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">Support!</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">24</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">Y</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥742.64</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">Support!</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">25</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">曲奇</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥8.88</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">Support!</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">26</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">大圣</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥8.88</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">Support!</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">27</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">yyzz</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥624.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">Support!</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">28</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">你好</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥8.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">Support!</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; padding: 14px; text-align: center; font-weight: 500;">29</td>
      <td style="border: none; padding: 14px; text-align: center;">*1</td>
      <td style="border: none; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥18.80</td>
      <td style="border: none; padding: 14px; text-align: center; color: #495057;">Support!</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; padding: 14px; text-align: center; font-weight: 500;">30</td>
      <td style="border: none; padding: 14px; text-align: center;">*豐</td>
      <td style="border: none; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥10.00</td>
      <td style="border: none; padding: 14px; text-align: center; color: #495057;">Support!</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">31</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">社恐的王同学</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥18.88</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">Support!</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; padding: 14px; text-align: center; font-weight: 500;">32</td>
      <td style="border: none; padding: 14px; text-align: center;">小邪</td>
      <td style="border: none; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥1364.00</td>
      <td style="border: none; padding: 14px; text-align: center; color: #495057;">Support!</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">33</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">little</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥100.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">Support!</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; padding: 14px; text-align: center; font-weight: 500;">34</td>
      <td style="border: none; padding: 14px; text-align: center;">走过十里坡</td>
      <td style="border: none; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥199.00</td>
      <td style="border: none; padding: 14px; text-align: center; color: #495057;">Support!</td>
    </tr>
  </table>
</div>

---

## 🌟 Star History

[![Star History Chart](https://star-history.dera.page/svg?repos=jiuhunwl/short_videos&type=date&legend=top-left)](https://star-history.dera.page/#jiuhunwl/short_videos&type=date&legend=top-left)

---

<div align="center">
  <p>⭐ If you find this project useful, please give it a star!</p>
</div>

---

*[中文](./README.md)*
