# Frelink 项目更新日志

## 2026-03-31

### 里程碑：知识内容摘要列表字号微调

- 知识内容详情页 `aw-article-brief li` 已去掉单项 `margin-bottom` 与 `line-height`，并把字号收口为 `0.9rem`
- 这次调整只细修 `30 秒看懂` 摘要列表的文字密度，不改摘要卡本身的结构、颜色和容器间距

### 里程碑：知识内容入口卡片外边距微调

- 知识内容页 `aw-knowledge-spotlights` 的外层 padding 已从上一轮较宽的容器留白收口为 `0 10px 18px`
- 这次调整只细修知识内容页入口卡片区与容器边界的贴合关系，不改卡片本身的结构、层级和链接逻辑

### 里程碑：FAQ / 知识内容入口卡片对齐主题页

- FAQ 页的 `aw-faq-lanes` 已收口到和 `aw-topic-lanes` 一致的强调条渐变，入口卡片不再保留单独一套配色语言
- 知识内容页的 `aw-knowledge-spotlights` 已改成和 `aw-topic-lane` 同一套卡片节奏：统一最小高度、内边距、圆角、背景渐变、阴影和顶部强调条
- `aw-knowledge-spotlight` 标题已从 badge 式强调收回到和主题卡片一致的标题层级，避免 FAQ / 主题 / 知识内容三页入口卡继续各说各话

### 里程碑：桌面端主题页 Swiper 初始化报错修复

- 桌面端公共模板新增 `__whenSwiperReady`，统一等待 `Swiper` 依赖真正就绪后再执行页面内联初始化
- 主题页、个人页和桌面分类组件已改为走同一套延迟初始化逻辑，不再在 `swiper.min.js` 仍处于 `defer` 加载阶段时直接调用 `new Swiper(...)`
- 这次调整只修复桌面端 `Swiper is not defined` 报错链路，不改导航结构、筛选逻辑和滑动参数

### 里程碑：PC 知识内容页视觉层级再收口

- PC 端知识内容聚合页的 hero 已收口为和 FAQ / 主题页一致的直角版式，不再保留独立圆角矩形造成跨页割裂
- 知识内容详情页已继续降低头部 `30 秒看懂` 与文章标题的视觉权重，避免首屏注意力被头部摘要区抢走
- `下一步阅读` 已改为与正文明显分隔的独立区块，增加上边距，并把区块标题与条目标题字号压回到低于文章主标题的层级

### 里程碑：PC 知识内容页头部冗余卡片收口

- PC 端知识内容页已移除 hero 下方那组三张说明卡，不再连续叠加 `最新更新 / 高关注内容 / 观察专题` 第二层入口
- 头部现在只保留页头说明和 `综述 / 观察` 主入口卡，随后直接进入排序与分类筛选，减少首屏重复表达
- 这次调整只收口 PC 端知识内容页头部结构，不改筛选参数、列表查询和右侧栏逻辑

### 里程碑：PC 知识内容主入口标题样式收口

- PC 端知识内容页的 `综述 / 观察` 主入口卡已移除“主内容入口”文案
- 原来用于“主内容入口”的 badge 样式已转给 `综述 / 观察` 标题本身，减少冗余文字但保留入口强调层级
- 这次调整只影响 PC 端知识内容页主入口卡的视觉表达，不改链接、说明文案和数据展示

### 里程碑：PC 知识内容页头部版式对齐 FAQ / 主题

- PC 端知识内容页的版头已切到和 FAQ、主题列表页一致的 atlas hero 语言，补齐深色渐变背景与 chips 结构
- `全部内容` 作为公开页头标题已收口为 `知识内容`，避免首页、导航和内容页继续混用两套公开叫法
- `综述 / 观察` 主入口卡已改为一行两列，直接贴合内容列表区域宽度，不再沿用三列栅格留下空位

### 里程碑：移动端知识内容筛选滚动与底部导航图标修复

- 移动端知识内容页的筛选带已改为局部覆盖 `swiper` 容器宽度、wrapper 最小宽度和 tab 高度约束，`综述 / 观察` 子页中的 `更新 / 精选 / 高关注 / 分类` 现在可以正常横向滑动
- 移动端底部导航的双态图标从“透明叠放切换”收口为“显隐切换”，避免个别导航项在移动端同时绘制 outline 和 filled 两个图标
- 这轮调整只收口移动端公共样式和知识内容列表筛选结构，不改文章查询、分类参数和页面路由

### 里程碑：移动端底部导航改为单图标渲染

- 移动端底部导航已进一步从“双层图标切换”收口为“单图标渲染”，每个导航项只输出一个 `<i>`，激活态直接在模板层切换图标类
- `首页 / 主题 / FAQ / 知识内容 / 观察专题` 现在都不再依赖两层 iconfont 叠加，因此可以彻底避免某一项同时出现两个图标
- 这次调整只影响移动端底部导航模板和对应公共样式，不改跳转地址、文案和 active 判定逻辑

### 里程碑：移动端导航缺失图标修复

- 移动端底部导航未选中态的 `主题` 和 `观察专题` 已切换到稳定可见的 `icon-huati1 / icon-zhuanlan1` 字形
- 这两个入口现在通过颜色区分选中与未选中状态，不再依赖之前那两个在部分设备上会缺失的图标字形
- 这次调整只影响移动端底部导航的两个图标类，不改其它入口的图标和交互

### 里程碑：移动端首页图标尺寸对齐

- 移动端底部导航的首页图标已从未选中态的 `icon-shouye` 收口为稳定字形 `icon-shouye1`
- 首页现在和 `主题 / 知识内容 / 观察专题` 一样，主要通过颜色区分选中与未选中状态，不再因为 outline 字形本身过大导致视觉尺寸不一致
- 这次调整只影响移动端底部导航首页的图标类，不改其它导航项

### 里程碑：移动端内容详情页排版收口

- 移动端文章详情和 FAQ 详情现在统一接入 `aw-mobile-detail-*` 这一套详情页样式类，页头信息、作者区、摘要卡、延展阅读和归档面板的间距与层级已统一
- 正文区改为详情页作用域下的专用排版：统一字号、行高、标题层级、列表缩进、引用、代码块、表格横向滚动和图片圆角，减少旧样式在手机端的拥挤感
- 底部评论输入框也同步收口成移动端更稳定的圆角输入样式，避免正文样式升级后底部交互区仍显得割裂

### 里程碑：移动端正文节奏与摘要高亮再收口

- 移动端内容详情页进一步把标题和正文行高拆开明确：标题组统一提高到 `1.62`，正文段落、列表、引用和容器文本统一收口到 `1.82`
- 这次补齐了正文首尾段落的 margin 收口，减少富文本结构里 `p / div / li / blockquote` 混排时出现的忽松忽紧
- `30 秒看懂` 现在恢复为更突出的高亮摘要卡，增加左侧强调边、蓝色高亮标题胶囊和更明显的背景层次

### 验证

