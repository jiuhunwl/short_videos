# 改动说明 · 多清晰度解析版

> 针对仓库 `github.com/zxzxnb666/short_videos` 的本地改动记录。
> 目的：把抖音解析从"仅返回最高画质一档"，升级为"返回多档清晰度供前端选择"。

---

## 一、本次改了什么

### 1. 后端：`api/douyin/DouyinParser.php`

| 改动 | 位置 | 说明 |
| --- | --- | --- |
| 新增方法 `extractVideoOptions()` | 文件末尾（`extractHighestQualityVideo` 之后） | 遍历抖音返回的 `bitRateList`，把每一档清晰度都提取出来，带 `quality`/`name`/`bitrate`/`size`/`url` 字段，按码率降序（原画在前）。名称缺失时按码率兜底成「原画/超清、高清、标清、流畅」 |
| 在 `formatData()` 的视频分支加调用 | 约第 414 行 | 新增输出字段 `data.video_options`；原来的最高画质 `url` 保留作默认 |

**接口新增返回字段 `data.video_options`（示例）：**
```json
"video_options": [
  { "quality": 112, "name": "原画", "bitrate": 3400000, "size": 15200000, "url": "https://.../原画.mp4" },
  { "quality": 104, "name": "高清", "bitrate": 1800000, "size": 8800000,  "url": "https://.../高清.mp4" },
  { "quality": 101, "name": "标清", "bitrate": 900000,  "size": 4200000,  "url": "https://.../标清.mp4" }
]
```

### 2. 前端：`short_videos/index.html`（新建）

一个真正的前端网页（原仓库没有网页，`sv1.php`/`sv2.php` 其实是后端聚合接口）。功能：

- 输入分享链接 → 调本地接口 `../api/douyin/douyin.php?url=...`
- 拿到 `video_options` → 渲染成**清晰度下拉框**（带码率、文件大小）
- 选哪档就切换播放器和下载链接
- 兼容旧接口（无 `video_options` 时回退到默认 `url`）
- 附带下载按钮、复制链接按钮

---

## 二、怎么部署

### 环境要求
- PHP 8.0+（含 cURL 扩展）
- 把整个仓库传到服务器，或用 PHP 内置服务器

### 本地快速跑起来
```bash
cd short_videos          # 进入仓库目录
php -S 0.0.0.0:8080      # PHP 内置服务器

# 浏览器打开
# http://localhost:8080/short_videos/index.html
```

### 部署到服务器（Nginx/Apache）
把整个仓库目录放到 Web 根目录即可，无需额外配置。目录结构保证前端相对路径能对上接口：
```
网站根目录/
├── api/douyin/douyin.php      # 解析接口
└── short_videos/index.html    # 前端页面
```

### 必须配置：抖音 Cookie
编辑 `api/douyin/douyin.php`，约第 56 行：
```php
$cookie = "把你的抖音Cookie粘到这里";
```
抖音解析依赖 Cookie，没填或过期是解析失败的主因。获取方法见仓库 README「抖音 Cookie 获取教程」。

---

## 三、自测清单

> 在自己服务器（或本机 PHP 环境）上按顺序验证。

### 1. 语法检查（必做）
```bash
php -l api/douyin/DouyinParser.php
# 输出 "No syntax errors detected" 即通过
```

### 2. 接口测试
```bash
# 用一条你账号下/已授权的抖音分享链接
curl "http://localhost:8080/api/douyin/douyin.php?url=https://v.douyin.com/xxxx/"

# 期望：返回 JSON，code=200，data 中同时有 url（默认最高画质）和 video_options（多档）
```
- ✅ 出现 `video_options` 数组 → 多档改造成功
- ❌ `video_options` 为空数组 / `code` 非 200 → 检查 Cookie 是否有效，或抖音数据结构是否改版（需更新 `extractVideoOptions` 里的取字段路径）

### 3. 前端测试
浏览器打开 `http://localhost:8080/short_videos/index.html`
- ✅ 粘贴链接 → 解析 → 出现清晰度下拉框（原画/高清/标清…）
- ✅ 切换下拉 → 播放器/下载链接随之变化
- ✅ 点"下载当前清晰度" → 能下载对应档位

---

## 四、已知边界（如实说明）

- **多档清晰度依赖抖音数据**：只有接口返回的 `bitRateList` 含多档时，`video_options` 才有多个选项；单档视频则只有一个选项。
- **`gearName` 可能为空**：已按码率兜底生成可读名称（原画/高清/标清/流畅）。
- **抖音改版风险**：`qualityType`/`gearName`/`bitRateList` 字段可能随平台变化，失效时需要同步更新 `extractVideoOptions` 的取字段逻辑（与 `extractHighestQualityVideo` 同源，可对照改）。
- **Cookie 时效**：抖音 Cookie 会过期，需定期更新。

---

## 五、合规提醒

- 本项目及本次改动仅用于**学习交流与技术测试**，只解析**自己账号、自己作品或已获授权**的内容。
- 公开部署"解析他人视频并下载"的服务存在版权与平台规则风险，不建议对外提供。
- 国内公网部署需完成 ICP 备案。

---

## 六、手机同步到你的 Fork（参考）

如果你要改的是自己 Fork 的仓库，手机可这样操作：
1. 手机浏览器打开你 Fork 的仓库；
2. 进入 `api/douyin/DouyinParser.php` → 点铅笔图标 ✏️ 编辑；
3. 把「文件末尾新增的 `extractVideoOptions` 方法」+「`formatData` 视频分支新增的那一行调用」粘贴进去 → Commit changes；
4. 前端 `short_videos/index.html` 是**新文件**：点仓库页 "Add file → Create new file"，把整份 HTML 粘贴进去 → Commit；
5. 在服务器上重新拉取 / 上传这两个文件，按第三节自测。

> 建议优先用电脑操作：手机改大段 PHP/HTML 容易出错，且无法本地 `php -l` 校验。
