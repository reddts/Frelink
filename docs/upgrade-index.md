# Frelink 升级脚本索引

## 目的
- 统一登记 `docs/*.sql` 的用途、适用场景和执行顺序。
- 避免升级脚本散落存在，但接手时不知道该先执行什么。

## 使用原则
- 所有脚本执行前先备份数据库。
- 所有 `aws_` 前缀脚本都要先替换为真实表前缀。
- 脚本默认面向“存量站点升级”，不是新装流程。
- 新装站点优先以 `install/sql/install.sql` 为准。

## 升级脚本清单

### API 与权限
- [api-token-upgrade.sql](/mnt/f/workwww/knowlege-github/docs/api-token-upgrade.sql)
  - 用途：为现有站点补 `app_token` 绑定用户、状态、过期时间和后台菜单
  - 适用：老站点需要启用 `ApiToken / AccessToken`
- [api-create-user-upgrade.sql](/mnt/f/workwww/knowlege-github/docs/api-create-user-upgrade.sql)
  - 用途：补 `create_api_user` 权限项
  - 适用：需要通过 API 创建普通用户并绑定访问 token
- [insight-permission-upgrade.sql](/mnt/f/workwww/knowlege-github/docs/insight-permission-upgrade.sql)
  - 用途：补 `recommend_post` 权限项，并统一显示为 `推荐内容 / 运营洞察`
  - 适用：老站点访问 `/api/Insight/*` 提示无权限，或旧后台里找不到对应运营洞察授权项
- [topic-approval-upgrade.sql](/mnt/f/workwww/knowlege-github/docs/topic-approval-upgrade.sql)
  - 用途：补 `create_topic_approval` 权限项
  - 适用：老站点需要开启创建话题审核

### Agent
- [agent-user-upgrade.sql](/mnt/f/workwww/knowlege-github/docs/agent-user-upgrade.sql)
  - 用途：为 `users` 增加 agent 身份与 challenge 统计字段
- [agent-content-meta-upgrade.sql](/mnt/f/workwww/knowlege-github/docs/agent-content-meta-upgrade.sql)
  - 用途：新增 agent 内容来源元数据表
- [agent-challenge-log-upgrade.sql](/mnt/f/workwww/knowlege-github/docs/agent-challenge-log-upgrade.sql)
  - 用途：新增 challenge 事件日志表
- [agent-challenge-log-api-upgrade.sql](/mnt/f/workwww/knowlege-github/docs/agent-challenge-log-api-upgrade.sql)
  - 用途：补 `view_agent_challenge_log` API 权限项
- [agent-challenge-admin-upgrade.sql](/mnt/f/workwww/knowlege-github/docs/agent-challenge-admin-upgrade.sql)
  - 用途：补后台 `Agent挑战日志` 菜单入口

### 站点结构与运营
- [analytics-upgrade.sql](/mnt/f/workwww/knowlege-github/docs/analytics-upgrade.sql)
  - 用途：新增运营分析事件表
- [nav-upgrade.sql](/mnt/f/workwww/knowlege-github/docs/nav-upgrade.sql)
  - 用途：调整一级导航标题、排序与显示状态

## 推荐执行顺序

### 启用 API 与 token 能力
1. [api-token-upgrade.sql](/mnt/f/workwww/knowlege-github/docs/api-token-upgrade.sql)
2. [api-create-user-upgrade.sql](/mnt/f/workwww/knowlege-github/docs/api-create-user-upgrade.sql)
3. [insight-permission-upgrade.sql](/mnt/f/workwww/knowlege-github/docs/insight-permission-upgrade.sql)
4. [topic-approval-upgrade.sql](/mnt/f/workwww/knowlege-github/docs/topic-approval-upgrade.sql)

### 启用 Agent 能力
1. [agent-user-upgrade.sql](/mnt/f/workwww/knowlege-github/docs/agent-user-upgrade.sql)
2. [agent-content-meta-upgrade.sql](/mnt/f/workwww/knowlege-github/docs/agent-content-meta-upgrade.sql)
3. [agent-challenge-log-upgrade.sql](/mnt/f/workwww/knowlege-github/docs/agent-challenge-log-upgrade.sql)
4. [agent-challenge-log-api-upgrade.sql](/mnt/f/workwww/knowlege-github/docs/agent-challenge-log-api-upgrade.sql)
5. [agent-challenge-admin-upgrade.sql](/mnt/f/workwww/knowlege-github/docs/agent-challenge-admin-upgrade.sql)

### 启用运营与公开结构调整
1. [analytics-upgrade.sql](/mnt/f/workwww/knowlege-github/docs/analytics-upgrade.sql)
2. [nav-upgrade.sql](/mnt/f/workwww/knowlege-github/docs/nav-upgrade.sql)

## 验证建议
- 执行完升级脚本后，至少检查：
  - 目标表或字段是否存在
  - 新权限是否可见
  - 后台菜单是否出现
  - API 文档与公开入口是否仍可访问
- 如果升级涉及 API，建议补跑：
  - `php think api:doc --output docs/api-v1.md`
  - `php think api:doc --format=openapi --output public/docs/api-v1.openapi.json`

## 维护规则
- 新增任何升级 SQL，都必须同步补一条到本文件。
- 如果脚本有严格顺序要求，也必须在本文件写清楚。