- 本地已复核 `public/templates/default/mobile/article/index.php` 与 `public/templates/default/static/mobile/css/app.css` diff，确认改动仅覆盖筛选条和底部导航图标显示逻辑
- 本地环境无 `php`，未执行本地 `php -l`，语法检查已放到远端执行
- 生产环境已执行 `bash scripts/deploy.sh deploy`
- 远端已执行 `php -l app/function.inc.php`，结果为 `No syntax errors detected in app/function.inc.php`
- 远端已执行 `sudo php think clear`
- 远端已执行 `sudo php think api:doc --output docs/api-v1.md` 与 `sudo php think api:doc --format=openapi --output public/docs/api-v1.openapi.json`
- 生产环境已完成 `https://www.frelink.top/`、`https://www.frelink.top/questions/`、`https://www.frelink.top/articles/` 基础 smoke 检查
- 生产文章详情样式文件 `https://www.frelink.top/templates/default/static/css/article/detail.css?v=4.1` 已确认输出 `aw-article-brief li`，且包含 `margin-bottom: 0 !important;`、`line-height: unset;`、`font-size: 0.9rem;`
- 生产知识内容页 `https://www.frelink.top/articles/` 已确认 `aw-knowledge-spotlights` 输出 `padding: 0 10px 18px;`
- 生产 FAQ 样式文件 `https://www.frelink.top/templates/default/static/css/question/index.css?v=4.1` 已确认输出 `aw-faq-lane::before`，且渐变参数为 `linear-gradient(90deg, #1d4ed8 0%, #0f766e 100%)`
- 生产 FAQ 样式文件已确认 `aw-faq-lane` 使用 `min-height: 132px` 与 `background: linear-gradient(180deg, #f8fbfd 0%, #eef6fb 100%)`
- 生产知识内容页 `https://www.frelink.top/articles/` 已确认输出 `aw-knowledge-spotlights`、`aw-knowledge-spotlight::before`、`min-height: 132px`、`background: linear-gradient(180deg, #f8fbfd 0%, #eef6fb 100%)` 和 `font-size: 16px`
- 生产知识内容页 HTML 已确认继续输出 `aw-knowledge-spotlights / aw-knowledge-spotlight` 结构，说明入口卡只是样式收口，没有影响链接结构
- 生产主题页 `https://www.frelink.top/topics/` 已确认同时输出 `swiper.min.js`、`window.__whenSwiperReady = function` 和 `window.__whenSwiperReady(function () { new Swiper(...) })`
- 生产主题页 HTML 已确认不再直接在页尾裸跑 `new Swiper(...)`，桌面端 `Swiper is not defined` 的时序问题已切断
- 生产知识内容页 `https://www.frelink.top/articles/` 已确认输出 `.aw-knowledge-hero { border-radius: 0; }`，聚合页 hero 已切到直角版式
- 生产知识内容页 HTML 已确认保留 `aw-knowledge-hero / aw-page-chips / aw-knowledge-spotlights` 结构，且继续输出 `综述优先沉淀`
- 生产文章详情页 HTML 已确认输出 `aw-article-brief-eyebrow` 与 `aw-article-next-read*` 结构，`30 秒看懂 / 下一步阅读` 新样式类已上线
- 生产文章详情样式文件 `https://www.frelink.top/templates/default/static/css/article/detail.css?v=4.1` 已确认输出 `font-size: 2rem`、`aw-article-next-read`、`margin-top: 32px`
- 生产知识内容页 `https://www.frelink.top/articles/` 已确认查不到 `aw-knowledge-lanes` 结构
- 生产知识内容页 HTML 已确认查不到“最新更新 / 高关注内容 / 如果你想沿同一主题继续追踪变化”这组三卡文案
- 生产知识内容页 HTML 已确认查不到“主内容入口”文案，`综述 / 观察` 标题已直接使用 badge 样式
- 生产知识内容页 HTML 已确认页头标题输出为 `知识内容`，且存在 `aw-page-chips` 结构
- 生产知识内容页样式已确认 `aw-knowledge-spotlights` 使用 `grid-template-columns: repeat(2, minmax(0, 1fr))`
- 生产移动端首页 `https://www.frelink.top/m/` 已确认底部导航不再输出 `aw-mobile-footer-icon-wrap / aw-mobile-footer-icon-outline / aw-mobile-footer-icon-filled` 双层结构
- 生产移动端首页底部导航 HTML 已确认每个导航项只保留一个 `aw-mobile-footer-icon` 图标节点
- 生产移动端首页底部导航 HTML 已确认 `主题 / 观察专题` 未选中态分别输出 `icon-huati1 / icon-zhuanlan1`
- 生产移动端首页底部导航 HTML 已确认首页图标输出为 `icon-shouye1 aw-mobile-footer-icon`
- 生产移动端样式文件 `https://www.frelink.top/templates/default/static/mobile/css/app.css?v=4.1` 已确认输出 `aw-mobile-detail-card / aw-mobile-detail-head / aw-mobile-detail-title / aw-mobile-detail-panel` 相关样式
- 生产移动端样式文件已确认输出 `aw-mobile-detail-panel-highlight`，以及标题 `line-height: 1.62`、正文 `line-height: 1.82` 的新排版参数

## 2026-03-30

### 里程碑：PC 知识内容筛选入口收口

- PC 端知识内容页已移除 hero 中重复展示的 `精选 / 更新 / 高关注` chips，避免页头先提示一遍、筛选区再显示一遍
- 原来的排序 tabs 与 `全部内容 / 综述 / 观察` 类型筛选已合并成一条筛选带，用单层入口同时承载排序和分类

### 里程碑：移动端底部导航图标风格统一

- 移动端底部导航已改成统一的双态图标结构：未选中使用 empty/outline 风格，选中后切换为 filled 风格
- `首页 / 主题 / FAQ / 知识内容 / 观察专题` 现在都使用同一套图标盒子、颜色和切换动画，不再混用不同风格的单个图标

### 里程碑：移动端知识内容筛选入口合并

- 移动端知识内容页的 `更新 / 精选 / 高关注` 与 `全部内容 / 综述 / 观察` 已并入同一条筛选带，不再上下重复出现两套圆角按钮
- 合并后仍保留排序和分类两个维度，只是用单层入口和轻量分隔来降低视觉重复感

### 里程碑：移动端知识内容页头样式统一

- 移动端知识内容页头已改为固定标题 `知识内容`，不再把 `全部内容` 直接作为页头主标题显示
- `综述 / 观察` 主入口卡下方的“转到观察专题”辅助提示已移除，避免在页头继续叠加次级入口
- 知识内容页下方筛选区外层也已去掉顶部额外间距，和 FAQ、主题页保持一致

### 里程碑：移动端 FAQ 页头样式统一

- 移动端 FAQ 页头已按主题页样式统一到同一套 hero 标题、说明文案和三卡片布局，顶部间距与卡片节奏保持一致
- FAQ 列表区外层已去掉顶部额外分隔感，不再在头部卡片区和下方筛选区之间再叠一条横线/上边距

### 里程碑：移动端知识内容入口卡片层级收口

- `全部内容` 下方的 `综述 / 观察` 入口卡已去掉“主内容入口”这层重复标签，只保留分类名称本身作为主视觉
- 原有强调样式已直接应用到 `综述 / 观察` 标题上，减少冗余文字的同时保留入口识别度

### 里程碑：移动端内容页头导航收口

