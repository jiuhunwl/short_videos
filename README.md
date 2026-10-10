<div align="center">
  <img width="100px" alt="logo" src="img/icon.png"/>
  <h1>短视频去水印解析接口</h1>
  <p><em>支持多平台短视频链接解析与去水印</em></p>
  <div>
    <a href="https://github.com/jiuhunwl/short_videos/blob/main/LICENSE" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">
      <img src="https://img.shields.io/github/license/jiuhunwl/short_videos" alt="许可证" />
    </a>
    <a href="https://php.net" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">
      <img src="https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat&logo=php&logoColor=white" alt="PHP版本" />
    </a>
    <a href="https://github.com/jiuhunwl/short_videos" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">
      <img src="https://img.shields.io/github/stars/jiuhunwl/short_videos?style=social" alt="GitHub星标" />
    </a>
  </div>
  <br />
  <div>
    <a href="#项目简介" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">项目简介</a>
    •
    <a href="#重要声明" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">重要声明</a>
    •
    <a href="#功能特点" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">功能特点</a>
    •
    <a href="#支持平台" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">支持平台</a>
    •
    <a href="#安装部署" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">安装部署</a>
    •
    <a href="#使用说明" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">使用说明</a>
    •
    <a href="#接口文档" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">接口文档</a>
    •
    <a href="#贡献指南" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">贡献指南</a>
    •
    <a href="#许可证" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">许可证</a>
    •
    <a href="#联系方式" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">联系方式</a>
    •
    <a href="#公众号" style="text-decoration: none; color: #0366d6; transition: color 0.2s;">公众号</a>
  </div>
</div>

<div align="center">
  <p><small>赞助 · Sponsored</small></p>
  <a href="https://www.hncloud.com/" target="_blank" rel="noopener noreferrer">
    <img src="img/hncloud_ad.jpg" alt="华纳云 - 点击访问官网" width="728" />
  </a>
  <p><a href="https://www.hncloud.com/" target="_blank" rel="noopener noreferrer">华纳云 | 点击访问官网 →</a></p>
</div>

---

## 📋 目录

