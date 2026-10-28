<div align="center">
  <img width="100px" alt="logo" src="img/icon.png"/>
  <h1>短视频去水印解析接口</h1>
  <p><em>多平台短视频解析 · 免 Cookie · 无水印直链 · 视频开箱即用</em></p>
  <div>
    <a href="https://github.com/jiuhunwl/short_videos/blob/main/LICENSE">
      <img src="https://img.shields.io/github/license/jiuhunwl/short_videos" alt="许可证" />
    </a>
    <a href="https://php.net">
      <img src="https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=flat&logo=php&logoColor=white" alt="PHP版本" />
    </a>
    <a href="https://github.com/jiuhunwl/short_videos">
      <img src="https://img.shields.io/github/stars/jiuhunwl/short_videos?style=social" alt="GitHub星标" />
    </a>
  </div>
  <br/>
  <p>
    <a href="#-项目简介">项目简介</a> •
    <a href="#-重要声明">重要声明</a> •
    <a href="#-功能特点">功能特点</a> •
    <a href="#-支持平台">支持平台</a> •
    <a href="#-项目结构">项目结构</a> •
    <a href="#-安装部署">安装部署</a> •
    <a href="#-使用说明">使用说明</a> •
    <a href="#-接口文档">接口文档</a> •
    <a href="#-贡献指南">贡献指南</a> •
    <a href="#-联系方式">联系方式</a>
  </p>
</div>

<div align="center">
  <p><small>赞助 · Sponsored</small></p>
  <a href="https://www.hncloud.com/" target="_blank" rel="noopener noreferrer">
    <img src="img/hncloud_ad.jpg" alt="华纳云 - 点击访问官网" width="728" />
  </a>
  <p><a href="https://www.hncloud.com/" target="_blank" rel="noopener noreferrer">华纳云 | 点击访问官网 →</a></p>
</div>

---

## 🎯 项目简介

本工具用于解析各大短视频平台的分享链接，一键获取无水印直链与完整元数据：

- **无水印视频/图集/实况直链**（抖音支持原画 `original` 画质解析）
- 图集**原图**与实况（Live Photo）的图片+视频对
- 视频封面、作者信息、背景音乐、时长、发布时间等元数据
- 全部接口返回结构化 JSON，便于快速集成

> 抖音解析采用**免 Cookie 详情接口**，无需登录、无需抓包提取 Cookie，视频 / 图集 / 实况三类内容通吃。

---

## 📢 重要声明

本项目为开源软件，遵循 MIT 许可证。任何个人或组织均可在遵守许可证及当地法律法规的前提下使用、修改和分发本项目源代码。

本项目仅提供技术研究与接口示例，**代码仅用于学习交流与技术测试，不得用于任何非法用途**。

通过本项目解析出的短视频、图集、音乐及相关素材，其著作权、邻接权及其他合法权益均归原平台及作品权利人（包括但不限于作者）所有。

使用者应自行确保已获得必要授权，并在合法、合规、合理范围内使用相关内容；由使用本项目产生的任何直接或间接后果，均由使用者自行承担。

如权利人认为相关内容存在侵权或不当使用，请及时联系处理，我们将积极配合。

---

## ✨ 功能特点

- **多平台支持**：抖音 / 快手 / 小红书 / 哔哩哔哩 / 微博 / 今日头条等主流平台
- **免 Cookie 解析**：抖音视频、图集、实况均无需登录与 Cookie 配置
- **原画直链**：抖音自动通过 vid 302 解析原画（`quality=original`），多档码率自动降级
- **直链规范**：抖音视频直链自动补齐 `mime_type=video_mp4`（已存在则不重复追加），便于播放器与下载器直接识别为 MP4
- **类型全覆盖**：短视频、图文图集、实况（Live Photo）统一契约输出
- **双运行时**：PHP 接口 + Cloudflare Workers 版（抖音），按部署环境任选
- **聚合分发**：`api/sv.php` 单入口聚合，自动识别平台并转发到对应单平台接口
- **零依赖部署**：纯 PHP 实现，无需 Composer，上传即用

---

## 🌐 支持平台

> 抖音内容解析仅保留 **PHP 版**与 **Cloudflare Workers 版**两个整合版（视频/图集/实况统一入口）；主页批量解析为独立接口，需登录 Cookie。