- 移动端 `主题 / FAQ / 知识内容` 页面已移除页头跨页导航，避免与底部全局导航重复占用首屏高度
- 这次调整只收掉抬头里的冗余入口，不改各页面原有 hero、筛选和列表逻辑

### 里程碑：移动端底部导航图标规格统一

- 移动端底部导航的 `首页 / 主题 / 问题 / 文章 / 专题` 图标已统一到同一套 iconfont，并补上固定尺寸的图标盒子
- `文章` 底部导航图标不再单独使用 Font Awesome 文件图标，避免与其它导航项在字形大小、基线和留白上出现明显偏差

### 里程碑：移动端主题页视觉收口

- 移动端主题列表页已改成和 FAQ、知识内容一致的顶部跨页导航与 hero 结构，避免三类入口视觉割裂
- 旧版 `全部话题 / 父级话题` 横向按钮条已移除，不再把类似 `3D建模` 这类父级话题分类直接作为突兀按钮展示在标题下方
- 移动端主题列表已改为两栏卡片布局，话题封面、描述、讨论数与关注状态统一收进卡片

### 里程碑：移动端首页内容结构区块收口

- 移动端首页已移除“内容结构”卡片区块，不再重复展示 `综述 / 观察 / FAQ` 这一组导览入口
- 这次调整只收掉移动端首页中段的结构说明卡片，不影响 hero 快捷卡和下方内容流筛选

### 里程碑：移动端 Hero 卡片数量收口

- 移动端首页 hero 已移除“帮助”卡片，首屏入口区恢复为 4 张卡片的偶数布局
- 这次只调整移动端 hero 快捷入口，不影响下方内容流筛选里的“帮助”入口

### 里程碑：首页 Hero 入口按钮收口

- PC 首页 hero 区已移除 `综述 / 主题 / 观察 / FAQ / 帮助` 五个入口按钮
- hero 现在只保留标题、搜索框和标准说明卡片，不再叠加一组与下方内容区重复的公开入口

### 里程碑：首页公告区说明文案收口

- PC 首页 `公告与更新` 模块已移除“这部分属于低频信息，放在首页底部，方便集中查看站点公告、规则调整和阶段更新。”这类运营提示文案
- 首页底部公告区现在只保留模块标题和实际公告列表，不再额外展示面对访客的说明性副文本

### 里程碑：首页重复导航收口与首排卡片等高

- PC 首页已移除 hero 下方与内容区重复的卡片导航，只保留实际内容模块和后续筛选入口
- `最新综述 / 最新观察 / 常见FAQ` 已拆成首页首排独立等高栅格，三张卡片统一高度，不再由 FAQ 列表单独撑高
- `常见FAQ` 首页卡片条目数收口为 2 条，并对标题与摘要做截断，保证首屏信息密度和版面平衡

### 里程碑：首页内容流筛选合并

- PC 首页内容流已移除“持续更新”标题，不再额外占一行
- 原顶部 `精选 / 更新 / 高关注 / 待补充 FAQ` 排序标签已并入下方筛选按钮，和 `全部内容 / FAQ / 综述 / 观察 / 帮助` 统一成单层入口
- 这次调整只重排首页筛选结构，不改内容流查询逻辑，原有 `sort / type / article_type` 参数仍保持兼容

### 里程碑：首页板块对齐与列表说明文案收口

- PC 首页三列内容卡片改为按自然高度起排，不再被最高卡片强制拉伸，`核心主题` 与 `知识归档` 下方的大面积空白已收口
- `知识归档` 章节无公开内容时，首页回退文案改为中性占位提示，不再把章节描述里的内部运营说明直接展示给访客
- 首页共享列表模板已移除 FAQ 列表中的说明性副文案，公开页不再输出“沉淀高频问题、明确答案和后续补充说明，作为知识系统的检索入口。”

### 里程碑：PC 首页归档与知识地图充实

- PC 首页 `知识归档` 模块已把章节标签改成和“归档章节 · X 条内容”同一行的横排 badge，减少纵向空白
- PC 首页 `知识地图` 已接入真实主题连接数据，展示主题、关联章节和关联内容数，不再只剩统计数字
- `知识地图` 仍保留底部统计卡，但上半区现在优先显示实际可点击内容

### 里程碑：首页公开文案收口

- 首页 `持续更新` 区块和首页顶部内容入口卡片，已移除面向内部分类/运营的解释性文案
- 移动端首页同步移除 `持续更新` 和 `内容结构` 区块中的分类说明，只保留公开入口名称和必要标题
- 这些分类语义仍保留在代码层、agent 流程和管理侧可读位置，不再在首页对访客直出

### 里程碑：首页文章残留缓存修复

- 首页整页缓存、首页公共列表缓存和首页文章卡片缓存现在统一挂到文章内容版本号上
- 文章发布、更新、删除、恢复和回滚时会主动刷新首页内容版本，避免首页继续显示刚删掉或刚改状态的文章
- 这次修复不依赖缩短 TTL，而是让文章状态变化直接触发首页缓存换 key

### 里程碑：API 发文审核闸门补强

- `ApiToken` 触发的文章发布与文章修改现在强制进入审核流，不再因为绑定管理员账号而直接绕过人工审核
- `scripts/publish_chain.py` 现在会显式记录 `article_approval_id` 与 `article_visibility`，把“待审成功”与“正式发布”区分开
- `docs/publish-test-template.md` 已补充 API 审核链路验收要求，明确管理员绑定 token 做文章发文测试时也必须返回待审结果

### 验证