- [项目简介](#项目简介)
- [重要声明](#重要声明)
- [✨ 功能特点](#功能特点)
- [🌐 支持平台](#支持平台)
- [📦 安装部署](#安装部署)
- [🚀 使用说明](#使用说明)
- [📖 接口文档](#接口文档)
- [🤝 贡献指南](#贡献指南)
- [📄 许可证](#许可证)
- [📮 联系方式](#联系方式)
- [📱 公众号](#公众号)

---

## 🎯 项目简介

本工具用于解析各大短视频平台的视频链接，获取视频的详细信息，包括：

- 无水印的直链视频地址
- 视频封面图片
- 作者信息
- 视频元数据

所有接口均返回结构化的 JSON 数据，便于快速集成到您的应用中。

---

## 重要声明

本项目为开源软件，遵循 MIT 许可证。任何个人或组织均可在遵守许可证及当地法律法规的前提下使用、修改和分发本项目源代码。

本项目仅提供技术研究与接口示例，**代码仅用于学习交流与技术测试，不得用于任何非法用途**。

通过本项目解析出的短视频、图集、音乐及相关素材，其著作权、邻接权及其他合法权益均归原平台及作品权利人（包括但不限于作者）所有。

使用者应自行确保已获得必要授权，并在合法、合规、合理范围内使用相关内容；由使用本项目产生的任何直接或间接后果，均由使用者自行承担。

如权利人认为相关内容存在侵权或不当使用，请及时联系处理，我们将积极配合。

---

## ✨ 功能特点

- **多平台支持**：支持主流短视频平台的链接解析
- **去水印解析**：获取无水印的视频直链
- **极速响应**：快速解析视频链接，响应迅速
- **结构化数据**：返回标准 JSON 格式数据
- **易于集成**：简单的 API 接口，便于集成
- **零配置部署**：无需安装依赖，上传即用

---

## 🌐 支持平台

| 平台                 | 接口文件           | 状态   |
|--------------------|----------------|------|
| **抖音** (TikTok 中国) | `douyin.php`   | ✅ 可用 |
| **快手**             | `kuaishou.php` | ✅ 可用 |
| **小红书**            | `xhsjx.php`    | ✅ 可用 |
| **汽水音乐**           | `dymusic.php`  | ✅ 可用 |
| **皮皮搞笑**           | `pipigx.php`   | ✅ 可用 |
| **皮皮虾**            | `ppxia.php`    | ✅ 可用 |
| **哔哩哔哩**           | `bilibili.php` | ✅ 可用 |
| **微博** 【接口版】       | `weibo.php`    | ✅ 可用 |
| **微博**             | `weibo_v.php`  | ✅ 可用 |
| **今日头条**           | `toutiao.php`  | ✅ 可用 |

---

## 📦 安装部署

### 环境要求

- **PHP 8.0** 或更高版本
- Web 服务器 (Apache/Nginx)
- 无需额外依赖！

### 1. 下载代码

```bash
git clone https://github.com/jiuhunwl/short_videos.git
cd short_videos
```

### 2. 部署到服务器

将 PHP 文件上传到您的 Web 服务器即可使用，无需任何配置！

---

## 🚀 使用说明

### 基础用法

直接通过 URL 访问接口：

```plaintext
https://你的服务器地址/api/xxx.php?url=视频链接
```

### 请求示例

```plaintext
https://api.bugpk.com/api/douyin.php?url=https://v.douyin.com/xxxx/
```

### 响应示例

```json
{
  "code": 200,
  "msg": "解析成功",
  "data": {
    "type": "video",
    "title": "视频标题",
    "desc": "视频描述内容",
    "author": {
      "name": "作者名称",
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
      "title": "背景音乐标题",
      "author": "背景音乐作者",
      "url": "https://example.com/music.mp3",
      "cover": "https://example.com/music_cover.jpg"
    },
    "video_id": "7489328058390000000"
  }
}
```

### 📱 抖音 Cookie 获取教程

**重要提示：** 抖音解析可能需要使用 Cookie 以提高解析成功率。

#### 获取步骤：

1. 打开浏览器，访问抖音网页版
2. 登录您的抖音账号
3. 按 F12 打开开发者工具
4. 切换到 Network 标签页
5. 刷新页面，找到一个请求
6. 在请求头中找到 Cookie 字段
7. 复制完整的 Cookie 值

#### 图解教程：

<div align="center">
  <img src="img/dycookie.jpg" alt="抖音Cookie获取教程" width="600" />
  <p><em>抖音Cookie获取步骤示意图</em></p>
</div>

---

## 📖 接口文档

### 请求参数

| 参数名   | 类型  | 描述         | 是否必填 |
|-------|-----|------------|------|
| `url` | 字符串 | 短视频平台的视频链接 | ✅ 是  |

### 响应格式

| 字段                   | 类型    | 描述                                          |
|----------------------|-------|---------------------------------------------|
| `code`               | 整数    | 业务状态码 (`200` 成功，`400/404/500` 失败)           |
| `msg`                | 字符串   | 响应消息（便于直接展示错误原因）                            |
| `data`               | 对象/数组 | 返回数据主体（失败时可能为空数组）                           |
| `data.type`          | 字符串   | 内容类型：`video` / `image` / `live` / `unknown` |
| `data.title`         | 字符串   | 标题（通常与 `desc` 一致）                           |
| `data.desc`          | 字符串   | 描述文本                                        |
| `data.author`        | 对象    | 作者信息对象                                      |
| `data.author.name`   | 字符串   | 作者昵称                                        |
| `data.author.id`     | 字符串   | 作者唯一标识                                      |
| `data.author.avatar` | 字符串   | 作者头像 URL                                    |
| `data.cover`         | 字符串   | 封面图 URL                                     |
| `data.music`         | 对象    | 背景音乐信息对象                                    |
| `data.music.title`   | 字符串   | 背景音乐标题                                      |
| `data.music.author`  | 字符串   | 背景音乐作者                                      |
| `data.music.url`     | 字符串   | 背景音乐直链                                      |
| `data.music.cover`   | 字符串   | 背景音乐封面 URL                                  |
| `data.duration`      | 整数/空  | 视频时长（毫秒，可能为 `null`）                         |
| `data.url`           | 字符串/空 | 视频直链（`type=video` 时返回）                      |
| `data.video_backup`  | 数组    | 视频备选直链列表（`type=video`）                      |
| `data.video_id`      | 字符串   | 视频 ID（`type=video`）                         |
| `data.images`        | 数组    | 图集图片 URL 数组（`type=image/live`）              |
| `data.live_photo`    | 数组    | 实况图数组（`type=live`，每项包含 `image` 和 `video`）   |

### 状态码说明

| 状态码   | 描述     |
|-------|--------|
| `200` | 解析成功   |
| `400` | 请求参数错误 |
| `404` | 视频不存在  |
| `500` | 服务器错误  |

---

## 🤝 贡献指南

欢迎贡献代码！请随时提交 Issue 和 Pull Request。

### 贡献步骤

1. Fork 本仓库
2. 创建特性分支 (`git checkout -b feature/AmazingFeature`)
3. 提交更改 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 开启 Pull Request

---

## 📄 许可证

本项目采用 MIT 许可证 - 详见 [LICENSE](https://github.com/jiuhunwl/short_videos/blob/main/LICENSE) 文件。

---

## 📮 联系方式

**作者**：JH-Ahua

**接口演示官网**：[https://api.bugpk.com/](https://api.bugpk.com/)

**反馈邮箱**：[admin@bugpk.com](mailto:admin@bugpk.com)

**GitHub**：[https://github.com/jiuhunwl](https://github.com/jiuhunwl)

---

## 📱 公众号

欢迎关注微信公众号获取更多资讯：

<div align="center">
  <table style="border-collapse: collapse; margin: 30px auto;">
    <tr>
      <td style="text-align: center;">
        <img src="img/gzh.jpg" alt="微信公众号" width="200" style="display: block; margin: 0 auto;" />
      </td>
    </tr>
    <tr>
      <td align="center" style="padding: 15px 0;">
        <strong>微信公众号</strong>
      </td>
    </tr>
  </table>
</div>

---

## 💖 赞赏支持

如果您觉得这个项目对您有帮助，欢迎通过以下方式支持我们的工作：

<div align="center">
  <table style="border-collapse: collapse; margin: 30px auto;">
    <tr>
      <td style="padding: 0 30px; text-align: center;">
        <img src="img/wechatpay.jpg" alt="微信支付" width="200" style="display: block; margin: 0 auto;" />
      </td>
      <td style="padding: 0 30px; text-align: center;">
        <img src="img/alipay.jpg" alt="支付宝" width="200" style="display: block; margin: 0 auto;" />
      </td>
    </tr>
    <tr>
      <td align="center" style="padding: 15px 0;">
        <strong>微信支付</strong>
      </td>
      <td align="center" style="padding: 15px 0;">
        <strong>支付宝</strong>
      </td>
    </tr>
  </table>
  <p style="margin-top: 20px;"><strong>TRC20：</strong></p>
  <p style="font-family: monospace; font-size: 1.1em; word-break: break-all; margin: 10px 0; font-weight: bold; padding: 0 20px;">TMgEhEBSmLjgMkv6vQwLyhkCRQXSDieuSK</p>
  <p style="margin-top: 15px;"><small>支持TRC20网络，感谢您的支持，我们会持续改进项目！</small></p>
</div>

---

## 🏆 赞助者名单

感谢以下支持者的慷慨赞助，您的支持是我们持续改进的动力！

<div align="center">
  <table style="border-collapse: collapse; margin: 25px auto; width: 95%; max-width: 900px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border-radius: 8px; overflow: hidden;">
    <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
      <th style="border: none; padding: 16px; text-align: center; color: white; font-weight: 600; font-size: 1.05em;">序号</th>
      <th style="border: none; padding: 16px; text-align: center; color: white; font-weight: 600; font-size: 1.05em;">赞助者</th>
      <th style="border: none; padding: 16px; text-align: center; color: white; font-weight: 600; font-size: 1.05em;">金额</th>
      <th style="border: none; padding: 16px; text-align: center; color: white; font-weight: 600; font-size: 1.05em;">留言</th>
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
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">14</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">笑而不語</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥8.88</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">15</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">Jasper</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥100.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">16</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">弘</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥18.88</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">17</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">零号潮玩舱</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥6.66</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">18</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">乐花趣</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥5.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">19</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">天行健</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥9.99</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">20</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">Zack</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥68.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">21</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">人民群众</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥100.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">22</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">小渡api</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥10.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">23</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">鑫</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥5.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">24</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">Y</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥742.64</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">25</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">曲奇</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥8.88</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">26</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">大圣</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥8.88</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">27</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">yyzz</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥624.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">28</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">你好</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥8.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">29</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">*1</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥18.80</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; padding: 14px; text-align: center; font-weight: 500;">30</td>
      <td style="border: none; padding: 14px; text-align: center;">*豐</td>
      <td style="border: none; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥10.00</td>
      <td style="border: none; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">31</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">社恐的王同学</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥18.88</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; padding: 14px; text-align: center; font-weight: 500;">32</td>
      <td style="border: none; padding: 14px; text-align: center;">小邪</td>
      <td style="border: none; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥1364.00</td>
      <td style="border: none; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #ffffff;">
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; font-weight: 500;">33</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center;">little</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em;">¥100.00</td>
      <td style="border: none; border-bottom: 1px solid #e9ecef; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
      <td style="border: none; padding: 14px; text-align: center; font-weight: 500;">34</td>
      <td style="border: none; padding: 14px; text-align: center;">走过十里坡</td>
      <td style="border: none; padding: 14px; text-align: center; color: #28a745; font-weight: bold; font-size: 1.1em;">¥199.00</td>
      <td style="border: none; padding: 14px; text-align: center; color: #495057;">支持！</td>
    </tr>

  </table>
</div>

---

## 🌟 Star History

[![Star History Chart](https://star-history.dera.page/svg?repos=jiuhunwl/short_videos&type=date&legend=top-left)](https://star-history.dera.page/#jiuhunwl/short_videos&type=date&legend=top-left)

---

<div align="center">
  <p>⭐ 如果这个项目对您有帮助，请给个 Star 支持一下！</p>
</div>

---

*[English](./README_EN.md)*
