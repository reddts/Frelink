# Ant Design Pro 后台迁移开发计划与实施规范

## 1. 文档目的

本文档用于指导将当前 `Frelink` / `FreCenter` 的 PHP 模板后台迁移为基于 Ant Design Pro 的新后台。目标不是局部美化，而是形成一套可由 Claude 按阶段持续交付的完整实施方案。

本文档覆盖：

- 现状分析
- 迁移目标
- 目标架构
- 分阶段开发计划
- 前后端实施规范
- API 设计约束
- 权限与菜单迁移规则
- 自动化测试方案
- CI / 验收标准
- Claude 执行要求

本文档不包含：

- 业务需求变更
- 数据模型大改
- 当前前台页面改造
- 一次性硬切生产方案

## 2. 当前后台现状

### 2.1 后台入口与基础结构

- 后台入口文件：`public/admin.php`
- 后台控制器基类：`app/common/controller/Backend.php`
- 后台首页控制器：`app/backend/Index.php`
- 当前后台渲染方式：
  - PHP 模板直出
  - 基于 `TableBuilder` / `FormBuilder` 生成页面
  - 通过服务端模板、局部 AJAX、弹窗 iframe、PJAX 混合驱动

### 2.2 当前后台模板关键文件

- 公共壳层：`app/backend/view/block.php`
- 表单模板：`app/backend/view/global/form/layout.php`
- 列表模板：`app/backend/view/global/table/layout.php`
- 登录页：`app/backend/view/index/login.php`

### 2.3 当前后台模块清单

#### 核心模块

- 首页：`app/backend/Index.php`

#### 系统管理

- `app/backend/admin/Auth.php`
- `app/backend/admin/Config.php`
- `app/backend/admin/Dict.php`
- `app/backend/admin/DictType.php`
- `app/backend/admin/Group.php`
- `app/backend/admin/Menu.php`
- `app/backend/admin/Theme.php`

#### 内容管理

- `app/backend/content/Announce.php`
- `app/backend/content/Answer.php`
- `app/backend/content/Approval.php`
- `app/backend/content/Article.php`
- `app/backend/content/Category.php`
- `app/backend/content/Column.php`
- `app/backend/content/Feature.php`
- `app/backend/content/Help.php`
- `app/backend/content/Page.php`
- `app/backend/content/Question.php`
- `app/backend/content/Report.php`
- `app/backend/content/Topic.php`

#### 扩展能力

- `app/backend/extend/Database.php`
- `app/backend/extend/Links.php`
- `app/backend/extend/Log.php`
- `app/backend/extend/RouteRule.php`
- `app/backend/extend/Task.php`
- `app/backend/extend/Token.php`

#### 会员管理

- `app/backend/member/Action.php`
- `app/backend/member/Forbidden.php`
- `app/backend/member/IntegralGroup.php`
- `app/backend/member/IntegralRule.php`
- `app/backend/member/NotifySetting.php`
- `app/backend/member/Permission.php`
- `app/backend/member/ReputationGroup.php`
- `app/backend/member/Users.php`
- `app/backend/member/Verify.php`

#### 插件与微信

- `app/backend/plugin/Plugins.php`
- `app/backend/plugin/Upgrade.php`
- `app/backend/wechat/Account.php`
- `app/backend/wechat/TemplateMessage.php`
- `app/backend/wechat/WeChatFactor.php`

### 2.4 当前架构问题

- 模板直出、局部 AJAX、iframe 弹窗、PJAX 混用，维护成本高。
- 列表页和表单页强依赖服务端 Builder，前端组件复用能力弱。
- 权限、菜单、页面结构以服务端渲染为中心，不适合现代单页后台。
- 页面样式与模板耦合较深，无法低成本引入统一设计系统。
- 自动化测试缺失，页面回归成本高。
- 前后端职责未分离，难以支持渐进式演进。

## 3. 迁移目标

### 3.1 总目标

构建一个新的 `admin-pro` 后台应用，使用 Ant Design Pro 作为主框架，实现：

- 独立前端项目
- 基于 API 的后台管理界面
- 统一菜单、路由、权限、表单、表格、弹窗、上传与通知体系
- 完整自动化测试与 CI 验收链路
- 支持旧后台与新后台双轨并行，逐模块替换

### 3.2 非目标

- 不要求首阶段完全删除旧后台模板
- 不要求一次性交付所有模块
- 不在首阶段重构业务表结构
- 不在首阶段重写所有插件后台

### 3.3 迁移原则