- 生产移动端模板已确认底部导航 `文章` 图标已改为 `iconfont icon-wenzhang1 aw-mobile-footer-icon`
- 生产移动端模板已确认底部导航统一使用 `aw-mobile-footer-icon` 固定图标盒子
- 生产移动端主题页 `https://www.frelink.top/topic/index.html` 已确认不再输出 `全部话题 / 3D建模` 这类父级话题按钮条
- 生产移动端主题页 HTML 已确认话题列表容器切换为 `aw-mobile-topic-grid` 两栏布局
- 生产移动端首页 `https://www.frelink.top/m/` 已确认查不到“内容结构”区块
- 生产移动端首页 `https://www.frelink.top/m/` 已确认 hero 区不再输出“帮助”卡片
- 生产首页 `https://www.frelink.top/` 已确认查不到 hero 区 `aw-home-quick-actions` 结构
- 生产首页 HTML 已确认不再输出 hero 区 `综述 / 主题 / 观察 / FAQ / 帮助` 这一组公开入口按钮
- 生产首页 `https://www.frelink.top/` 已查不到“这部分属于低频信息，放在首页底部，方便集中查看站点公告、规则调整和阶段更新。”
- 生产首页 HTML 已确认不再输出 `aw-home-content-map` 重复卡片导航结构
- 生产首页 HTML 已确认首页内容卡改为 `aw-home-curated-row aw-home-curated-row-primary/secondary` 双排结构，首排三卡为等高布局
- 生产首页 HTML 已确认 `常见FAQ` 首页卡片查询已改为 `limit=\"2\"`
- 生产首页 HTML 已确认查不到“持续更新”标题，内容流筛选区改为单层按钮结构
- 生产首页 HTML 已确认同一区块同时输出 `精选 / 更新 / 高关注 / 待补充 FAQ / 全部内容 / FAQ / 综述 / 观察 / 帮助`
- 生产首页 HTML 已确认输出 `align-items: start` 与 `align-self: start`，首页三列卡片不再按等高网格被强制撑高
- 生产首页 HTML 已确认输出“当前章节下还没有公开归档内容”，不再回退展示章节描述中的运营说明
- 生产首页 `https://www.frelink.top/` 与移动端首页 `https://www.frelink.top/m/` 已查不到“沉淀高频问题、明确答案和后续补充说明，作为知识系统的检索入口。”
- 生产首页 HTML 已确认输出 `aw-home-curated-meta / aw-home-curated-tag`，`FAQ / 帮助 / 研究综述 / 观察` 章节标签已横排展示
- 生产首页 HTML 已确认输出 `aw-home-map-link` 相关结构，`知识地图` 已展示真实主题连接及“X 个章节 / X 条关联内容”
- 生产首页 `https://www.frelink.top/` 已查不到“以下内容流用于持续补充综述、观察、FAQ 和帮助条目”等解释性文案
- 生产移动端首页 `https://www.frelink.top/m/` 已查不到“混排综述、观察、FAQ 和帮助条目，作为首页主入口之外的更新流”等分类说明
- 生产模板文件 `public/templates/default/html/index.php` 与 `public/templates/default/mobile/index.php` 已确认不再包含上述首页公开文案
- 本地已完成改动 diff 复核，当前环境仍无 `php`，语法检查放到远端执行
- 本地已完成脚本语法检查：`python3 -m py_compile scripts/publish_chain.py`
- 生产环境已执行 `bash scripts/deploy.sh deploy`，远端 `php -l app/function.inc.php`、`sudo php think clear`、API 文档重建与首页/列表 smoke 均通过
- 生产库已确认旧测试文章 `149 / 148 / 147` 当前均为 `status=0`
- 生产首页 `https://www.frelink.top/`、文章页 `https://www.frelink.top/articles/` 与 `api/Article/index?sort=new&page=1&page_size=10` 均已查不到 `审核待审文章-*` 残留标题
- 生产站点已用真实 `ApiToken` 调用 `/api/Article/publish`，返回 `code=1`、`data.status=pending_review`、`approval_id=9`
- 生产库已确认 `kn_approval.id=9` 为新增待审文章记录，`status=0`、`item_id=0`
- 生产库已确认测试标题 `审核链路 API 验证 20260330 文章待审` 未写入 `kn_article`，计数结果为 `0`

## 2026-03-29

### 里程碑：API 文档自动生成接入部署

- `scripts/deploy.sh` 的远端验证流程现在会自动执行 `php think api:doc`，同步重建 `docs/api-v1.md` 和 `public/docs/api-v1.openapi.json`
- `app/common/command/ApiDoc.php` 已补上 `Article/publish` 与 `Topic/create` 的待审返回说明，Markdown 文档会追加 `特殊返回说明`
- OpenAPI 输出现在会为上述接口生成 `pending_review` 成功示例，明确 `code=1`、`data.status=pending_review` 与 `approval_id` 的契约

### 里程碑：话题审核链与 agent 发布测试补齐

- API 与前台的 `Topic/create` 现在都支持进入 `approval` 审核流，不再只有文章、问题、回答有待审闸门
- 后台审核列表、管理员通知和审核预览页已补上 `topic` 类型，审核员现在可以直接查看并通过/拒绝待审话题
- `Article/publish` 的 API 待审返回从错误态收口为成功态，外部 agent 不需要再把“等待管理员审核”误判成失败
- `scripts/publish_chain.py` 已补齐两类兼容逻辑：话题已存在时自动复用；文章进入待审时按正常结果记录 `status`
- 已补充现网升级脚本 `docs/topic-approval-upgrade.sql`，用于给存量站点插入 `create_topic_approval` 权限项

### 里程碑：后台登录跳转循环修复

- 后台登录态现在统一以 `admin_login_uid` 作为判定依据，不再误用前台 `login_uid` 参与后台跳转判断
- 登录成功后后台会回填 `admin_user_info` 和 `admin_login_user_info`，避免进入管理页时被误判成未登录
- 退出登录时同步清理后台相关 session 键，防止旧会话残留引发重复跳转
- 后台入口会复用前台登录态做权限桥接，前台账号若属于管理组可直接进入后台，普通账号仍进入后台登录页
- 前台“管理后台”菜单改为带自动登录标记的后台入口，直接访问 `/admin.php` 仍会看到后台登录界面

### 里程碑：后台首页 `primary_url` 空值容错修复

- 后台首页的周执行清单与补齐建议现在会先补全 `primary_url`、`secondary_url` 等字段，再交给模板渲染
- 后台首页模板改为防御式判断链接字段是否存在，避免旧缓存或缺字段记录触发 `Undefined array key "primary_url"`

### 里程碑：API token 后台入口补齐

- `app/backend/extend/Token.php` 的 API 认证管理页已经存在，支持新建、编辑和查看 `type=3` 的 API token
- 现有站点需要补执行 `docs/api-token-upgrade.sql`，把 `extend.Token/index` 的后台权限节点插入 `admin_auth`，否则菜单树不会显示入口
- 升级脚本同时保留了 `app_token` 的结构扩展，保证 token 配置页和数据库字段同步落地

### 里程碑：后台 `explore` 兼容入口修复

- `admin.php/explore/` 现在不再走后台登录/路由错误分支，而是直接复用前台 `Index@index` 的页面输出
- 这次修复保留了前台 `/explore/` 的原始路由，不改动站点公开入口，只补了后台兼容访问路径
- 已在生产环境确认 `admin.php/explore/` 返回前台首页 HTML，不再是跳转页或 404

### 里程碑：首页登录态下拉菜单遮挡修复

- 首页 Hero 区恢复了可见溢出，避免登录后右上角用户下拉菜单被首屏背景层裁切或覆盖
- 首页顶部导航和下拉菜单在模板层提升了层级，确保菜单面板始终压在 Hero 内容之上
- 该修复已同步到服务器并完成最小验证，生产页面输出中已能看到新的 `z-index` 与 `overflow: visible` 样式

### 里程碑：API token 服务器落地验证

- 已通过标准部署脚本把这轮改动同步到生产服务器，并完成远程缓存清理
- 已在服务器上执行 `kn_app_token` 的结构升级 SQL，新增 `uid`、`status`、`expire_time`、`last_use_time`、`last_use_ip` 和 `remark` 字段
- 已创建临时验证 token 并完成真实接口测试，`/api/User/my` 与 `/api/Common/get_access_key` 均可通过 `ApiToken` 正常鉴权
- `Insight/summary` 返回的是业务权限拒绝，说明 token 认证链路已经生效，当前限制来自接口权限而不是登录态失效

### 里程碑：API token 认证接入

- 后台 `app_token` 现在可直接创建 API 认证项，支持绑定用户 UID、设置启停状态、过期时间和使用备注
- `/api` 入口已兼容 `ApiToken` / `AccessToken` 头；当 token 绑定用户时，会直接按该用户身份通过需要登录的接口
- 这次改动保留了 `UserToken` 的原有用户登录方式，新增的是适合服务接入方和 agent 的后台托管 token 方案
- 已补充数据库升级脚本 `docs/api-token-upgrade.sql`，并同步更新安装 SQL、接口说明和 OpenAPI 规范