| 平台 | 接口文件 | 功能 | 登录要求 | 状态 |
|------|----------|------|:---:|:---:|
| **抖音** 视频/图集/实况 | `api/douyin/douyin.php` | 原画直链、图集原图、实况 Live Photo、背景音乐、多档备选直链 | 免登录 | ✅ 可用 |
| **抖音** Cloudflare Workers 版 | `api/douyin-cloudflare workers/workers.js` | 原画直链、图集原图、实况 Live Photo、背景音乐、多档备选直链 | 免登录 | ✅ 可用 |
| **抖音主页** 批量解析 | `api/douyin-home/home.php` | 主页作品批量列表（feed 接口） | 需登录 Cookie | ✅ 可用 |
| **快手** | `api/kuaishou/ksjx.php` | 视频直链、图片直链、封面、作者信息 | 免登录 | ✅ 可用 |
| **小红书** | `api/xiaohongshu/xhsjx.php` | 视频去水印、图集原图、实况 Live Photo | 免登录 | ✅ 可用 |
| **哔哩哔哩** | `api/bilibili/bilibili.php` | 视频直链、分P 列表、时长、封面 | 免登录 | ✅ 可用 |
| **微博**（接口版） | `api/weibo.php` | 多画质选择、备选直链、互动数据、封面 | 免登录 | ✅ 可用 |
| **微博**（爬虫版） | `api/weibo_v.php` | 视频直链、封面 | 免登录 | ✅ 可用 |
| **今日头条** | `api/toutiao.php` | 视频去水印、背景音乐、封面 | 免登录 | ✅ 可用 |
| **皮皮搞笑** | `api/pipigx.php` | 视频去水印、封面 | 免登录 | ✅ 可用 |
| **皮皮虾** | `api/ppxia.php` | 视频去水印、图集图片、封面、作者信息 | 免登录 | ✅ 可用 |
| **最右** | `api/zuiyou.php` | 视频去水印、封面、作者信息 | 免登录 | ✅ 可用 |
| **网易云音乐** | `api/wyy.py`（Python/Flask） | 歌曲直链解析 | 免登录 | ✅ 可用 |
| **豆包 AI** 视频/对话图片 | `api/doubao/video/api.php` | AI 生成视频无水印直链 | 免登录 | ✅ 可用 |
| **即梦 AI** 视频 | `api/jimengai/index.php` | AI 生成视频无水印直链 | 免登录 | ✅ 可用 |

> 视频直链出现 **403 防盗链**时，可用 `api/svproxyurl.php`（PHP）或 `cf_sv_proxy/worker.js`（Cloudflare Workers）代理转发，后者部署与接口详见 [cf_sv_proxy/README.md](cf_sv_proxy/README.md)。

---

## 📦 项目结构

```
short_videos/
├── api/                            # 各平台解析接口
│   ├── douyin/                     # 抖音解析（免 Cookie，视频/图集/实况）
│   │   ├── douyin.php              #   解析入口
│   │   └── DouyinParser.php        #   解析核心（详情接口 + 原画 302）
│   ├── douyin-cloudflare workers/     # 抖音解析 Cloudflare Workers 版
│   │   ├── workers.js              #   Worker 脚本
│   │   └── README.md               #   部署说明
│   ├── douyin-home/                # 抖音主页批量解析（需登录 Cookie）
│   │   ├── home.php                #   入口
│   │   └── DouyinHomeParser.php    #   主页解析核心
│   ├── kuaishou/                   # 快手解析
│   ├── xiaohongshu/                # 小红书解析
│   ├── bilibili/                   # 哔哩哔哩解析
│   ├── doubao/                     # 豆包 AI（视频解析 / 对话图片提取）
│   ├── jimengai/                   # 即梦 AI 视频解析
│   ├── weibo.php / weibo_v.php     # 微博解析（接口版 / 爬虫版）
│   ├── toutiao.php                 # 今日头条解析
│   ├── pipigx.php / ppxia.php      # 皮皮搞笑 / 皮皮虾解析
│   ├── zuiyou.php                  # 最右解析
│   ├── sv.php                      # 聚合解析（自动识别平台，转发单平台接口）
│   ├── wyy.py                      # 网易云音乐解析（Python/Flask）
│   └── svproxyurl.php              # 视频直链 403 代理
├── cf_sv_proxy/                    # 403 代理（Cloudflare Workers 版，含部署文档）
├── img/                            # 文档图片资源
├── docs/                           # 规范文档
│   ├── 视频解析统一JSON格式3.0.md    #   统一 JSON 格式规范（中文）
│   └── Unified-Video-Parsing-JSON-Format-3.0.md   #   同上（English）
├── README.md / README_EN.md        # 项目文档
└── LICENSE                         # MIT 许可证
```