- 先并行，后替换，不允许一刀切。
- 先高频，后低频，优先迁移登录、首页、用户、内容核心模块。
- 先接口化，后页面化，服务端先稳定输出 API。
- 所有新功能必须只写在新后台，不再新增旧模板后台功能。
- 所有迁移模块必须带自动化测试，不接受“页面可用但无测试”。

## 4. 目标架构

## 4.1 推荐目录结构

```text
/
├── app/
├── public/
├── docs/
├── admin-pro/
│   ├── src/
│   │   ├── app.tsx
│   │   ├── access.ts
│   │   ├── services/
│   │   │   ├── api/
│   │   │   ├── auth.ts
│   │   │   ├── menu.ts
│   │   │   └── types.ts
│   │   ├── pages/
│   │   │   ├── Auth/
│   │   │   ├── Dashboard/
│   │   │   ├── System/
│   │   │   ├── Content/
│   │   │   ├── Member/
│   │   │   ├── Extend/
│   │   │   ├── Plugin/
│   │   │   └── Wechat/
│   │   ├── components/
│   │   │   ├── PageTable/
│   │   │   ├── PageForm/
│   │   │   ├── UploadField/
│   │   │   ├── PermissionGuard/
│   │   │   └── StatusTag/
│   │   ├── layouts/
│   │   ├── utils/
│   │   ├── hooks/
│   │   └── constants/
│   ├── tests/
│   │   ├── e2e/
│   │   ├── integration/
│   │   ├── contracts/
│   │   └── fixtures/
│   ├── mock/
│   ├── package.json
│   ├── tsconfig.json
│   └── playwright.config.ts
└── ...
```

### 4.2 架构分层

#### 前端层

- Ant Design Pro 应用壳
- 路由层
- 页面容器层
- 通用业务组件层
- API 请求层
- 状态与权限层

#### 服务端层

- 认证 API
- 菜单 API
- 列表 API
- 详情 API
- 编辑 API
- 上传 API
- 统计 API

#### 兼容层

- 保留旧后台控制器
- 新增 `/admin-api/*` 或 `/admin.php/api/*` 风格接口
- 在迁移期同时支持旧模板渲染与新前端调用

## 5. 技术策略

### 5.1 前端技术原则

- 使用 TypeScript。
- 使用 Ant Design Pro 官方推荐模式构建。
- 所有页面必须组件化，不允许页面内堆积大段逻辑。
- 所有接口调用必须走统一 request 封装。
- 所有表格统一基于二次封装的 `PageTable`。
- 所有表单统一基于二次封装的 `PageForm`。
- 所有权限控制统一通过 access 层处理。

### 5.2 服务端技术原则

- 旧控制器保留。
- 新接口按资源化组织，不允许继续输出 HTML 给新后台使用。
- 返回结构统一。
- 认证失败、权限失败、校验失败、业务失败必须有稳定错误码。
- 分页格式统一，不允许不同模块使用不同返回字段名。

## 6. API 统一规范

### 6.1 响应格式

成功：

```json
{
  "code": 1,
  "msg": "ok",
  "data": {},
  "request_id": "uuid"
}
```

失败：

```json
{
  "code": 0,
  "msg": "error message",
  "error_code": "PERMISSION_DENIED",
  "data": null,
  "request_id": "uuid"
}
```

### 6.2 分页格式

```json
{
  "code": 1,
  "msg": "ok",
  "data": {
    "items": [],
    "pagination": {
      "current": 1,
      "page_size": 20,
      "total": 200
    }
  }
}
```

### 6.3 列表查询参数规范

- `page`
- `page_size`
- `keyword`
- `sort_field`
- `sort_order`
- `filters`

禁止：

- 不允许模块私自改用 `rows` / `limit` / `offset` 混写
- 不允许返回 `list`、`rows`、`data` 三种分页列表字段并存

### 6.4 表单接口规范

- `GET /resource/{id}` 获取详情
- `POST /resource` 新建
- `PUT /resource/{id}` 更新
- `DELETE /resource/{id}` 删除
- `POST /resource/batch-delete` 批量删除
- `POST /resource/{id}/status` 修改状态

### 6.5 上传接口规范

- 返回文件 URL、文件名、大小、MIME、存储路径、缩略图 URL
- 统一错误处理
- 支持图片、文件、多文件

## 7. 权限与菜单迁移规范

### 7.1 当前权限来源

当前权限逻辑主要集中在：