### 里程碑：API 响应包补齐追踪字段

- 所有 API 响应现在统一附带 `request_id`，便于把前端报错、接口日志和服务器侧排障串成一条链路
- 失败响应补充了可机器识别的 `error_code`，外部调用方可以先按稳定错误码分支，再按 `msg` 做人类可读提示
- `app/common/command/ApiDoc.php`、`docs/api-v1.md` 和 `README.md` 已同步更新响应契约说明，避免继续沿用旧的 `code=0/1` 认知
- 这次调整保持了既有 `code/msg/data` 结构不变，只是补齐了对 agent 和集成方更友好的元数据

### 里程碑：FAQ / 文章 / 主题公开页视觉语言收口

- 文章、FAQ 和主题页的页头统一补齐了引导标签和 chips 结构，公开入口的语义层级开始保持一致
- 文章详情页的旧版全局按钮样式已收口到页级作用域，避免继续污染其它页面的按钮外观
- 主题详情页与 FAQ 详情页的高频操作区开始统一到同一套圆角卡片和蓝绿系强调色

### 里程碑：agent 草稿与回滚闭环推进

- `优化计划.md` 的 `M5 API / Agent 扩展` 已更新为已完成、旁路完成和已验证状态
- API 侧新增 `agent_draft` 草稿生成入口，洞察建议已能落成待审草稿并写入 `draft` 表
- 文章与 FAQ 的更新链路已经接通 API 侧回滚动作，可回退到上一版修订快照
- 已同步到服务器，并完成远程 `php -l`、缓存清理和接口可达性检查
- 发布页的“生成草稿 / 回滚”只是管理端旁路，不算 agent 自动化主线

## 2026-03-28

### 里程碑：移动端公告条宽度收口

- 移动端首页的本站公告组件取消了 `100% + margin` 的外溢布局，改为 `calc(100% - 24px)` 的内收宽度
- 公告文本区补了 `min-width: 0` 和 overflow 收口，避免长标题把首页横向撑宽

### 里程碑：移动端首页宣传卡移除

- 搜索框下方的四个宣传式说明卡片移除，首页首屏改为更正式的站点定位说明
- 首页 Hero 仅保留主定位、搜索和入口，不再使用偏口号化的自我说明

### 里程碑：移动端首页结构重排

- 移动端首页把公告、探索入口和持续更新流拆成更连续的分区，去掉了“展开次级入口”的折叠按钮
- 首页内容结构与更新流的列表样式统一成更圆润的卡片体系，减少了方框感和视觉断裂

### 里程碑：观察专题页恢复底部导航

- 移动端观察专题列表页和详情页取消了对 `footer` block 的空覆盖，重新继承全局底部导航栏
- 这次修复只影响底部导航显示，不影响观察专题页内容、筛选或跳转逻辑

### 里程碑：移动端底部导航图标修复

- 移动端底部导航里的 `FAQ` 和 `专题` 图标改为移动端字体表里稳定存在的类名，避免依赖旧的通用图标类导致不可见
- 这次修复只动底部导航图标，不影响对应页面跳转、文案或权限逻辑

### 里程碑：agent 汇总接口补齐

- 运营洞察新增 `agent_brief` 入口，可以一次拿到摘要、周执行清单、写作工作流、发布辅助和主题图谱
- `app/api/index.html` 和 `docs/api-v1.md` 已同步把 `agent_brief` 作为 agent 优先入口说明
- 这一步把 agent 的 API 路径从“多个分散接口”收敛成“单次汇总 + 按需拆分”的模式

### 里程碑：移动端文章页首屏收口

- 移动端文章列表页现在补上了和首页一致的首屏标题与说明区，内容入口不再直接从分类说明跳到列表
- 增加了 `最新更新 / 精选 / 高关注 / 转到主题` 的快捷入口，减少用户在内容类型和主题入口之间来回找路
- 文章页移动端首屏现在和桌面端一样，开始把“内容形态”放在比列表更前的位置

### 里程碑：主题页首屏引导统一

- 桌面端和移动端主题页都补齐了统一的首屏引导块，开始和首页、FAQ、文章页保持一致的信息层级
- 主题页新增 `最新话题 / 关注最多 / 讨论最多` 三个快速入口，继续把主题页定位成长期追踪入口，而不是零散话题列表
- 这次调整属于 UI 收口的一部分，后续可以继续向列表页、详情页和发布页扩展同类结构

### 里程碑：发布页补齐研究综述与思想碎片运营规则

- 文章发布页和移动端发文页现在都会直接展示 `运营规则`，把 `研究综述 / 思想碎片 / 主题追踪 / 教程 / FAQ / 普通内容` 的写法侧重点放到写作入口
- 内容类型切换后会同步刷新对应规则说明，避免只改标题和模板、不改正文组织方式
- `思想碎片` 现在在发布页中有了明确的运营规则承接，不再只是一个内部标签

### 里程碑：移动端基础模板继续收口首屏阻塞

- 移动端基础模板把 `swiper / module / highlight / mescroll / captcha` 等非关键 CSS 改成预加载方式，减少同步样式阻塞
- 保留 `aui`、`app.css` 和字体样式为同步直出，避免影响移动端基础布局与交互初始化
- 这一步先收 CSS 链路，脚本链路保持稳定，不扩大到可能影响底部内联逻辑的 defer 改造

### 里程碑：首页头图补上 AVIF 优先级

- 首页头图现在优先走 `top-img.avif`，其次是 `top-img.webp`，最后回退 `top-img.png`
- `top-img.avif` 已生成并接入首页头图与全局 preload 链路，进一步压低首屏图片体积
- 这一步保留了 WebP / PNG 回退，不影响不支持 AVIF 的浏览器

### 里程碑：首页首屏标准直接写进 hero 文案

- 桌面端和移动端首页都补上了 `3 秒 / 30 秒 / 下一篇 / 标题真实反映正文` 的筛选标准
- 首页首屏现在会直接告诉用户这站点为什么值得看、看完能得到什么、是否值得继续点下一篇
- 这一步把内容策略从计划文档推进到了用户可见的首屏文案

### 里程碑：移动端首页洞察数据开始走缓存

- `Insight` 和 `Help` 的首页相关读模型增加了缓存，减少移动端首页每次请求都重新统计热门词、内容趋势和知识归档章节
- 移动端首页的 `links` 查询也改成了短时缓存，减少重复打库
- 这一步直接针对首页 TTFB 的实时统计查询开刀，不再只靠页面级缓存兜底

### 影响范围

