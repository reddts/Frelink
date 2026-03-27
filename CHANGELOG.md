# Frelink 项目更新日志

## 2026-03-27（续）

### 里程碑：主题追踪成为可选内容类型

- `主题追踪` 现在已经进入文章内容类型体系，可以在发布页直接选择
- 发布页的写作模板区新增了 `主题追踪` 模板，支持阶段更新、本期变化、旧判断修正和下一步观察点
- 搜索驱动的文章建议现在也能把阶段变化类主题推荐为 `主题追踪`，不再只能落到综述或观察
- 冷启动统计新增了 `主题追踪` 目标，后台也能直接看到这类内容的补齐进度

### 影响范围

- [app/function.inc.php](/mnt/f/workwww/knowlege-github/app/function.inc.php)
- [app/model/Insight.php](/mnt/f/workwww/knowlege-github/app/model/Insight.php)
- [app/lang/en-us.php](/mnt/f/workwww/knowlege-github/app/lang/en-us.php)
- [public/templates/default/html/article/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/article/publish.php)
- [public/templates/default/mobile/article/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/article/publish.php)
- [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 本地 `git diff --check` 已通过
- 本地环境没有 `php` 可执行文件，无法执行本地 `php -l`
- 远程已同步本轮变更文件
- 远程已执行：
  - `php -l app/function.inc.php`
  - `php -l app/model/Insight.php`
  - `php -l app/frontend/Article.php`
  - `php -l app/mobile/Article.php`
  - `sudo php think clear`
- 远程烟测已执行：
  - `https://www.frelink.top/`
  - `https://www.frelink.top/questions/`
  - `https://www.frelink.top/articles/`

## 2026-03-27

### 里程碑：文章发布页接入本周执行清单

- 文章发布页现在会直接展示 `本周优先写作` 卡片，把周执行清单带到写作入口
- 该卡片复用了现有的搜索驱动选题结果，能直接跳转到对应的内容发布页和现有内容列表
- 桌面端与移动端文章发布页已同步接入，避免后台有执行清单、前台却要手动来回切换
- `优化计划.md` 里的周选题待办也补充了当前进展说明，方便继续跟踪后续收口

### 影响范围

- [app/frontend/Article.php](/mnt/f/workwww/knowlege-github/app/frontend/Article.php)
- [app/mobile/Article.php](/mnt/f/workwww/knowlege-github/app/mobile/Article.php)
- [public/templates/default/html/article/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/article/publish.php)
- [public/templates/default/mobile/article/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/article/publish.php)
- [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 本地 `git diff --check` 已通过
- 本地环境没有 `php` 可执行文件，无法执行本地 `php -l`
- 远程已同步本轮变更文件
- 远程已执行：
  - `php -l app/function.inc.php`
  - `php -l app/frontend/Article.php`
  - `php -l app/mobile/Article.php`
  - `sudo php think clear`
- 远程烟测已执行：
  - `https://www.frelink.top/`
  - `https://www.frelink.top/questions/`
  - `https://www.frelink.top/articles/`

## 2026-03-26（续）

### 里程碑：API 文档补充 OpenAPI 导出

- `api:doc` 现已支持 `--format=openapi`，可以从 `app/api/v1` 控制器源码自动生成机器可读的 OpenAPI JSON
- 默认输出路径为 [public/docs/api-v1.openapi.json](/mnt/f/workwww/knowlege-github/public/docs/api-v1.openapi.json)，浏览器和 agent 都可以直接下载
- 帮助页的 API 文档卡片新增了 OpenAPI 下载入口，方便前端、脚本和外部工具直接消费同一份规范
- `docs/api-v1.md` 也补充了 OpenAPI 导出说明，避免人读文档和机器规范脱节
- OpenAPI 导出现在还补了统一返回结构和基础参数类型推断，减少 agent 接入时的歧义
- OpenAPI 路径已按服务器前缀改为相对路径，避免生成出重复的 `/api/api/...` 地址
- 统一返回说明现在还补了成功/失败示例，便于快速区分 `code=0` 和非 0 返回

### 影响范围

- [app/common/command/ApiDoc.php](/mnt/f/workwww/knowlege-github/app/common/command/ApiDoc.php)
- [docs/api-v1.md](/mnt/f/workwww/knowlege-github/docs/api-v1.md)
- [public/templates/default/html/help/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/help/index.php)
- [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 本地 `git diff --check` 已通过
- 远程已同步本轮变更文件
- 远程已执行：
  - `php -l app/common/command/ApiDoc.php`
  - `sudo php think clear`
  - `php think api:doc --format=openapi --output public/docs/api-v1.openapi.json`

### 里程碑：API 接口说明改为自动生成

- 新增 `api:doc` 控制台命令，支持从 `app/api/v1` 控制器源码自动扫描并生成接口说明文档
- 生成产物为 [docs/api-v1.md](/mnt/f/workwww/knowlege-github/docs/api-v1.md)，内容覆盖路由、HTTP 方法、登录要求、参数与简要说明
- 站内新增 `api-docs` 入口，可直接在帮助页打开自动生成的 API 文档
- 这次把接口说明从手工维护改为按代码生成，后续只要接口实现变更，就可以重新执行命令同步更新文档
- `优化计划.md` 的 `M5 API / Agent 扩展` 已补充“自动生成 API 接口说明文档”的推进项，作为后续 agent 接入前置规范

### 影响范围

- [app/common/command/ApiDoc.php](/mnt/f/workwww/knowlege-github/app/common/command/ApiDoc.php)
- [app/common/library/helper/HtmlHelper.php](/mnt/f/workwww/knowlege-github/app/common/library/helper/HtmlHelper.php)
- [app/frontend/Page.php](/mnt/f/workwww/knowlege-github/app/frontend/Page.php)
- [config/console.php](/mnt/f/workwww/knowlege-github/config/console.php)
- [docs/api-v1.md](/mnt/f/workwww/knowlege-github/docs/api-v1.md)
- [public/templates/default/html/help/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/help/index.php)
- [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)
- [route/app.php](/mnt/f/workwww/knowlege-github/route/app.php)

### 验证

- 本地 `git diff --check` 已通过
- 远程已同步本轮变更文件
- 远程已执行：
  - `php -l app/common/command/ApiDoc.php`
  - `php -l app/common/library/helper/HtmlHelper.php`
  - `php -l app/frontend/Page.php`
  - `php -l config/console.php`
  - `php -l route/app.php`
  - `sudo php think clear`
  - `sudo php think api:doc --output docs/api-v1.md`

### 里程碑：周执行清单增加观察整理任务

- 周执行清单现在会额外从近期表现较好的 `观察 / 碎片` 内容中挑出可升级条目
- 对应任务会直接指向新的文章发布入口，并保留原观察链接，方便把碎片进一步整理为综述、帮助或阶段性追踪
- 这次把碎片筛选门槛略微放宽，优先保证真实有阅读势能的观察不会长期被漏掉
- 当最近窗口没有明显热点碎片时，会自动回退到站内最新的观察内容，避免整理任务长期空着
- 这一步是对“将优质思想碎片定期整理为综述、专题或帮助文档”的执行层补强

### 影响范围

- [app/model/Insight.php](/mnt/f/workwww/knowlege-github/app/model/Insight.php)

### 验证

- 本地 `git diff --check` 已通过
- 本地环境无 `php` 可执行文件
- 远程已同步本轮变更文件
- 远程已执行：
  - `php -l app/model/Insight.php`
  - `sudo php think clear`
  - `sudo php think insight:report --days=7 --limit=3 --format=markdown`

## 2026-03-26

### 里程碑：本周执行清单接入搜索驱动选题

- `Insight::getWeeklyExecutionPlan()` 现在会优先从站内高频搜索词生成 1-2 个本周选题，补足冷启动目标之外的持续内容生产入口
- 已过滤纯数字、年份和明显噪音搜索词，避免扫描流量污染周选题
- 周执行任务键已做更严格的关键词归一化，避免同词重复生成两条任务
- 后台运营面板的本周执行卡片补充展示：
  - `task_type`
  - `content_type`
  - `source_key`
- `insight:report` 现已同步输出周执行清单，方便直接复制给 agent 或人工周会使用
- 目标是让“搜索词 -> 选题 -> 发布动作”的链路在后台和命令行里都能直接看到，不再只停留在数据统计层

### 影响范围

- [app/model/Insight.php](/mnt/f/workwww/knowlege-github/app/model/Insight.php)
- [app/backend/view/index/index.php](/mnt/f/workwww/knowlege-github/app/backend/view/index/index.php)
- [app/common/command/InsightReport.php](/mnt/f/workwww/knowlege-github/app/common/command/InsightReport.php)

### 验证

- 本地 `git diff --check` 已通过
- 本地环境无 `php` 可执行文件
- 远程已同步本轮变更文件
- 远程已执行：
  - `php -l app/model/Insight.php`
  - `php -l app/backend/view/index/index.php`
  - `php -l app/common/command/InsightReport.php`
  - `sudo php think clear`
- 远程已执行 `php think insight:report --days=7 --limit=3 --format=markdown` 作为输出回归检查

## 2026-03-24

### 里程碑：移动端次级大图延迟加载

- 移动端首页与两个分类页的“热门用户 / 热门话题”大图已补齐 `loading="lazy"` 和 `decoding="async"`
- 这类图片通常处于首屏下方或折叠区，延迟加载后可以减少首轮并发请求
- 目标是继续压缩移动端首页、文章页、问题页的图片竞争

### 影响范围

- [public/templates/default/mobile/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/index.php)
- [public/templates/default/mobile/article/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/article/index.php)
- [public/templates/default/mobile/question/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/question/index.php)

### 验证

- 本地 `git diff --check` 待复核
- 远程同步后将执行：
  - `sudo php think clear`
  - 模板内容抽查

## 2026-03-24

### 里程碑：高频列表组件补齐图片懒加载

- 桌面端高频列表组件已补齐图片懒加载和异步解码：
  - `widget/render/articles`
  - `widget/render/questions`
  - `widget/member/posts`
- 移动端高频 Ajax 列表已同步补齐：
  - `mobile/ajax/lists`
  - `mobile/search/ajax_search`
- 这轮优化主要覆盖：
  - 头像图片
  - 列表封面图
  - 空态图
  - 主题图
- 目标是继续减少首轮并发请求，降低列表页和搜索页的图片阻塞

### 影响范围

- 桌面端：
  - [public/templates/default/html/widget/render/articles.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/widget/render/articles.php)
  - [public/templates/default/html/widget/render/questions.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/widget/render/questions.php)
  - [public/templates/default/html/widget/member/posts.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/widget/member/posts.php)
- 移动端：
  - [public/templates/default/mobile/ajax/lists.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/ajax/lists.php)
  - [public/templates/default/mobile/search/ajax_search.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/search/ajax_search.php)

### 验证

- 本地 `git diff --check` 待复核
- 本地当前环境无 `php`，无法执行本地语法检查
- 远程同步后将执行：
  - `php -l` 相关模板 / 逻辑文件
  - `sudo php think clear`

## 2026-03-24

### 里程碑：发布页增加内容质量检查卡片

- 发布页新增 `发布前检查` 卡片，将内容策略里的 4 条筛选标准前置到作者写作现场：
  - 3 秒内能否看出与自己有关
  - 30 秒内能否获得一个新判断
  - 看完后是否愿意继续点下一篇
  - 标题是否真实反映正文
- 桌面端与移动端的 `FAQ 条目`、`综述 / 观察` 发布页均已同步接入
- 同时保留了此前的发布推荐修复：
  - `suggested_topics` 里缺少 `url` 时不再报错
  - 友情链接与插件发布配置增加了 `url` 兜底

### 影响范围

- 发布页模板：
  - [public/templates/default/html/question/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/question/publish.php)
  - [public/templates/default/html/article/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/article/publish.php)
  - [public/templates/default/mobile/question/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/question/publish.php)
  - [public/templates/default/mobile/article/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/article/publish.php)
- 兜底与校验：
  - [app/common/library/helper/PluginsHelper.php](/mnt/f/workwww/knowlege-github/app/common/library/helper/PluginsHelper.php)
  - [app/widget/Common.php](/mnt/f/workwww/knowlege-github/app/widget/Common.php)
  - [public/templates/default/html/widget/common/links.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/widget/common/links.php)

### 验证

- 本地 `git diff --check` 已通过
- 本地当前环境无 `php`，无法执行本地语法检查
- 远程已定向同步本轮改动文件，随后执行了：
  - `php -l app/common/library/helper/PluginsHelper.php`
  - `php -l app/widget/Common.php`
  - `php -l config/version.php`
  - `sudo php think clear`

## 2026-03-24

### 里程碑：M3 知识地图升级为长期主题容器

- 知识地图首页不再只是帮助章节列表，已新增 `长期主题连接` 聚合区：
  - 展示已经和知识章节建立真实归档关系的主题
  - 每个主题可直接看到已连接章节数、已归档内容数和代表章节入口
- 知识章节卡片已补充真实覆盖结构：
  - `FAQ 条目`
  - `知识内容`
  - `相关主题`
  - 同时增加章节角色说明，明确当前更像答案入口、知识归档位还是双向容器
- 章节详情页已补充：
  - 顶部章节定位说明
  - `FAQ / 知识内容 / 全部内容` 三种浏览提示
  - 让用户更容易理解这个章节该怎么继续往下读
- `优化计划.md` 已同步回填：
  - `将知识章节从帮助展示页升级为知识地图与长期主题容器`

### 影响范围

- 前台控制器：
  - [app/frontend/Help.php](/mnt/f/workwww/knowlege-github/app/frontend/Help.php)
- 模型与语言：
  - [app/model/Help.php](/mnt/f/workwww/knowlege-github/app/model/Help.php)
  - [app/lang/en-us.php](/mnt/f/workwww/knowlege-github/app/lang/en-us.php)
- 模板与文档：
  - [public/templates/default/html/help/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/help/index.php)
  - [public/templates/default/html/help/detail.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/help/detail.php)
  - [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 本地 `git diff --check` 已通过
- 当前环境无本地 `php` 可执行文件，未能执行本地 `php -l`
- 远程标准部署脚本全量 `rsync` 时命中历史只读文件权限限制，未采用全量同步结束本轮
- 远程已定向同步本轮改动文件：
  - `app/frontend/Help.php`
  - `app/lang/en-us.php`
  - `app/model/Help.php`
  - `public/templates/default/html/help/index.php`
  - `public/templates/default/html/help/detail.php`
- 远程已执行：
  - `php -l app/frontend/Help.php`
  - `php -l app/model/Help.php`
  - `sudo php think clear`
- 线上页面验证已通过：
  - `https://www.frelink.top/help/index.html` 已输出 `已连接主题`
  - `https://www.frelink.top/help/index.html` 已输出 `长期主题连接`
- 当前线上知识地图首页暂无公开章节数据，因此未能继续抽样章节详情页做 smoke test；页面当前仍返回空态 `暂无内容`

## 2026-03-23

### 里程碑：M1 修正首页主内容区宽度未拉满

- 已确认首页宽度问题的根因不在栅格类，而在全局样式对 `.aw-left` 的固定限制：
  - `max-width: 73.6%`
  - `flex: 0 0 73.6%`
- 首页模板现已增加更强的页面级覆盖规则：
  - `.aw-home-main-shell .aw-left { max-width: 100% !important; flex: 0 0 100% !important; }`
- 这样首页在移除右侧整列后，主内容区会真正吃满整行，不再留下旧侧栏宽度的空白

### 影响范围

- 首页模板：
  - [public/templates/default/html/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/index.php)
- 文档：
  - [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 本地 `git diff --check` 已通过
- 远程已定向同步：
  - `public/templates/default/html/index.php`
- 远程已执行：
  - `sudo php think clear`
  - `php -l public/templates/default/html/index.php`
- 线上首页 HTML 已确认输出：
  - `.aw-home-main-shell .aw-left`
  - `max-width: 100% !important`
  - `flex: 0 0 100% !important`
  - `col-md-12 bg-white mb-2 aw-home-primary-column`

### 里程碑：M1 首页右侧整列下线

- 首页右侧整列已整体移除，主内容列改为满宽布局
- 首页不再渲染右侧的：
  - 快速发起
  - 关注话题
  - 热门专栏
  - 热门用户
  - 自定义内容
- 首页主题配置默认值已同步收口：
  - `sidebar_show_items` 默认只保留 `announce`
  - `sidebar_diy_content` 默认内容已清空
- 页面中原先可能出现的 `这是自定义内容` 占位内容已从首页默认配置中移除

### 影响范围

- 首页模板：
  - [public/templates/default/html/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/index.php)
- 主题配置：
  - [public/templates/default/config.php](/mnt/f/workwww/knowlege-github/public/templates/default/config.php)
  - [public/templates/default/info.php](/mnt/f/workwww/knowlege-github/public/templates/default/info.php)
- 文档：
  - [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 本地 `git diff --check` 已通过
- 远程已定向同步：
  - `public/templates/default/html/index.php`
  - `public/templates/default/config.php`
  - `public/templates/default/info.php`
- 远程已执行：
  - `sudo php think clear`
  - `php -l public/templates/default/html/index.php`
- 线上首页 HTML 已确认：
  - 主列类名已变为 `col-md-12`
  - 页面中不再出现 `aw-home-sidebar`
  - 页面中不再出现 `这是自定义内容`

### 里程碑：M1 首页信息层级收口

- 首页主列已经保留唯一一组 `核心主题` 卡片，不再与右侧侧栏重复展示同一组主题
- `公告与更新` 已从首页右侧高频区域下沉到页面底部，转为低频信息区
- 首页右侧侧栏现在优先保留高频浏览与分流能力，慢更新内容不再抢占主阅读注意力

### 影响范围

- 首页模板：
  - [public/templates/default/html/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/index.php)
- 语言：
  - [app/lang/en-us.php](/mnt/f/workwww/knowlege-github/app/lang/en-us.php)
- 文档：
  - [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 本地 `git diff --check` 已通过
- 远程已定向同步：
  - `public/templates/default/html/index.php`
  - `app/lang/en-us.php`
- 远程已执行：
  - `sudo php think clear`
  - `php -l public/templates/default/html/index.php`
- 线上首页 HTML 已确认：
  - `核心主题` 仅保留主列一处
  - `公告与更新` 已出现在首页底部低频区

### 里程碑：M1 综述与观察聚合入口前置

- 文章总入口已新增 `综述 / 观察` 双入口聚合卡，前置站点的两条主内容生产线
- 这组入口同时覆盖桌面端与移动端，不新增一级主导航，但把 `观察` 稳定提升为公开可见的聚合入口
- 聚合卡会直接展示每种内容形态的：
  - 当前累计篇数
  - 最近更新内容
  - 进入对应聚合页的快捷动作
- `优化计划.md` 已同步勾选：
  - `综述与观察承接主要内容生产，成为站点核心资产`
  - `新增 思想碎片 标签体系或专题聚合入口，不新增一级主导航`

### 影响范围

- 公共函数：
  - [app/function.inc.php](/mnt/f/workwww/knowlege-github/app/function.inc.php)
- 控制器：
  - [app/frontend/Article.php](/mnt/f/workwww/knowlege-github/app/frontend/Article.php)
  - [app/mobile/Article.php](/mnt/f/workwww/knowlege-github/app/mobile/Article.php)
- 模板与语言：
  - [public/templates/default/html/article/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/article/index.php)
  - [public/templates/default/mobile/article/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/article/index.php)
  - [app/lang/en-us.php](/mnt/f/workwww/knowlege-github/app/lang/en-us.php)
  - [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 本地 `git diff --check` 已通过
- 远程已定向同步：
  - `app/function.inc.php`
  - `app/frontend/Article.php`
  - `app/mobile/Article.php`
  - `app/lang/en-us.php`
  - `public/templates/default/html/article/index.php`
  - `public/templates/default/mobile/article/index.php`
- 远程已执行：
  - `sudo php think clear`
  - `php -l app/function.inc.php`
  - `php -l app/frontend/Article.php`
  - `php -l app/mobile/Article.php`
- 线上页面验证已通过：
  - 桌面端 `https://www.frelink.top/articles/` 已输出 `综述 / 观察` 主内容入口卡
  - 移动端 `https://www.frelink.top/articles/` 已输出 `综述 / 观察` 主内容入口卡
  - 当前线上 `research / fragment` 统计均为 `0`，说明入口已上线，但仍需要后续内容冷启动补量

### 里程碑：M1 中英文公开语义回归检查与漏翻修复

- 已完成首页、FAQ、文章、观察、帮助、主题页的中英文抽样回归
- 修复了一批代码层遗留的公开中文标题与文案：
  - FAQ 列表标题统一改为 `FAQ - Answer entry`
  - 文章列表标题统一改为 `Knowledge library`
  - 观察专题标题统一改为 `Observation track - Long-term topic observation`
  - 移动端首页 `最近有人在搜` 改为走语言包输出
- 英文语言包已补齐这一轮回归发现的缺口：
  - `观察专题`
  - `长期更新`
  - `保留判断`
  - `答案入口`
  - `知识内容库`
  - `公开、开放、可检索的知识系统`
  - `让真正有价值的思想被看见`
- `优化计划.md` 已同步勾选：
  - `改造完成后，补一次中文/英文双语回归检查`
- 本轮回归也确认：英文页剩余中文主要来自站点 SEO 配置、分类名称和内容数据，不属于模板漏翻

### 影响范围

- 前台控制器：
  - [app/frontend/Feature.php](/mnt/f/workwww/knowlege-github/app/frontend/Feature.php)
  - [app/frontend/Question.php](/mnt/f/workwww/knowlege-github/app/frontend/Question.php)
  - [app/frontend/Article.php](/mnt/f/workwww/knowlege-github/app/frontend/Article.php)
  - [app/mobile/Question.php](/mnt/f/workwww/knowlege-github/app/mobile/Question.php)
  - [app/mobile/Article.php](/mnt/f/workwww/knowlege-github/app/mobile/Article.php)
- 模板与语言：
  - [public/templates/default/mobile/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/index.php)
  - [app/lang/en-us.php](/mnt/f/workwww/knowlege-github/app/lang/en-us.php)
  - [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 本地 `git diff --check` 已通过
- 远程已定向同步：
  - `app/frontend/Feature.php`
  - `app/frontend/Question.php`
  - `app/frontend/Article.php`
  - `app/mobile/Question.php`
  - `app/mobile/Article.php`
  - `app/lang/en-us.php`
  - `public/templates/default/mobile/index.php`
- 远程已执行：
  - `sudo php think clear`
  - `php -l app/frontend/Feature.php`
  - `php -l app/frontend/Question.php`
  - `php -l app/frontend/Article.php`
  - `php -l app/mobile/Question.php`
  - `php -l app/mobile/Article.php`
- 线上英文页抽样验证已通过：
  - `https://www.frelink.top/questions/?lang=en-us` 标题已输出 `FAQ - Answer entry`
  - `https://www.frelink.top/articles/?lang=en-us` 标题已输出 `Knowledge library`
  - `https://www.frelink.top/feature/index.html?lang=en-us` 标题已输出 `Observation track - Long-term topic observation`
  - 移动 UA `https://www.frelink.top/?lang=en-us` 已确认输出 `An open, searchable public knowledge system`、`People recently searched`

### 里程碑：M1 移动端底部导航收敛到知识消费主路径

- 移动端底部导航已进一步收敛为公开知识消费路径：
  - `首页 / 主题 / FAQ / 综述 / 观察`
- 底部最后一项不再继续占用 `帮助` 入口，改为直达 `观察专题`
- 移动端主导航现在和首页四个主入口的内容结构更一致，优先承接 `FAQ / 综述 / 观察 / 主题`
- `优化计划.md` 已同步勾选：
  - `移动端底部导航收敛为内容消费路径，不保留与知识消费弱相关入口`

### 影响范围

- 移动端公共模板：
  - [public/templates/default/mobile/block.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/block.php)
- 文档：
  - [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 本地部署配置检查已通过：
  - `bash scripts/deploy.sh show-config`
- 当前环境无本地 `php` 可执行文件，未能执行本地 `php -l`
- 远程已定向同步：
  - `public/templates/default/mobile/block.php`
- 远程已执行：
  - `sudo php think clear`
  - `php -l public/templates/default/mobile/block.php`
- 线上移动 UA 页面验证已通过：
  - `https://www.frelink.top/` 返回 `HTTP 200`
  - 首页移动端 HTML 已确认底部导航输出 `FAQ / 综述 / 观察`

### 里程碑：M1 默认导航收敛到知识消费主路径

- 桌面端默认一级导航已按知识消费路径重排为：
  - `首页 / 主题 / FAQ / 综述 / 观察 / 帮助`
- `专栏 / 创作者` 不再占用默认一级导航位置，保留为非默认入口
- 公共 helper 新增统一导航排序逻辑，避免不同导航模板继续各自维护顺序
- `优化计划.md` 已同步回填：
  - `默认导航顺序调整为 首页 / 主题 / 问题 / 文章 / 专题 / 帮助中心`
  - `将 专栏 / 创作者 从默认一级导航中隐藏`

### 影响范围

- 公共函数：
  - [app/function.inc.php](/mnt/f/workwww/knowlege-github/app/function.inc.php)
- 桌面导航模板：
  - [public/templates/default/html/block.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/block.php)
  - [public/templates/default/html/global/nav.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/global/nav.php)
- 文档：
  - [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 本地 `git diff --check` 已通过
- 远程已定向同步：
  - `app/function.inc.php`
  - `public/templates/default/html/block.php`
  - `public/templates/default/html/global/nav.php`
  - `优化计划.md`
- 远程已执行：
  - `sudo php think clear`
  - `php -l app/function.inc.php`
- 线上页面验证已通过：
  - `https://www.frelink.top/` 返回 `HTTP 200`
  - `https://www.frelink.top/topic/index.html` 返回 `HTTP 200`
  - `https://www.frelink.top/help/index.html` 返回 `HTTP 200`
- 线上首页 HTML 已确认默认一级导航输出为 `主题 / FAQ / 综述 / 观察 / 帮助`，未再出现 `专栏 / 创作者`

### 里程碑：M1 专题页按内容类型聚合

- 观察专题页不再只按 `观察动态 / 热门内容 / 最佳回复` 这类来源或热度维度浏览
- 桌面端与移动端都新增了内容形态筛选：
  - `全部内容 / FAQ / 综述 / 观察 / 帮助`
- 数据层已改为按真实内容类型筛选专题下的聚合结果：
  - `question`
  - `article_type=research`
  - `article_type=fragment`
  - `article_type in (faq, tutorial)`
- `优化计划.md` 已同步回填 `后续专题页按内容类型做聚合，而不是只按来源聚合`

### 影响范围

- 模型与控制器：
  - [app/model/Feature.php](/mnt/f/workwww/knowlege-github/app/model/Feature.php)
  - [app/frontend/Feature.php](/mnt/f/workwww/knowlege-github/app/frontend/Feature.php)
  - [app/mobile/Feature.php](/mnt/f/workwww/knowlege-github/app/mobile/Feature.php)
  - [app/mobile/Ajax.php](/mnt/f/workwww/knowlege-github/app/mobile/Ajax.php)
  - [app/widget/Common.php](/mnt/f/workwww/knowlege-github/app/widget/Common.php)
- 模板与语言：
  - [public/templates/default/html/feature/detail.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/feature/detail.php)
  - [public/templates/default/mobile/feature/detail.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/feature/detail.php)
  - [app/lang/en-us.php](/mnt/f/workwww/knowlege-github/app/lang/en-us.php)
  - [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 本地 `git diff --check` 已通过
- 远程已补齐同步：
  - `app/model/Feature.php`
  - `app/frontend/Feature.php`
  - `app/mobile/Feature.php`
  - `app/mobile/Ajax.php`
  - `app/widget/Common.php`
  - `public/templates/default/html/feature/detail.php`
  - `public/templates/default/mobile/feature/detail.php`
  - `app/lang/en-us.php`
- 远程已执行：
  - `sudo php think clear`
  - `php -l app/model/Feature.php`
  - `php -l app/frontend/Feature.php`
  - `php -l app/mobile/Feature.php`
  - `php -l app/mobile/Ajax.php`
  - `php -l app/widget/Common.php`
- 线上页面验证：
  - 桌面 UA `https://www.frelink.top/feature/index.html` 返回 `HTTP 200`
  - 移动 UA `https://www.frelink.top/feature/index.html` 返回 `HTTP 200`
- 当前线上 `kn_feature` 表记录数为 `0`，没有可公开访问的专题详情 token，因此详情页 smoke test 暂无法执行

### 里程碑：M1 移动端推荐链路与内容类型状态回填

- 移动端文章详情页与 FAQ 详情页的 `下一步阅读` 已同步采用内容形态优先排序，和桌面端保持一致
- `优化计划.md` 已按现有代码事实回填两项状态：
  - `给文章增加内容类型字段或至少增加统一标签`
  - `后续为文章增加内容类型标识：研究综述 / 思想碎片 / 教程 / FAQ`

### 影响范围

- 移动控制器：
  - [app/mobile/Article.php](/mnt/f/workwww/knowlege-github/app/mobile/Article.php)
  - [app/mobile/Question.php](/mnt/f/workwww/knowlege-github/app/mobile/Question.php)
- 文档：
  - [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 本地 `git diff --check` 已通过
- 远程已定向同步：
  - `app/mobile/Article.php`
  - `app/mobile/Question.php`
- 远程已执行：
  - `sudo php think clear`
  - `php -l app/mobile/Article.php`
  - `php -l app/mobile/Question.php`
- 移动 UA 线上页面验证已通过：
  - `https://www.frelink.top/questions/new-0.html`
  - `https://www.frelink.top/articles/new-0.html?type=all`

### 里程碑：M1 推荐链路按内容形态收口

- 文章详情页与 FAQ 详情页的 `下一步阅读` 已改为优先按内容形态串联：
  - `热点解释 -> 研究综述 -> FAQ/帮助 -> FAQ -> 观察`
- 侧栏 `推荐内容` 中的文章项不再只显示泛化的 `文`，而是直接显示真实内容类型标签
- 公共 helper 新增推荐排序与分组逻辑，统一处理推荐内容的优先级与标签
- `优化计划.md` 已同步回填 `站内推荐优先串联：热点解释 -> 研究综述 -> FAQ/帮助`

### 影响范围

- 公共函数：
  - [app/function.inc.php](/mnt/f/workwww/knowlege-github/app/function.inc.php)
- 控制器：
  - [app/frontend/Article.php](/mnt/f/workwww/knowlege-github/app/frontend/Article.php)
  - [app/frontend/Question.php](/mnt/f/workwww/knowlege-github/app/frontend/Question.php)
- 详情页模板：
  - [public/templates/default/html/article/detail.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/article/detail.php)
  - [public/templates/default/html/question/detail.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/question/detail.php)
- 文档：
  - [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 本地 `git diff --check` 已通过
- 远程已定向同步以下文件到 `/www/wwwroot/knoledge`：
  - `app/function.inc.php`
  - `app/frontend/Article.php`
  - `app/frontend/Question.php`
  - `public/templates/default/html/article/detail.php`
  - `public/templates/default/html/question/detail.php`
- 远程已执行：
  - `sudo php think clear`
  - `php -l app/function.inc.php`
  - `php -l app/frontend/Article.php`
  - `php -l app/frontend/Question.php`
- 线上页面验证已通过：
  - `https://www.frelink.top/questions/new-0.html`
  - `https://www.frelink.top/articles/new-0.html?type=all`

### 里程碑：M0 本地标准部署环境建立

- 已建立项目级本地部署环境：
  - 新增标准脚本 `bash scripts/deploy.sh [show-config|sync|verify|deploy|shell]`
  - 新增本地私有配置 `deploy.local.json`，并加入 `.gitignore`
  - 新增 `deploy.local.json.example`、`.deployignore`、`docs/deploy.local.example.md`
- 已接入当前服务器信息：
  - SSH 用户：`azureuser`
  - 主机：`20.191.157.253`
  - 项目路径：`/www/wwwroot/knoledge`
- 已完成一次真实远程环境验证：
  - SSH 可连接，项目目录存在
  - 远程 `php -l` 已通过：
    - `app/function.inc.php`
    - `app/frontend/Article.php`
  - 远程缓存清理已通过：
    - `sudo php think clear`
  - smoke test 已通过：
    - `/`
    - `/questions/`
    - `/articles/`

### 影响范围

- 部署脚本与配置模板：
  - [scripts/deploy.sh](/mnt/f/workwww/knowlege-github/scripts/deploy.sh)
  - [deploy.local.json.example](/mnt/f/workwww/knowlege-github/deploy.local.json.example)
  - [.deployignore](/mnt/f/workwww/knowlege-github/.deployignore)
- 文档与规则：
  - [README.md](/mnt/f/workwww/knowlege-github/README.md)
  - [docs/deploy.local.example.md](/mnt/f/workwww/knowlege-github/docs/deploy.local.example.md)
  - [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)
  - [.gitignore](/mnt/f/workwww/knowlege-github/.gitignore)

### 验证

- `bash scripts/deploy.sh show-config` 已通过
- `bash scripts/deploy.sh verify` 已通过
- `git diff --check` 已通过

### 里程碑：M1 研究综述模板落地

- 文章发布页新增 `写作模板` 区块，可一键插入：
  - `研究综述模板`
  - `观察记录模板`
- `研究综述模板` 已按计划中的固定结构落地为可直接写作的编辑器骨架：
  - `背景 / 核心问题 / 资料来源 / 分歧点 / 当前判断 / 待验证`
- 插入模板时会同步切换对应内容类型，并把编辑器焦点引到正文区域，减少从空白页起手的成本
- `优化计划.md` 已同步回填 `新增 研究综述 文章模板`

### 影响范围

- 发布页模板：
  - [public/templates/default/html/article/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/article/publish.php)
- 语言与文档：
  - [app/lang/en-us.php](/mnt/f/workwww/knowlege-github/app/lang/en-us.php)
  - [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- `git diff --check` 已通过
- 当前环境无 `php` 可执行文件，未能执行本地 `php -l`
- 当前仓库未发现可直接使用的服务器连接信息或部署脚本，本轮未完成远程服务器同步与验证

### 里程碑：M1 发布入口类型提示继续收口

- 发布入口新增统一内容形态提示：
  - 公共 helper 新增 `frelink_publish_type_scene()`，统一输出 FAQ / 综述 / 观察 / 帮助 / 热点解释 / 方法 的使用场景
  - 文章发布页的类型说明从静态三卡片扩展为更完整的形态提示，并跟随当前选择的内容类型实时更新
  - FAQ 发布页新增“如果这条内容更像综述 / 观察，就改走知识内容发布”的分流入口，减少入口选错
- 文章发布页现在支持通过查询参数预选 `article_type`，方便从 FAQ 发布页直接切换到 `综述` 或 `观察`
- `优化计划.md` 同步回填“发布页建议中补更适合写成哪一类内容的提示”相关待办

### 影响范围

- 公共函数：
  - [app/function.inc.php](/mnt/f/workwww/knowlege-github/app/function.inc.php)
- 控制器：
  - [app/frontend/Article.php](/mnt/f/workwww/knowlege-github/app/frontend/Article.php)
- 发布页模板：
  - [public/templates/default/html/article/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/article/publish.php)
  - [public/templates/default/html/question/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/question/publish.php)
- 语言与文档：
  - [app/lang/en-us.php](/mnt/f/workwww/knowlege-github/app/lang/en-us.php)
  - [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- `git diff --check` 已通过
- 当前环境无 `php` 可执行文件，未能执行本地 `php -l`
- 当前仓库未发现可直接使用的服务器连接信息或部署脚本，本轮未完成远程服务器同步与验证

### 里程碑：M1 首页主入口继续收口

- 首页主入口卡片从 `综述 / 主题 / 观察` 补全为 `综述 / 主题 / 观察 / FAQ`
- FAQ 入口明确回到首页第一层结构，不再只作为次级列表或旧问答语义残留
- `优化计划.md` 同步回填：
  - `首页主结构改成 综述 / 主题 / 观察 / FAQ`
  - `FAQ 用于承接检索和高频问题，不再继续强化社区问答氛围`

### 影响范围

- 首页模板：
  - [public/templates/default/html/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/index.php)
- 文档：
  - [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- `git diff --check` 已通过
- 当前仓库未发现可直接使用的服务器连接信息或部署脚本，本轮未完成远程服务器同步与验证

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