- `app/common/controller/Backend.php`
- `AuthHelper`
- 数据库内后台菜单与规则

### 7.2 新后台权限模型

服务端负责：

- 登录态校验
- 用户权限点计算
- 菜单树输出
- 路由可访问性判定

前端负责：

- 菜单渲染
- 页面级访问控制
- 按钮级访问控制
- 无权限组件隐藏

### 7.3 菜单接口返回建议

```json
[
  {
    "name": "system",
    "path": "/system",
    "label": "系统管理",
    "icon": "SettingOutlined",
    "children": [
      {
        "name": "system-config",
        "path": "/system/config",
        "label": "系统配置",
        "permission": "admin.Config/index"
      }
    ]
  }
]
```

### 7.4 权限点命名规则

- 优先复用现有 `控制器/动作` 权限点
- 前端 route permission 与服务端 permission 保持一一对应
- 不允许前端自己发明另一套权限命名体系

## 8. 页面设计规范

### 8.1 页面分类

- 登录页
- 仪表盘
- 列表页
- 新建页
- 编辑页
- 详情页
- 配置页
- 弹窗页
- 审核流页

### 8.2 列表页统一规范

必须具备：

- 页面标题
- 搜索区
- 表格区
- 批量操作区
- 行级操作区
- 分页
- 空态
- 错误态
- 加载态

建议统一列类型：

- 文本
- 状态
- 标签
- 图片
- 时间
- 数字
- 操作列

### 8.3 表单页统一规范

必须具备：

- 页面标题
- 返回入口
- 表单分组
- 提交按钮
- 重置或取消按钮
- 提交前校验
- 提交中状态
- 成功后跳转或提示

### 8.4 Dashboard 规范

- 卡片统计
- 趋势图
- 待处理事项
- 快捷入口

## 9. 模块迁移优先级

### P0：基础能力

- 登录
- 获取当前管理员信息
- 菜单接口
- 权限接口
- 统一 request 封装
- 基础 layout
- 全局错误处理

### P1：高频核心模块

- 后台首页 Dashboard
- 用户管理
- 问题管理
- 文章管理
- 话题管理
- 审核管理

### P2：系统配置与内容扩展

- 系统配置
- 菜单管理
- 权限管理
- 分类、页面、公告、帮助中心
- 链接、路由、日志、任务

### P3：低频与插件域

- 插件管理
- 升级模块
- 微信模块
- 低频维护型页面

## 10. 分阶段实施计划

## 阶段 0：迁移准备

### 目标

- 搭建新后台项目骨架
- 定义基础规范
- 梳理接口缺口

### 交付物

- `admin-pro/` 项目初始化
- 统一 ESLint / Prettier / TypeScript 规则
- 统一 request 封装
- `.env` 示例
- 菜单 / 权限 / 用户信息接口草案
- 本文档落库

### 验收

- 新项目可启动
- 登录页骨架可访问
- 基础布局可渲染

## 阶段 1：认证与框架层

### 目标

- 打通登录态
- 完成主布局
- 支持动态菜单与权限校验

### 工作项

- 登录页
- 获取当前用户信息接口
- 退出登录接口
- access.ts 权限逻辑
- 动态菜单渲染
- 404 / 403 / 500 页面

### 验收

- 未登录自动跳登录
- 登录后加载菜单
- 无权限页面正确拦截

## 阶段 2：通用业务组件层

### 目标

- 用组件沉淀替代旧 Builder

### 工作项

- `PageTable`
- `PageForm`
- `PageModal`
- `UploadField`
- `RichTextField`
- `StatusSwitch`
- `SearchFilterBar`

### 验收

- 至少 2 个页面完成迁移并只使用通用组件

## 阶段 3：高频模块迁移

### 目标

- 完成高频模块业务闭环

### 工作项

- Dashboard
- 用户管理
- 问题管理
- 文章管理
- 话题管理
- 审核管理

### 验收

- 上述模块支持列表、搜索、分页、增删改查、状态切换、批量操作

## 阶段 4：系统与扩展模块迁移

### 目标

- 迁移后台剩余主要管理能力

### 工作项

- 配置管理
- 菜单管理
- 权限管理
- 分类 / 页面 / 公告 / 帮助
- 路由 / 链接 / 日志 / 任务 / Token / Database

### 验收

- 后台日常管理工作可完全在新后台完成

## 阶段 5：插件与低频模块迁移

### 目标

- 收尾并完成旧后台替换条件

### 工作项

- 插件管理
- 升级管理
- 微信模块
- 低频页面

