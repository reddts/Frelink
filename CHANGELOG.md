# Frelink 项目更新日志

## 2026-03-22

### 里程碑：M1 公开导航与移动端入口继续收口

- 桌面端公开一级导航继续统一为内容消费路径：
  - `首页 / 主题 / FAQ / 综述 / 观察 / 帮助`
  - 隐藏 `专栏 / 创作者` 的公开一级入口
- 发布入口链路继续做兼容修复：
  - 前台主要发布链接改为统一走 `frelink_publish_url()`
  - 问题与文章发布页在知识归档表不存在时可自动降级，不再阻塞正常发布
- 问题页、文章页与移动端公共壳继续收口语义：
  - 子栏目筛选由伪 `tablist` 收回为真实导航
  - 移动端底部栏改为纯内容消费路径，不再把 `发布` 作为全局主导航项
  - 移动端底部与弹层发布入口改为复用统一 helper 输出，减少 `问题 / 文章 / FAQ / 综述` 混用
  - 桌面端与移动端发布页进一步改成“知识形态优先”，弱化 `文章 / 提问` 的旧表达
- 多语言 helper 第一批落地：
  - 新增并接入 `frelink_content_label()` / `frelink_content_description()` / `frelink_nav_label()`
  - 首页、文章页、导航、移动端底部栏开始复用统一命名
  - 发布页、详情提示和列表摘要中的新增文案继续回收到语言包，英文站点不再直接漏出这一批中文提示
  - 英文语言包继续补齐 `FAQ / Knowledge Content / Research / Help` 这一组公开语义，公开入口不再优先落回旧 `Question / Article` 表达

### 影响范围

- 公共函数：
  - [app/function.inc.php](/mnt/f/workwww/knowlege-github/app/function.inc.php)
- 导航与控制器：
  - [app/common/library/helper/UserAuthHelper.php](/mnt/f/workwww/knowlege-github/app/common/library/helper/UserAuthHelper.php)
  - [app/frontend/Question.php](/mnt/f/workwww/knowlege-github/app/frontend/Question.php)
  - [app/frontend/Article.php](/mnt/f/workwww/knowlege-github/app/frontend/Article.php)
  - [app/model/Help.php](/mnt/f/workwww/knowlege-github/app/model/Help.php)
- 桌面模板：
  - [public/templates/default/html/global/nav.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/global/nav.php)
  - [public/templates/default/html/block.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/block.php)
  - [public/templates/default/html/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/index.php)
  - [public/templates/default/html/question/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/question/index.php)
  - [public/templates/default/html/article/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/article/index.php)
  - [public/templates/default/html/question/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/question/publish.php)
  - [public/templates/default/html/article/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/article/publish.php)
- 移动模板：
  - [public/templates/default/mobile/block.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/block.php)

### 验证

- `git diff --check` 已通过
- 当前环境没有 `php` 可执行文件，未能执行本地 `php -l`

## 2026-03-21

### 里程碑：M1-M3 第一阶段收口

- 完成站点方向重构，从传统问答站收敛到“公开、开放、可检索的知识系统”。
- 首页、导航、列表页、详情页、移动端与后台表达统一到 `综述 / 主题 / 观察 / FAQ / 帮助 / 知识地图`。
- 建立知识归档主链路：
  - 发布时可选择知识章节
  - 详情页可展示已归档章节
  - 首页可稳定暴露知识归档入口
- 完成后台知识整理工作流：
  - 可查看未归档 FAQ / 内容
  - 可查看待整理章节
  - 可直接归档到章节
  - 可查看章节候选内容
  - 可查看章节建议关联主题
- 完成知识地图公开结构：
  - 帮助首页升级为知识地图入口
  - 章节页支持 `全部 / FAQ / 知识内容` 真实切换
  - 章节页支持相关主题展示
  - 章节页相关主题改为“真实归档关系优先”
  - 主题页开始接入相关知识章节入口
- 补齐运营洞察与自动化底座：
  - 搜索/曝光/点击/阅读埋点落库
  - 后台运营洞察面板
  - `insight:report` 命令行输出
  - 发布页支持建议标题与建议内容形态
- 完成一批基础工程修复：
  - `sitemap`/`robots`/导航默认配置修正
  - API 入口补齐
  - 若干公开配置和发布接口问题修复
  - 移动端入口与知识化首页打通

### 提交说明

- 从这一版开始，Frelink 改为按里程碑提交中文 commit。
- 每完成一个明确阶段，必须同步更新本日志和 [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)。

## 2026-03-21

### 里程碑：M4 内容冷启动执行闭环

- 后台新增 `内容冷启动进度` 面板，可直接查看：
  - `综述 / 观察 / FAQ / 帮助 / 知识章节` 的目标值、当前值与缺口
  - 最近补齐的内容
  - 当前冷启动整体完成度
- 后台新增 `优先补齐建议`：
  - 自动判断先补哪一类内容
  - 直接提供 `立即处理 / 查看现有内容` 的动作入口
- 后台新增 `本周执行清单`：
  - 结合搜索缺口、内容形态建议与冷启动缺口
  - 直接生成“本周该补哪三篇”的建议标题、关键词、原因与入口
- 后台新增 `执行周报导出`：
  - 支持导出 Markdown
  - 支持导出 JSON
  - 方便站长复盘，也方便 agent 接手执行
- 主题页知识地图入口继续收口：
  - 即使没有匹配到具体章节，也固定保留 `知识地图` 入口
  - 避免因为数据不足导致入口时有时无
- 章节页相关主题关系进一步收实：
  - 优先采用归档内容的真实主题关系，而不是只靠文本猜测

### 影响范围

- 后台：
  - [app/backend/Index.php](/mnt/f/workwww/knowlege-github/app/backend/Index.php)
  - [app/backend/view/index/index.php](/mnt/f/workwww/knowlege-github/app/backend/view/index/index.php)
- 前台：
  - [public/templates/default/html/topic/detail.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/topic/detail.php)
  - [public/templates/default/mobile/topic/detail.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/topic/detail.php)
- 模型与语言：
  - [app/model/Help.php](/mnt/f/workwww/knowlege-github/app/model/Help.php)
  - [app/lang/en-us.php](/mnt/f/workwww/knowlege-github/app/lang/en-us.php)
- 文档：
  - [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)
  - [docs/project-delivery-template.md](/mnt/f/workwww/knowlege-github/docs/project-delivery-template.md)

### 验证

- 远端 `php -l` 已通过：
  - [app/backend/Index.php](/mnt/f/workwww/knowlege-github/app/backend/Index.php)
  - [app/model/Help.php](/mnt/f/workwww/knowlege-github/app/model/Help.php)
  - [app/mobile/Topic.php](/mnt/f/workwww/knowlege-github/app/mobile/Topic.php)
- 服务器已多次执行 `sudo php think clear`
- 公开页面验证已确认：
  - `topic/115` 与 `m/topic/115` 均可稳定看到知识地图入口

### 下一步

- 开始准备 `M5 API / Agent 扩展`
- 先补周报/API 输出规范，再补更正式的 agent 接入边界说明