---

## 📥 安装部署

### 环境要求

| 项目 | 要求 |
|------|------|
| PHP | 8.0+（启用 curl、json 扩展） |
| Web 服务器 | Apache / Nginx |
| 依赖 | 无需 Composer，零依赖 |

### 1. 下载代码

```bash
git clone https://github.com/jiuhunwl/short_videos.git
cd short_videos
```

### 2. 部署到服务器

将整个目录部署到 Web 站点根目录（或任意子目录）即可，无需任何配置。

### 3. 抖音 Workers 版（可选）

如需部署在 Cloudflare Workers 上，进入 `api/douyin-cloudflare workers/` 查看 [README](api/douyin-cloudflare%20workers/README.md)，粘贴 `workers.js` 部署即可。

---

## 🚀 使用说明

### 基础用法

直接通过 URL 访问接口，传入分享链接（支持整段分享文本）：

```plaintext
https://你的服务器地址/api/douyin/douyin.php?url=视频链接
```

### 请求示例

```bash
curl "https://api.bugpk.com/api/douyin/douyin.php?url=https://v.douyin.com/xxxx/"
```

> 抖音接口支持直接粘贴带文案的分享文本，例如：
> `?url=7.79 复制打开抖音... https://v.douyin.com/xxxx/ 看看这个`

### 聚合解析（自动分发）

不想区分平台时，使用聚合入口 `api/sv.php`，自动识别平台并转发到对应单平台接口：

```bash
curl "https://你的服务器地址/api/sv.php?url=https://v.douyin.com/xxxx/"
```