### 验收

- 新后台覆盖率达到 90% 以上

## 阶段 6：切换与收敛

### 目标

- 生产切流
- 保留回滚方案

### 工作项

- 新旧后台切换开关
- 日志与错误监控
- 性能基线校验
- 回滚文档

### 验收

- 可灰度开启
- 可快速回退到旧后台

## 11. Claude 开发执行规范

### 11.1 总原则

- 不允许直接整站推倒重来。
- 不允许先写 UI 后补接口。
- 不允许无测试交付。
- 不允许绕过统一组件重复造轮子。
- 不允许把业务逻辑散落在页面 JSX 中。

### 11.2 Claude 每次提交必须包含

- 改动范围说明
- 文件清单
- 已完成内容
- 未完成内容
- 风险说明
- 测试结果

### 11.3 Claude 每次提交禁止行为

- 修改无关模块
- 擅自变更接口字段
- 引入未经说明的状态管理方案
- 绕过权限控制
- 用 mock 替代真实接口后不还原

### 11.4 Claude 单次任务粒度建议

- 一次只做一个模块或一个横切能力
- 先写接口类型，再写页面
- 先写测试，再补实现或同步实现

## 12. 自动化测试总方案

迁移项目必须自带完整自动化测试，不接受“后补”。

### 12.1 测试分层

#### A. 单元测试

覆盖对象：

- 工具函数
- 数据转换函数
- 权限判断函数
- 表格列格式化函数
- 表单 schema 转换函数
- hooks
- services 请求适配器

推荐目录：

```text
admin-pro/src/**/*.test.ts
admin-pro/src/**/*.test.tsx
```

#### B. 组件集成测试

覆盖对象：

- `PageTable`
- `PageForm`
- `UploadField`
- `PermissionGuard`
- Dashboard 卡片组件
- 搜索栏与批量操作栏

验证点：

- 正确渲染
- 加载态
- 空态
- 异常态
- 交互事件
- 权限隐藏

#### C. 接口契约测试

覆盖对象：

- 登录接口
- 当前用户接口
- 菜单接口
- 分页列表接口
- 新增 / 编辑 / 删除接口
- 上传接口

验证点：

- 字段完整性
- 类型正确性
- 错误码一致性
- 分页结构一致性

#### D. E2E 端到端测试

覆盖对象：

- 登录
- 退出
- 动态菜单
- 典型列表页
- 典型编辑页
- 上传
- 审核流
- 权限限制

#### E. 可视回归测试

覆盖对象：

- 登录页
- Dashboard
- 典型列表页
- 典型表单页
- 弹窗
- 移动端窄屏布局

### 12.2 推荐测试目录

```text
admin-pro/tests/
├── e2e/
│   ├── auth.spec.ts
│   ├── dashboard.spec.ts
│   ├── users.spec.ts
│   ├── questions.spec.ts
│   ├── articles.spec.ts
│   ├── permissions.spec.ts
│   └── uploads.spec.ts
├── integration/
│   ├── page-table.test.tsx
│   ├── page-form.test.tsx
│   ├── permission-guard.test.tsx
│   └── search-filter-bar.test.tsx
├── contracts/
│   ├── auth.contract.test.ts
│   ├── menu.contract.test.ts
│   ├── pagination.contract.test.ts
│   └── upload.contract.test.ts
└── fixtures/
    ├── users.ts
    ├── questions.ts
    └── auth.ts
```

### 12.3 单元测试最低要求

- 关键工具函数覆盖率 95% 以上
- hooks 覆盖率 90% 以上
- services 层覆盖率 90% 以上

### 12.4 集成测试最低要求

- 通用组件必须全覆盖
- 每个通用组件至少覆盖：
  - 正常渲染
  - 加载态
  - 失败态
  - 关键交互

### 12.5 E2E 最低覆盖清单

必须实现以下场景：

1. 管理员登录成功
2. 未登录访问后台跳转登录
3. 无权限用户看不到指定菜单
4. 用户列表搜索与分页正常
5. 用户编辑保存成功
6. 问题列表搜索与状态切换正常
7. 文章列表与编辑正常
8. 上传组件上传图片成功
9. 审核流页面审批成功
10. 退出登录成功

### 12.6 契约测试最低覆盖清单

必须验证：

- `code/msg/data` 是否存在
- 分页字段是否统一
- 错误结构是否统一
- 菜单树字段是否完整
- 当前用户接口是否返回权限集合

### 12.7 可视回归测试最低要求

