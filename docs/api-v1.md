# Frelink API v1

Frelink 的 API 以 `app/api/v1` 为入口，当前更适合作为移动端、站点集成层和 agent 辅助能力的基础接口，而不是直接无约束开放的自动化控制面。

## 路由规则

- 默认路由格式：`/api/{controller}/{function}`
- 版本通过请求头 `version` 指定，默认值为 `v1`
- 例如：`GET /api/Common/config`

## 认证方式

- 登录态通过请求头 `UserToken` 传递
- 敏感接口由控制器内部 `needLogin` 控制
- 当前建议为 agent 单独准备低权限账号，不直接复用管理员账号

## 关键请求头

- `Content-Type: application/json`
- `version: v1`
- `UserToken: <token>`

## 常用公开接口

- `GET /api/Common/config`
- `GET /api/Common/search?q=关键词`
- `GET /api/Common/hot_search`
- `GET /api/Question/index`
- `GET /api/Question/detail?id=1`
- `GET /api/Article/index`
- `GET /api/Article/detail?id=1`
- `GET /api/Topic/index`
- `GET /api/Page/detail`
- `POST /api/Insight/track`

## 常用登录接口

- `POST /api/Question/publish`
- `POST /api/Article/publish`
- `POST /api/Common/upload`
- `POST /api/Common/save_draft`
- `POST /api/Common/set_vote`
- `POST /api/Common/update_focus`
- `GET /api/Insight/summary?days=7`
- `GET /api/Insight/keywords?days=7&limit=10`
- `GET /api/Insight/opportunities?days=7&limit=10`
- `GET /api/Insight/content_trends?days=7&limit=10`
- `GET /api/Insight/topic_trends?days=7&limit=10`
- `GET /api/Insight/recommendations?days=7&limit=10`

## 运营洞察接口说明

- `Insight` 模块只统计最近窗口，不使用站点累计点击率。
- 当前支持固定时间窗口：`1 / 3 / 7 / 30` 天。
- 推荐让 agent 读取 `summary -> opportunities -> recommendations` 三组接口，生成选题与巡检建议。
- `POST /api/Insight/track` 用于记录匿名或登录用户的曝光、点击、详情阅读事件。
- `GET /api/Insight/content_trends` 返回近窗口内的曝光、点击、详情阅读和窗口 CTR。
- `GET /api/Insight/opportunities` 返回高频搜索词、现有内容覆盖量和内容缺口建议。

## 推荐的 agent 使用边界

- 允许：
  - 搜索词采集与选题建议
  - 内容健康巡检
  - 发布后链接检查
  - 收录状态巡检
  - 低风险草稿生成
- 不建议直接开放：
  - 删除内容
  - 修改权限
  - 自动正式发布
  - 无审批的生产运维操作

## 接入前建议

1. 为 agent 单独创建账号与权限组。
2. 先补齐接口 smoke test，再接入自动化发布流程。
3. 为发布、删除、推荐、置顶等动作记录审计日志。
4. 对登录、短信、发布、评论等接口增加限频与监控。

## smoke test 建议

```bash
curl -H "version: v1" https://your-domain/api/Common/config
curl -H "version: v1" "https://your-domain/api/Common/search?q=frelink"
curl -H "version: v1" "https://your-domain/api/Question/index?page=1&page_size=5"
curl -H "version: v1" "https://your-domain/api/Article/index?page=1&page_size=5"
curl -X POST -H "version: v1" -H "Content-Type: application/json" \
  -d '{"event_type":"detail_view","item_type":"article","item_id":1,"visitor_token":"debug-token","source":"smoke_test"}' \
  https://your-domain/api/Insight/track
```

## 后续改造方向

- 增加 OpenAPI 文档
- 统一错误码规范
- 为 agent 提供只读巡检 token
- 为发布流程增加审批与回滚机制
- 为 Insight 增加后台面板与定时报表