- 平台关键词、单平台接口路径均在 `api/sv.php` 顶部配置区维护，默认调用**本站部署目录**下的单平台接口，也可改为完整 URL 转发到其他站点；
- 聚合转发需要 Web 服务器支持并发（Nginx/Apache + PHP-FPM 均可）；`php -S` 内置服务器为单进程，自请求会阻塞，仅建议用于单平台接口的本地调试。

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
      "title": "背景音乐标题",
      "author": "背景音乐作者",
      "url": "https://example.com/music.mp3",
      "cover": "https://example.com/music_cover.jpg"
    },
    "video_id": "v0200fg10000xxxx"
  }
}
```

---

## 📖 接口文档

> 📘 各平台解析接口的返回数据遵循统一契约，完整字段定义与空值约定见 **[视频解析统一 JSON 格式 3.0](docs/视频解析统一JSON格式3.0.md)**（[English](docs/Unified-Video-Parsing-JSON-Format-3.0.md)）。

### 请求参数

| 参数名 | 类型 | 描述 | 是否必填 |
|--------|------|------|:---:|
| `url` | 字符串 | 短视频平台的分享链接或含链接的分享文本 | ✅ 是 |

### 响应格式

| 字段 | 类型 | 描述 |
|------|------|------|
| `code` | 整数 | 业务状态码（`200` 成功，`400/404/500` 失败） |
| `msg` | 字符串 | 响应消息（便于直接展示错误原因） |
| `data` | 对象/数组 | 返回数据主体（失败时为空数组） |
| `data.type` | 字符串 | 内容类型：`video` / `image` / `live` / `unknown` |
| `data.title` | 字符串 | 标题（通常与 `desc` 一致） |
| `data.desc` | 字符串 | 描述文本 |
| `data.author` | 对象 | 作者信息（`name` / `id` / `avatar`） |
| `data.cover` | 字符串 | 封面图 URL |
| `data.url` | 字符串/null | 无水印直链（`type=video` 时返回）；抖音直链已补齐 `mime_type=video_mp4` |
| `data.quality` | 字符串 | 画质：抖音原画为 `original`，其余返回码率档位名 |
| `data.duration` | 数值/null | 视频时长（秒，保留两位小数） |
| `data.size` | 整数 | 主直链字节大小（探测失败为 `0`） |
| `data.size_label` | 字符串 | 人类可读大小（如 `1.74MB`） |
| `data.create_time` | 整数 | 发布时间戳（秒） |
| `data.publish_time` | 字符串 | 发布时间（东八区 `Y-m-d H:i:s`） |
| `data.video_backup` | 数组 | 备选直链列表（`type=video`） |
| `data.video_id` | 字符串 | 视频 vid（`type=video`） |
| `data.images` | 数组 | 图集原图 URL 数组（`type=image/live`） |
| `data.live_photo` | 数组 | 实况图数组（`type=live`，每项含 `image` 和 `video`） |
| `data.music` | 对象 | 背景音乐（`title` / `author` / `url` / `cover`） |

### 状态码说明

| 状态码 | 描述 |
|--------|------|
| `200` | 解析成功 |
| `400` | 请求参数错误 / 链接格式错误 |
| `404` | 内容不存在 |
| `500` | 上游接口请求失败 |

---

## 🤝 贡献指南

欢迎贡献代码！请随时提交 Issue 和 Pull Request。

1. Fork 本仓库
2. 创建特性分支 (`git checkout -b feature/AmazingFeature`)
3. 提交更改 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 开启 Pull Request

---

## 📄 许可证

本项目采用 MIT 许可证 - 详见 [LICENSE](LICENSE) 文件。

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

<details>
<summary><strong>展开查看完整赞助者名单（36 位）</strong></summary>

<div align="center">

| 序号 | 赞助者 | 金额 | 留言 |
|:---:|:---:|:---:|:---:|
| 1 | 梦安 | ¥60.00 | 赞助用于开通网易云音... |
| 2 | aゞ不忘初心,方得始终 | ¥100.00 | 支持！ |
| 3 | 说你愿意 | ¥58.87 | 大义，无条件支持！... |
| 4 | apivl.yrain.top \| 夜雨api | ¥6.66 | 支持！ |
| 5 | 周路遥 | ¥3.68 | 支持！ |
| 6 | LinBai | ¥14.00 | 支持！ |
| 7 | 吃馍 | ¥57.20 | 支持！ |
| 8 | Chovy | ¥5.00 | 支持！ |
| 9 | 芊艺Yqin | ¥18.88 | 祝BugPK越来越好 |
| 10 | 夜猫子 | ¥9.99 | 支持一下 |
| 11 | 孜恋 | ¥8.00 | 希望可以继续下去 |
| 12 | 墨白 | ¥30.00 | 支持！ |
| 13 | sky | ¥107.76 | 支持！ |
| 14 | 笑而不語 | ¥8.88 | 支持！ |
| 15 | Jasper | ¥100.00 | 支持！ |
| 16 | 弘 | ¥18.88 | 支持！ |
| 17 | 零号潮玩舱 | ¥6.66 | 支持！ |
| 18 | 乐花趣 | ¥5.00 | 支持！ |
| 19 | 天行健 | ¥9.99 | 支持！ |
| 20 | Zack | ¥68.00 | 支持！ |
| 21 | 人民群众 | ¥100.00 | 支持！ |
| 22 | 小渡api | ¥10.00 | 支持！ |
| 23 | 鑫 | ¥5.00 | 支持！ |
| 24 | Y | ¥742.64 | 支持！ |
| 25 | 曲奇 | ¥8.88 | 支持！ |
| 26 | 大圣 | ¥8.88 | 支持！ |
| 27 | yyzz | ¥1064.00 | 支持！ |
| 28 | 你好 | ¥8.00 | 支持！ |
| 29 | *1 | ¥18.80 | 支持！ |
| 30 | *豐 | ¥10.00 | 支持！ |
| 31 | 社恐的王同学 | ¥38.88 | 支持！ |
| 32 | 小邪 | ¥3584.00 | 支持！ |
| 33 | little | ¥100.00 | 支持！ |
| 34 | 走过十里坡 | ¥199.00 | 支持！ |
| 35 | ɴᴏᴡᴏʀɴᴇᴠᴇʀ | ¥200.00 | 支持！ |
| 36 | 路飞 | ¥425.76 | 支持！ |
| 37 | *） | ¥88.88 | 支持！ |

</div>

</details>

---

## 🌟 Star History

[![Star History Chart](https://star-history.dera.page/svg?repos=jiuhunwl/short_videos&type=date&legend=top-left)](https://star-history.dera.page/#jiuhunwl/short_videos&type=date&legend=top-left)

---

<div align="center">
  <p>⭐ 如果这个项目对您有帮助，请给个 Star 支持一下！</p>
</div>

---

*[English](./README_EN.md)*