至少生成以下截图快照：

- 登录页
- Dashboard
- 用户列表
- 问题列表
- 编辑表单
- 审核弹窗

### 12.8 测试数据规范

- 禁止复用生产数据
- 使用独立 fixtures
- 每个 E2E 用例必须可重复执行
- 测试数据创建与清理必须自动化

## 13. 自动化测试实施细则

### 13.1 前端测试建议

- 单元与集成测试：
  - 使用 Vitest 或同级方案
  - 使用 Testing Library
- E2E：
  - 使用 Playwright
- API Mock：
  - 开发期可使用 MSW
  - 契约测试必须对真实测试环境接口执行

### 13.2 服务端测试建议

新增 API 时必须补：

- 接口响应结构测试
- 权限测试
- 参数校验测试
- 关键业务失败路径测试

### 13.3 测试环境要求

- 独立测试数据库
- 独立后台测试账号
- 独立上传目录或对象存储前缀
- 可重复初始化的测试种子

## 14. CI / CD 验收要求

### 14.1 CI 必跑项

每次提交必须执行：

1. 依赖安装
2. TypeScript 类型检查
3. ESLint
4. 单元测试
5. 集成测试
6. 契约测试
7. E2E 冒烟测试
8. 构建产物检查

### 14.2 质量门槛

- 类型检查必须 0 error
- ESLint 必须 0 error
- 单元测试通过率 100%
- 集成测试通过率 100%
- E2E 冒烟通过率 100%
- 主分支覆盖率最低 80%
- 核心模块覆盖率最低 90%

### 14.3 合并门禁

满足以下条件才允许合并：

- 代码评审通过
- 测试全绿
- 无高危安全问题
- 文档同步更新

## 15. Definition of Done

一个迁移模块只有满足以下全部条件才算完成：

- 页面功能完成
- 接口接入真实可用
- 权限控制完成
- 加载、空态、异常态完整
- 单元测试完成
- 集成测试完成
- E2E 覆盖完成
- 文档更新完成
- 代码评审通过

## 16. 风险与应对

### 风险 1：接口不统一导致前端反复适配

应对：

- 先做统一 API 规范
- 先补契约测试

### 风险 2：一页一页重写导致组件体系失控

应对：

- 先沉淀 `PageTable` / `PageForm`
- 先定义列和字段 schema 规范

### 风险 3：权限迁移不完整导致线上越权

应对：

- 服务端保持最终权限校验
- 增加权限 E2E 与接口测试

### 风险 4：新旧后台并行期间维护成本过高

应对：

- 明确“新功能只进新后台”
- 模块迁移完毕后及时冻结旧模块

### 风险 5：测试环境不稳定导致 CI 失真

应对：

- 固定测试种子
- 固定测试账号
- 上传目录独立隔离

## 17. Claude 交付顺序建议

推荐 Claude 按如下顺序执行：

1. 初始化 `admin-pro` 项目
2. 写基础工程规范与 request 封装
3. 打通登录 / 当前用户 / 菜单 / 权限
4. 落地 layout 与 access
5. 实现 `PageTable` / `PageForm`
6. 迁移 Dashboard
7. 迁移用户管理
8. 迁移问题管理
9. 迁移文章管理
10. 迁移话题与审核
11. 迁移系统配置与菜单权限
12. 迁移扩展与插件模块
13. 收敛旧后台入口

## 18. Claude 每阶段输出模板

Claude 每完成一阶段，必须输出：

```text
阶段：
目标：
完成范围：
新增文件：
修改文件：
接口变更：
测试清单：
未完成项：
风险与后续建议：
```

## 19. 最终验收标准

达到以下标准才可认为迁移项目进入可切流状态：

- 新后台已覆盖核心高频模块
- 旧后台仅保留低频或未迁移模块
- 认证、菜单、权限、上传、表单、表格全部稳定
- 自动化测试体系可持续运行
- 团队可以在新后台继续开发而非回退旧模板系统

## 20. 执行结论

该项目必须按“接口先行、框架先行、组件先行、模块分批、测试强制”的方式推进。

不接受以下迁移方式：

- 直接套一个 Ant Design Pro 外壳后把旧后台 iframe 进去
- 先写页面，接口字段边写边猜
- 高度依赖 mock，长期不接真实接口
- 无测试交付
- 大范围同时改动导致无法回滚

推荐执行策略：

- 双轨并行
- 小步迭代
- 每阶段都可运行
- 每阶段都可回退
- 每阶段都可验收