- [app/function.inc.php](/mnt/f/workwww/knowlege-github/app/function.inc.php)
- [app/frontend/Article.php](/mnt/f/workwww/knowlege-github/app/frontend/Article.php)
- [app/mobile/Article.php](/mnt/f/workwww/knowlege-github/app/mobile/Article.php)
- [public/templates/default/html/article/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/article/publish.php)
- [public/templates/default/mobile/article/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/article/publish.php)
- [public/templates/default/mobile/block.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/block.php)
- [public/templates/default/html/block.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/block.php)
- [public/templates/default/html/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/index.php)
- [public/templates/default/mobile/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/index.php)
- [app/model/Insight.php](/mnt/f/workwww/knowlege-github/app/model/Insight.php)
- [app/model/Help.php](/mnt/f/workwww/knowlege-github/app/model/Help.php)
- [app/mobile/Index.php](/mnt/f/workwww/knowlege-github/app/mobile/Index.php)
- [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 已完成本地页面与文案联动修改
- 当前环境无 `php` 可执行文件，未能执行本地语法检查

## 2026-03-27（续10）

### 里程碑：首页、列表页与栏目页语义继续收口

- 首页与移动端首页继续统一为 `FAQ / 知识内容 / 知识章节` 的公开语境
- 话题详情页的发布入口从 `写文章` 收口为 `写知识内容`
- 栏目页顶部 CTA 从 `开始写文章` 收口为 `开始写知识内容`

### 影响范围

- [public/templates/default/html/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/index.php)
- [public/templates/default/mobile/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/index.php)
- [public/templates/default/html/topic/detail.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/topic/detail.php)
- [public/templates/default/mobile/topic/detail.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/topic/detail.php)
- [public/templates/default/html/column/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/column/index.php)
- [public/templates/default/html/draft/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/draft/index.php)
- [public/templates/default/html/focus/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/focus/index.php)
- [public/templates/default/html/widget/member/lists.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/widget/member/lists.php)
- [public/templates/default/mobile/search/ajax_search.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/search/ajax_search.php)
- [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 已完成本地模板语义调整
- 已通过远端文件抽查确认相关页面文案已同步到生产机

## 2026-03-27（续9）

### 里程碑：详情页与发布页语义统一

- FAQ 列表、详情、发布和移动端详情页进一步统一语义
- 帮助中心的章节筛选与搜索提示统一到 `FAQ / 知识内容` 语境
- 文章详情页的管理动作统一到 `知识内容` 语境

### 影响范围

- [app/function.inc.php](/mnt/f/workwww/knowlege-github/app/function.inc.php)
- [public/templates/default/html/question/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/question/index.php)
- [public/templates/default/html/question/detail.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/question/detail.php)
- [public/templates/default/html/question/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/question/publish.php)
- [public/templates/default/html/help/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/help/index.php)
- [public/templates/default/html/help/detail.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/help/detail.php)
- [public/templates/default/html/article/detail.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/article/detail.php)
- [public/templates/default/mobile/question/index.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/question/index.php)
- [public/templates/default/mobile/question/detail.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/question/detail.php)
- [public/templates/default/mobile/question/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/question/publish.php)
- [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 已完成本地模板语义调整
- 已通过远端文件抽查确认相关页面文案已同步到生产机

## 2026-03-27（续8）

### 里程碑：站点导航词汇统一

- 主导航、移动端底栏和新建入口统一到 `FAQ / 知识内容 / 专题 / 帮助中心`
- 导航层不再混用 `问题 / FAQ` 和 `文章 / 知识内容` 两套说法

### 影响范围

- [app/function.inc.php](/mnt/f/workwww/knowlege-github/app/function.inc.php)
- [public/templates/default/html/global/nav.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/global/nav.php)
- [public/templates/default/mobile/block.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/block.php)
- [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 已完成本地模板与词典调整
- 准备同步到远端验证主导航与移动端底栏显示

## 2026-03-27（续7）

### 里程碑：移动端 FAQ 详情页语义统一

- 移动端 FAQ 详情页底部的关注按钮改为 `关注 FAQ`
- 同一内容页内的动作文案不再出现 `问题` 与 `FAQ` 混用

### 影响范围

- [public/templates/default/mobile/question/detail.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/question/detail.php)
- [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 已完成本地模板修改
- 已准备同步到远端进行烟测

## 2026-03-27（续6）

### 里程碑：桌面端与移动端发布页推荐区统一

- 移动端文章/FAQ 发布页补齐了桌面端已有的标题建议理由和搜索量数字
- 发布页推荐区现在在多端保持更接近的结构和信息密度，减少 PC 与移动端切换时的认知落差

### 影响范围

- [public/templates/default/mobile/article/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/article/publish.php)
- [public/templates/default/mobile/question/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/question/publish.php)
- [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 已完成远端同步和站点烟测
- 页面相关模板已同步到线上代码

## 2026-03-27（续5）

### 里程碑：每日运营日报自动生成并接入后台

- `we day` 现在会在每日任务里自动生成运营日报快照，并写入 `runtime/insight/daily`
- 后台首页新增了“每日运营日报”查看卡片，可以直接复制给 agent 或人工复盘
- `insight:report` 现在也支持 `--save`，方便手动触发时顺手落盘相同快照
- 这次把“每日报告和定时任务”从路线图推进成了可执行、可查看、可复用的闭环

### 影响范围

- [app/model/Insight.php](/mnt/f/workwww/knowlege-github/app/model/Insight.php)
- [app/common/command/InsightReport.php](/mnt/f/workwww/knowlege-github/app/common/command/InsightReport.php)
- [app/common/command/WeCenter.php](/mnt/f/workwww/knowlege-github/app/common/command/WeCenter.php)
- [app/common/logic/common/CronLogic.php](/mnt/f/workwww/knowlege-github/app/common/logic/common/CronLogic.php)
- [app/backend/Index.php](/mnt/f/workwww/knowlege-github/app/backend/Index.php)
- [app/backend/view/index/index.php](/mnt/f/workwww/knowlege-github/app/backend/view/index/index.php)
- [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 本地 `git diff --check` 已通过
- 本地已执行 `bash scripts/deploy.sh deploy`
- 远程已执行：
  - `php -l app/function.inc.php`
  - `sudo php think clear`
  - `curl -I -L --max-time 20 https://www.frelink.top/`
  - `curl -I -L --max-time 20 https://www.frelink.top/questions/`
  - `curl -I -L --max-time 20 https://www.frelink.top/articles/`
- 远程文件抽查已确认：
  - `app/model/Insight.php`
  - `app/common/command/InsightReport.php`
  - `app/common/command/WeCenter.php`
  - `app/common/logic/common/CronLogic.php`
  - `app/backend/Index.php`
  - `app/backend/view/index/index.php`

## 2026-03-27（续4）

### 里程碑：推荐话题可直接挂载到草稿

- 发布页里的推荐话题不再只是跳转查看，而是可以直接加入当前草稿
- 桌面端文章/FAQ 发布页会把推荐话题写入 `select2` 选择器
- 移动端文章/FAQ 发布页会把推荐话题写入当前草稿的话题列表和隐藏字段
- 这次优化把“看到建议”推进到“直接挂载”，减少了发布时的重复检索和手工补挂步骤

### 影响范围

- [public/templates/default/html/article/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/article/publish.php)
- [public/templates/default/html/question/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/question/publish.php)
- [public/templates/default/mobile/article/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/article/publish.php)
- [public/templates/default/mobile/question/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/question/publish.php)
- [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 本地 `git diff --check` 已通过
- 本地已执行 `bash scripts/deploy.sh deploy`
- 远程已执行：
  - `php -l app/function.inc.php`
  - `sudo php think clear`
  - `curl -I -L --max-time 20 https://www.frelink.top/`
  - `curl -I -L --max-time 20 https://www.frelink.top/questions/`
  - `curl -I -L --max-time 20 https://www.frelink.top/articles/`
- 远程文件抽查已确认：
  - `public/templates/default/html/article/publish.php`
  - `public/templates/default/html/question/publish.php`
  - `public/templates/default/mobile/article/publish.php`
  - `public/templates/default/mobile/question/publish.php`

## 2026-03-27（续3）

### 里程碑：移动端发布页补齐主题建议

- 移动端文章发布页现在也能直接展示 `建议优先扩展的话题`
- 移动端 FAQ 发布页现在也能直接展示 `建议优先挂载的话题`
- 这次补齐后，文章和 FAQ 的移动端发布入口在“标题建议 + 话题建议”上保持一致，减少了只在桌面端可见的引导缺口

### 影响范围

- [public/templates/default/mobile/article/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/article/publish.php)
- [public/templates/default/mobile/question/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/question/publish.php)
- [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 本地 `git diff --check` 已通过
- 本地已执行 `bash scripts/deploy.sh deploy`
- 远程已执行：
  - `php -l app/function.inc.php`
  - `sudo php think clear`
  - `curl -I -L --max-time 20 https://www.frelink.top/`
  - `curl -I -L --max-time 20 https://www.frelink.top/questions/`
  - `curl -I -L --max-time 20 https://www.frelink.top/articles/`
- 远程文件抽查已确认：
  - `public/templates/default/mobile/article/publish.php`
  - `public/templates/default/mobile/question/publish.php`

## 2026-03-27（续2）

### 里程碑：部署脚本自动修复 runtime 权限

- 这次线上 `500` 的根因是 `runtime` 目录属主和 PHP-FPM 进程不一致，导致 file cache 写入失败
- `scripts/deploy.sh` 现在在 `sync` 后自动把 `runtime` 修正为 `www:www`，并同步目录权限，避免再次因为缓存写失败把首页打成 500
- 这属于部署可靠性收口，不改业务逻辑，但能直接降低发布后站点不可用风险

### 影响范围

- [scripts/deploy.sh](/mnt/f/workwww/knowlege-github/scripts/deploy.sh)

### 验证

- 本地 `git diff --check` 待执行
- 远程已手动修复 `runtime` 权限并验证首页恢复 `200`

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

## 2026-03-30

### 里程碑：收口移动端知识内容公开语义

- 移动端 `知识内容` 公开分类收敛为 `全部内容 / 综述 / 观察`：
  - 不再把 `主题追踪 / 方法 / 帮助` 这类内部历史文章类型直接暴露成前台筛选按钮
  - 手工访问旧类型参数时会自动回退到 `全部内容`
  - `全部内容` 现在只聚合公开知识内容范围内的 `综述 + 观察`，不再把旧的内部文章类型混进来
- `综述` 与 `观察` 的公开定义重新改写：
  - `综述` 明确为知识内容里的稳定条目，用来整理背景、分歧和当前结论
  - `观察` 明确为知识内容里的动态条目，用来记录判断、线索和持续变化
- 移动端 `专题` 入口和知识内容页跳转统一改成 `观察专题`：
  - 避免“导航写专题，进去却是观察专题”的语义错位
  - 知识内容与观察专题之间的关系改成公开可理解的“条目页 -> 长期观察容器”
- 移动端首页同步收口：
  - 首页知识流筛选去掉 `帮助` 这个文章子类型入口
  - hero 文案和快捷卡统一改成 `综述 / 主题 / 观察专题 / FAQ` 这套公开层口径
- 移动端文章列表卡片说明改写为统一语义：
  - `综述条目` 强调系统整理
  - `观察条目` 强调动态判断
  - 其余知识内容条目只保留中性说明，不再混入旧运营分类解释
- 修复移动端知识内容异步列表：
  - `ajax/lists` 渲染文章行时补齐 `item_type`
  - 解决“点击知识内容后页面看起来无内容，实际是列表接口 500” 的问题

### 里程碑补充：同步收口桌面端知识内容语义

- 桌面端 `知识内容` 页与首页文章筛选同步改成公开范围：
  - `全部内容` 只聚合 `综述 + 观察`
  - 不再把 `热点解释 / 方法 / 帮助 / 主题追踪` 混进桌面公开分类页
- 桌面端知识内容页的关系入口改成 `观察专题`：
  - 去掉容易和普通 `主题` 混淆的 `转到主题`
  - 明确知识内容条目与长期观察容器之间的关系
- 桌面端公共导航中的 `专题` 入口同步更名为 `观察专题`
- 桌面首页 hero 文案同步更新：
  - 不再沿用 `主题追踪` 这套旧公开说法
  - 统一为 `综述 / 观察 / FAQ / 主题 / 观察专题` 这一层公开语义
- 桌面导航 title 与可见文案也同步改成统一口径，避免鼠标悬停时仍出现旧的 `专题`

### 里程碑补充：收口移动端主题卡片高度

- 移动端主题列表保留两栏排列，但去掉额外增加的卡片最小高度
- 主题卡片现在按内容自然撑开，避免两栏卡片显得过高、空白过多

### 里程碑补充：补齐主题卡片横向头部与旧文章可见性

- 移动端主题卡片改成 `图标 + 标题` 横向排布
- 进一步压缩卡片内边距、描述行数和按钮尺寸，继续降低两栏卡片高度
- `知识内容` 的公开筛选仍只保留 `综述 / 观察` 两个主入口，但 `全部内容` 恢复显示原有文章：
  - `research` 对应 `综述`
  - `fragment` 对应 `观察`
  - `track / tutorial / faq / normal` 暂不强制映射进这两个公开分类
  - 历史文章继续保留在 `全部内容` 里，避免旧内容消失

### 里程碑补充：统一移动端 FAQ / 知识内容 / 主题页头

- 移动端 `FAQ / 知识内容 / 主题` 三个页面的顶部交叉导航统一成同一组入口
- `FAQ` 页头中间入口由 `综述` 改为 `知识内容`
- `知识内容` 页头第三项不再直接写成 `观察专题`，改回 `主题`
- `观察专题` 继续保留在正文功能入口中，不再和页头主导航混用

### 里程碑补充：收口移动端知识内容页头层级

- 移动端知识内容页删除排序 badges 与说明卡的重复层
- 页头现在只保留：
  - 一层主说明
  - 一层 `综述 / 观察` 主入口卡
  - 一条简短辅助说明指向 `观察专题`
- 减少在文章分类前重复出现 `最新更新 / 精选 / 高关注 / 观察专题` 多套入口，降低语义重叠和视觉冗余

### 验证

- 已同步到远程服务器
- 远端已执行 `sudo php think clear`
- 移动端公开页将只展示 `全部内容 / 综述 / 观察`
- 移动端底部导航 `专题` 已明确改成 `观察专题`

## 2026-03-27

### 里程碑：Sitemap 提交清单与后台入口

- `sitemap:build` 继续负责生成 `sitemap.xml`
- 后台新增 `Sitemap 提交清单`，可直接导出 Markdown / JSON
- 提供当前 sitemap 地址、Google Search Console 和 Bing Webmaster Tools 入口
- 公开 ping 端点已不再作为稳定提交通道，因此改为后台提交清单辅助人工操作

### 影响范围

- sitemap 生成链路与提交清单：
  - [app/common/library/helper/SitemapHelper.php](/mnt/f/workwww/knowlege-github/app/common/library/helper/SitemapHelper.php)
- 命令行：
  - [app/common/command/Sitemap.php](/mnt/f/workwww/knowlege-github/app/common/command/Sitemap.php)
- 定时任务：
  - [app/common/logic/common/CronLogic.php](/mnt/f/workwww/knowlege-github/app/common/logic/common/CronLogic.php)
- 后台：
  - [app/backend/Index.php](/mnt/f/workwww/knowlege-github/app/backend/Index.php)
  - [app/backend/view/index/index.php](/mnt/f/workwww/knowlege-github/app/backend/view/index/index.php)
- 文档：
  - [优化计划.md](/mnt/f/workwww/knowlege-github/优化计划.md)

### 验证

- 远端 `php -l` 通过：
  - [app/common/library/helper/SitemapHelper.php](/mnt/f/workwww/knowlege-github/app/common/library/helper/SitemapHelper.php)
  - [app/common/command/Sitemap.php](/mnt/f/workwww/knowlege-github/app/common/command/Sitemap.php)
  - [app/common/logic/common/CronLogic.php](/mnt/f/workwww/knowlege-github/app/common/logic/common/CronLogic.php)
- 远端 `php think sitemap:build --base-url=https://www.frelink.top --notify` 可正常执行并输出 sitemap 路径，但公开 ping 端点返回非 2xx，因此改为后台提交清单入口

### 下一步

- 如果后续搜索引擎仍有抓取滞后，再把通知结果做成后台可视化状态卡

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

## 2026-03-27

### 里程碑：观察整理入口前置到发布辅助

- 文章发布页新增 `观察整理` 候选区：
  - 直接展示近期被持续阅读的观察内容
  - 一键填充标题与推荐内容形态
  - 支持继续整理成 `综述 / 帮助 / 主题追踪`
- 后端 `publish_assist` 现在会优先输出适合整理的观察内容：
  - 让“思想碎片”不再只停留在原始记录层
  - 把高频阅读的观察提前推入写作入口
- 桌面端与移动端发布页都接入了同一套建议数据：
  - 保持内容类型提示、模板按钮和周执行清单的一致性
  - 避免前后端看到的建议口径不一致

### 影响范围

- 后端：
  - [app/model/Insight.php](/mnt/f/workwww/knowlege-github/app/model/Insight.php)
- 前台：
  - [public/templates/default/html/article/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/html/article/publish.php)
  - [public/templates/default/mobile/article/publish.php](/mnt/f/workwww/knowlege-github/public/templates/default/mobile/article/publish.php)
- 模型与语言：
  - [app/lang/en-us.php](/mnt/f/workwww/knowlege-github/app/lang/en-us.php)

### 验证

- 已同步到远程服务器
- 远端已执行 `sudo php think clear`
- 公开站点 `https://www.frelink.top/`、`https://www.frelink.top/questions/`、`https://www.frelink.top/articles/` 均可访问

### 下一步

- 继续把 `思想碎片 -> 研究综述 -> 专题/帮助` 这条内容链路再收紧一点
- 逐步把发布页建议和专题页聚合规则统一成同一套内容判断逻辑

### 里程碑补充：发文流程双路径与人工审核闸门

- 发文流程明确拆成两条线：
  - `自动化筛选发文`：由热点、搜索词和内容缺口驱动，每天控制在 `1-2` 篇
  - `手动指令发文`：由站长对感兴趣观点的主动指令驱动
- 两条线都必须经过人工审核后才能发布：
  - 自动发文只允许生成草稿与待审建议
  - 手动发文同样要经过审核，避免跑题或逻辑缺口
- 审核重点统一为：
  - 标题与正文是否一致
  - 是否存在自动化痕迹、模板腔、重复段落
  - 是否存在事实跳跃、结论外推或上下文缺失
- 后续实现会优先把 agent 限定在“选题、起草、整理候选”层，正式发布仍保留人工确认

### 里程碑补充：发文工作流接口

- 新增 `GET /api/Insight/writing_workflow`：
  - 统一输出自动筛选发文、手动指令发文和人工审核闸门
  - 自动流返回候选稿、推荐形态和发布入口
  - 手动流返回主题提示、标题建议、结构模板和审核要求
- 新增文档入口：
  - `app/api/index.html`
  - `docs/api-v1.md`
- 这一步的目标是先把 agent 限制在“可读工作流”层，而不是直接接管发布权限

### 里程碑补充：首页内容卡兜底

- 首页“最新综述”改为首页专用数据源：
  - 优先展示 `article_type=research` 的内容
  - 当综述为空时，自动回退到最近发布或最热门的文章
- 首页“知识归档”改为首页专用数据源：
  - 优先展示已有归档章节
  - 当归档为空时，自动回退到高关注主题
- 这次改动同步覆盖桌面端和移动端首页，避免空卡片长期挂在首屏上
- 桌面首页补充“知识地图”概览卡，填充核心主题与知识归档后方的空位，维持三列等宽布局
- 首页“最新观察”不再回退成普通文章列表，优先显示 fragment 内容，空时改用观察推荐兜底；桌面首页卡片统一改为 flex 结构，减少和 FAQ 的高度错位感
- 修复首页观察兜底调用权限问题：`Insight::getFragmentPromotionIdeas()` 改为公开方法，避免首页在无 fragment 内容时 500
- 首页“最新观察”空态改为正式 CTA 卡，提供发布观察和查看观察列表入口，避免与综述卡出现同内容或空行错位
- 桌面首页“公告与更新”移回主内容列内部，避免与上方卡片区出现宽度分离
- 首页公告 widget 在桌面首页内彻底扁平化；`help/index` 统一重做为知识地图 hero + 主题连接 + 章节网格布局，修复原有错位

### 里程碑补充：知识内容详情页桌面端排版收口

- 桌面端知识内容详情页新增独立头部样式：
  - 收窄标题字号，避免首屏标题压迫感过强
  - 作者与认可/浏览信息改成统一信息卡，减少旧样式的散乱感
- 正文富文本排版重新统一：
  - 段落、列表、引用、表格文字统一字号和行高
  - 正文标题层级重新收口，减少动态内容中大小忽大忽小的问题
- `30 秒看懂` 改成强调摘要卡：
  - 增加高亮底色、左侧强调条和标题胶囊
  - 提升摘要卡在正文前的视觉辨识度

### 里程碑补充：部署脚本批处理与防卡死

- `scripts/deploy.sh` 改成按阶段输出明确计时日志：
  - 本地 `rsync`、远端批处理、smoke 检查都会打印开始和结束耗时
  - 日志前缀区分 `local` 与 `remote`，避免本地时区和服务器时区混淆
- 远端校验改成单 SSH 会话批处理：
  - `php -l`
  - `php think clear`
  - API 文档生成与回写权限
- 所有远端 `sudo` 改为 `sudo -n`：
  - 避免凭据状态异常时静默等待交互输入
  - 减少部署过程“看起来卡住”的假死现象
- `.deployignore` 新增 `.codex/`，避免本地辅助目录参与部署扫描
