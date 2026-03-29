# Frelink API v1

自动生成时间：2026-03-29 11:29:59
生成命令：`php think api:doc --output docs/api-v1.md`

Frelink 的 API 以 `app/api/v1` 为入口，当前更适合作为移动端、站点集成层和 agent 辅助能力的基础接口，而不是直接无约束开放的自动化控制面。

## 路由规则

- 默认路由格式：`/api/{controller}/{function}`
- 版本通过请求头 `version` 指定，默认值为 `v1`
- 例如：`GET /api/Common/config`

## 认证方式

- 登录态通过请求头 `UserToken` 传递
- 后台创建的 API 认证 token 可以通过请求头 `ApiToken` 或 `AccessToken` 传递
- 当 token 绑定了用户 UID 时，可直接作为该用户的 API 登录态使用
- 敏感接口由控制器内部 `needLogin` 控制
- 当前建议为 agent 单独准备低权限账号，不直接复用管理员账号

## 关键请求头

- `Content-Type: application/json`
- `version: v1`
- `UserToken: <token>`

## 统一返回与错误码约定

- 成功时返回 `code=1`
- 失败时返回 `code=0`，并在 `msg` 中给出说明
- `time` 表示服务端返回时刻的 Unix 时间戳
- `request_id` 用于串联日志和排障
- `error_code` 仅在失败响应中返回，供程序侧做稳定分支判断
- `data` 承载接口实际数据
- 不同接口可能会在 `data` 中承载不同结构，调用前以具体接口为准

```json
{
  "code": 1,
  "msg": "请求成功",
  "time": 1710000000,
  "request_id": "req_0123456789abcdef",
  "data": {}
}
```

```json
{
  "code": 0,
  "msg": "参数错误",
  "time": 1710000000,
  "request_id": "req_0123456789abcdef",
  "error_code": "INVALID_REQUEST",
  "data": {}
}
```

## 接口清单

### `Account`

- 需要登录的方法：`my`

| 方法 | 路由 | HTTP | 鉴权 | 参数 | 说明 |
| --- | --- | --- | --- | --- | --- |
| `login` | `/api/Account/login` | `POST` | 公开 | - | 自动生成接口说明 |
| `my` | `/api/Account/my` | `GET` | 需要登录 | - | 自动生成接口说明 |
| `register` | `/api/Account/register` | `POST` | 公开 | - | 自动生成接口说明 |
| `reset_password` | `/api/Account/reset_password` | `POST` | 公开 | - | 自动生成接口说明 |
| `wxminiapp_bind` | `/api/Account/wxminiapp_bind` | `POST` | 公开 | - | 自动生成接口说明 |
| `wxminiapp_login` | `/api/Account/wxminiapp_login` | `GET` | 公开 | `code` | 自动生成接口说明，参数：code |

### `Article`

- 需要登录的方法：`publish`, `manager`, `remove_article`
- `manager` 支持 `recommend`、`set_top`、`rollback`，其中 `rollback` 会回退到上一版修订快照

| 方法 | 路由 | HTTP | 鉴权 | 参数 | 说明 |
| --- | --- | --- | --- | --- | --- |
| `comments` | `/api/Article/comments` | `GET` | 公开 | `article_id`, `page`, `page_size`, `sort` | 返回评论列表 |
| `detail` | `/api/Article/detail` | `GET` | 公开 | `id` | 返回详情数据 |
| `index` | `/api/Article/index` | `GET` | 公开 | `category_id`, `page`, `page_size`, `sort`, `uid`, `words_count` | 返回列表数据 |
| `manager` | `/api/Article/manager` | `GET` | 需要登录 | `id`, `type`, `is_recommend`, `set_top` | 管理文章操作，支持推荐、置顶和回滚 |
| `publish` | `/api/Article/publish` | `POST` | 需要登录 | - | 发布或修改内容 |
| `relation` | `/api/Article/relation` | `GET` | 公开 | `id`, `page`, `page_size` | 返回相关文章 |
| `remove_article` | `/api/Article/remove_article` | `GET` | 需要登录 | `id` | 自动生成接口说明，参数：id |

### `Ask`

| 方法 | 路由 | HTTP | 鉴权 | 参数 | 说明 |
| --- | --- | --- | --- | --- | --- |
| `lists` | `/api/Ask/lists` | `GET` | 公开 | `category_id`, `page`, `page_size`, `relation_uid`, `sort`, `type`, `words_count` | 自动生成接口说明，参数：category_id, page, page_size, relation_uid, sort, type, words_count |

### `Captcha`

| 方法 | 路由 | HTTP | 鉴权 | 参数 | 说明 |
| --- | --- | --- | --- | --- | --- |
| `check` | `/api/Captcha/check` | `POST` | 公开 | `x` | 自动生成接口说明，参数：x |
| `clear` | `/api/Captcha/clear` | `GET` | 公开 | - | 自动生成接口说明 |
| `generate` | `/api/Captcha/generate` | `GET` | 公开 | - | 自动生成接口说明 |
| `initialize` | `/api/Captcha/initialize` | `GET` | 公开 | `session_id` | 自动生成接口说明，参数：session_id |

### `Column`

- 需要登录的方法：`my`, `apply`, `collect`

| 方法 | 路由 | HTTP | 鉴权 | 参数 | 说明 |
| --- | --- | --- | --- | --- | --- |
| `apply` | `/api/Column/apply` | `POST` | 需要登录 | `cover`, `description`, `id`, `name` | 自动生成接口说明，参数：cover, description, id, name |
| `articles` | `/api/Column/articles` | `GET` | 公开 | - | 自动生成接口说明 |
| `collect` | `/api/Column/collect` | `POST` | 需要登录 | `article_id`, `column_id` | 自动生成接口说明，参数：article_id, column_id |
| `detail` | `/api/Column/detail` | `GET` | 公开 | `id` | 返回详情数据 |
| `index` | `/api/Column/index` | `GET` | 公开 | `page`, `page_size`, `sort`, `uid` | 返回列表数据 |
| `my` | `/api/Column/my` | `GET` | 需要登录 | `page`, `sort`, `verify` | 自动生成接口说明，参数：page, sort, verify |

### `Comment`

- 需要登录的方法：`save_comment`, `remove_comment`

| 方法 | 路由 | HTTP | 鉴权 | 参数 | 说明 |
| --- | --- | --- | --- | --- | --- |
| `get_comments` | `/api/Comment/get_comments` | `GET` | 公开 | `item_id`, `item_type`, `page`, `page_size`, `sort` | 自动生成接口说明，参数：item_id, item_type, page, page_size, sort |
| `remove_comment` | `/api/Comment/remove_comment` | `GET` | 需要登录 | `id`, `item_type` | 自动生成接口说明，参数：id, item_type |
| `save_comment` | `/api/Comment/save_comment` | `POST` | 需要登录 | `at_uid`, `item_id`, `item_type`, `message`, `pid` | 自动生成接口说明，参数：at_uid, item_id, item_type, message, pid |

### `Common`

- 需要登录的方法：`set_vote`, `update_focus`, `get_access_key`, `upload`, `remove_attach`, `save_draft`

| 方法 | 路由 | HTTP | 鉴权 | 参数 | 说明 |
| --- | --- | --- | --- | --- | --- |
| `announce` | `/api/Common/announce` | `GET` | 公开 | `id` | 自动生成接口说明，参数：id |
| `category` | `/api/Common/category` | `GET` | 公开 | `type` | 返回分类列表 |
| `check_update` | `/api/Common/check_update` | `GET` | 公开 | `edition_type`, `version_type` | 自动生成接口说明，参数：edition_type, version_type |
| `config` | `/api/Common/config` | `GET` | 公开 | - | 返回公开配置 |
| `get_access_key` | `/api/Common/get_access_key` | `GET` | 需要登录 | - | 自动生成接口说明 |
| `hot_search` | `/api/Common/hot_search` | `GET` | 公开 | `page`, `page_size` | 自动生成接口说明，参数：page, page_size |
| `mixed_list` | `/api/Common/mixed_list` | `GET` | 公开 | `category_id`, `page`, `page_size`, `relation_uid`, `sort`, `type`, `words_count` | 自动生成接口说明，参数：category_id, page, page_size, relation_uid, sort, type, words_count |
| `remove_attach` | `/api/Common/remove_attach` | `POST` | 需要登录 | `url` | 自动生成接口说明，参数：url |
| `save_draft` | `/api/Common/save_draft` | `POST` | 需要登录 | `data`, `item_id`, `item_type` | 自动生成接口说明，参数：data, item_id, item_type |
| `search` | `/api/Common/search` | `GET` | 公开 | `page`, `page_size`, `q`, `type` | 执行站内搜索 |
| `set_vote` | `/api/Common/set_vote` | `POST` | 需要登录 | `item_id`, `item_type`, `vote_value` | 自动生成接口说明，参数：item_id, item_type, vote_value |
| `sms` | `/api/Common/sms` | `GET` | 公开 | `mobile` | 自动生成接口说明，参数：mobile |
| `update_focus` | `/api/Common/update_focus` | `POST` | 需要登录 | `id`, `type` | 自动生成接口说明，参数：id, type |
| `upload` | `/api/Common/upload` | `POST` | 需要登录 | `access_key`, `path`, `upload_type` | 自动生成接口说明，参数：access_key, path, upload_type |

### `Favorite`

- 需要登录的方法：`*`

| 方法 | 路由 | HTTP | 鉴权 | 参数 | 说明 |
| --- | --- | --- | --- | --- | --- |
| `delete` | `/api/Favorite/delete` | `POST` | 需要登录 | `id` | 自动生成接口说明，参数：id |
| `detail` | `/api/Favorite/detail` | `GET` | 需要登录 | `id`, `page` | 返回详情数据 |
| `get_fav_tags` | `/api/Favorite/get_fav_tags` | `GET` | 需要登录 | `item_id`, `item_type` | 自动生成接口说明，参数：item_id, item_type |
| `index` | `/api/Favorite/index` | `GET` | 需要登录 | `page` | 返回列表数据 |
| `save_favorite` | `/api/Favorite/save_favorite` | `GET` | 需要登录 | `item_id`, `item_type`, `tag_id` | 自动生成接口说明，参数：item_id, item_type, tag_id |
| `save_favorite_tag` | `/api/Favorite/save_favorite_tag` | `POST` | 需要登录 | `description`, `is_public`, `title` | 自动生成接口说明，参数：description, is_public, title |

### `Inbox`

- 需要登录的方法：`*`

| 方法 | 路由 | HTTP | 鉴权 | 参数 | 说明 |
| --- | --- | --- | --- | --- | --- |
| `delete` | `/api/Inbox/delete` | `POST` | 需要登录 | `id` | 自动生成接口说明，参数：id |
| `detail` | `/api/Inbox/detail` | `GET` | 需要登录 | `page`, `recipient_uid` | 返回详情数据 |
| `getGpt` | `/api/Inbox/getGpt` | `GET` | 需要登录 | - | 自动生成接口说明 |
| `index` | `/api/Inbox/index` | `GET` | 需要登录 | - | 返回列表数据 |
| `send` | `/api/Inbox/send` | `POST` | 需要登录 | - | 自动生成接口说明 |
| `sendGpt` | `/api/Inbox/sendGpt` | `POST` | 需要登录 | - | 自动生成接口说明 |

### `Insight`

- 需要登录的方法：`summary`, `keywords`, `content_trends`, `topic_trends`, `topic_graph`, `opportunities`, `recommendations`, `publish_assist`, `weekly_execution`, `writing_workflow`, `agent_brief`, `agent_draft`
- 上述接口既可通过 `UserToken` 访问，也可通过后台绑定用户的 `ApiToken` / `AccessToken` 访问

| 方法 | 路由 | HTTP | 鉴权 | 参数 | 说明 |
| --- | --- | --- | --- | --- | --- |
| `content_trends` | `/api/Insight/content_trends` | `GET` | 需要登录 | `days`, `item_type`, `limit` | 返回内容曝光、点击和阅读趋势 |
| `keywords` | `/api/Insight/keywords` | `GET` | 需要登录 | `days`, `limit` | 返回最近窗口高频搜索词 |
| `opportunities` | `/api/Insight/opportunities` | `GET` | 需要登录 | `days`, `limit` | 返回搜索缺口与内容建议 |
| `publish_assist` | `/api/Insight/publish_assist` | `GET` | 需要登录 | `days`, `item_type`, `limit` | 返回发布选题与标题建议 |
| `recommendations` | `/api/Insight/recommendations` | `GET` | 需要登录 | `days`, `limit` | 返回运营建议动作 |
| `summary` | `/api/Insight/summary` | `GET` | 需要登录 | `days` | 返回最近窗口运营汇总 |
| `topic_graph` | `/api/Insight/topic_graph` | `GET` | 需要登录 | `days`, `limit` | 返回最近窗口主题共现图谱 |
| `topic_trends` | `/api/Insight/topic_trends` | `GET` | 需要登录 | `days`, `limit` | 返回主题趋势 |
| `track` | `/api/Insight/track` | `POST` | 公开 | - | 记录曝光、点击或详情阅读事件 |
| `weekly_execution` | `/api/Insight/weekly_execution` | `GET` | 需要登录 | `days`, `format`, `limit` | 返回本周执行清单 |
| `agent_brief` | `/api/Insight/agent_brief` | `GET` | 需要登录 | `days`, `format`, `item_type`, `limit`, `mode`, `topic` | 返回 agent 可直接消费的运营汇总、周执行清单、写作工作流和主题图谱 |
| `agent_draft` | `/api/Insight/agent_draft` | `POST` | 需要登录 | `days`, `item_id`, `item_type`, `limit`, `mode`, `topic` | 生成待审草稿并落库到 draft 表 |
| `writing_workflow` | `/api/Insight/writing_workflow` | `GET` | 需要登录 | `days`, `format`, `item_type`, `limit`, `mode`, `topic` | 返回自动筛选发文与手动指令发文的统一工作流，包含人工审核闸门 |

### `Invitation`

- 需要登录的方法：`*`

| 方法 | 路由 | HTTP | 鉴权 | 参数 | 说明 |
| --- | --- | --- | --- | --- | --- |
| `create` | `/api/Invitation/create` | `POST` | 需要登录 | `invitation_email`, `type` | 自动生成接口说明，参数：invitation_email, type |
| `index` | `/api/Invitation/index` | `GET` | 需要登录 | `page`, `page_size` | 返回列表数据 |
| `invite_list` | `/api/Invitation/invite_list` | `GET` | 需要登录 | `page`, `page_size` | 自动生成接口说明，参数：page, page_size |

### `Notify`

- 需要登录的方法：`*`

| 方法 | 路由 | HTTP | 鉴权 | 参数 | 说明 |
| --- | --- | --- | --- | --- | --- |
| `delete` | `/api/Notify/delete` | `GET` | 需要登录 | `id` | 自动生成接口说明，参数：id |
| `detail` | `/api/Notify/detail` | `GET` | 需要登录 | `id` | 返回详情数据 |
| `index` | `/api/Notify/index` | `GET` | 需要登录 | - | 返回列表数据 |
| `lists` | `/api/Notify/lists` | `GET` | 需要登录 | `page`, `type` | 自动生成接口说明，参数：page, type |
| `read` | `/api/Notify/read` | `GET` | 需要登录 | `id` | 自动生成接口说明，参数：id |
| `read_all` | `/api/Notify/read_all` | `GET` | 需要登录 | - | 自动生成接口说明 |

### `Page`

| 方法 | 路由 | HTTP | 鉴权 | 参数 | 说明 |
| --- | --- | --- | --- | --- | --- |
| `index` | `/api/Page/index` | `GET` | 公开 | `name` | 返回列表数据 |
| `score` | `/api/Page/score` | `GET` | 公开 | - | 自动生成接口说明 |

### `Pay`

- 需要登录的方法：`createOrder`, `qrcode`, `balance`

| 方法 | 路由 | HTTP | 鉴权 | 参数 | 说明 |
| --- | --- | --- | --- | --- | --- |
| `balance` | `/api/Pay/balance` | `POST` | 需要登录 | `order_id`, `password`, `url` | 自动生成接口说明，参数：order_id, password, url |
| `checkProvider` | `/api/Pay/checkProvider` | `GET` | 公开 | - | 自动生成接口说明 |
| `createOrder` | `/api/Pay/createOrder` | `POST` | 需要登录 | - | 自动生成接口说明 |
| `initialize` | `/api/Pay/initialize` | `GET` | 公开 | - | 自动生成接口说明 |
| `qrcode` | `/api/Pay/qrcode` | `GET` | 需要登录 | `gateway`, `notify_url`, `order_id`, `pay_type`, `return_url` | 自动生成接口说明，参数：gateway, notify_url, order_id, pay_type, return_url |

### `Permission`

- 需要登录的方法：`report`

| 方法 | 路由 | HTTP | 鉴权 | 参数 | 说明 |
| --- | --- | --- | --- | --- | --- |
| `report` | `/api/Permission/report` | `POST` | 需要登录 | `item_id`, `item_type`, `reason`, `report_type` | 自动生成接口说明，参数：item_id, item_type, reason, report_type |

### `Question`

- 需要登录的方法：`publish`, `remove_answer`, `remove_question`, `manager`, `save_question_invite`, `answer_editor`, `save_answer`, `set_best_answer`, `remove_answer_comment`
- `manager` 支持 `recommend`、`set_top`、`rollback`，其中 `rollback` 会回退到上一版修订快照

| 方法 | 路由 | HTTP | 鉴权 | 参数 | 说明 |
| --- | --- | --- | --- | --- | --- |
| `answer` | `/api/Question/answer` | `GET` | 公开 | `id` | 自动生成接口说明，参数：id |
| `answer_comments` | `/api/Question/answer_comments` | `GET` | 公开 | - | 自动生成接口说明 |
| `answer_editor` | `/api/Question/answer_editor` | `GET` | 需要登录 | `answer_id`, `question_id` | 自动生成接口说明，参数：answer_id, question_id |
| `answers` | `/api/Question/answers` | `GET` | 公开 | `export_answer`, `page`, `per_page`, `question_id`, `sort` | 自动生成接口说明，参数：export_answer, page, per_page, question_id, sort |
| `detail` | `/api/Question/detail` | `GET` | 公开 | `answer_id`, `id` | 返回详情数据 |
| `get_invite_users` | `/api/Question/get_invite_users` | `GET` | 公开 | `question_id` | 自动生成接口说明，参数：question_id |
| `index` | `/api/Question/index` | `GET` | 公开 | `category_id`, `page`, `page_size`, `sort`, `uid`, `words_count` | 返回列表数据 |
| `manager` | `/api/Question/manager` | `GET` | 需要登录 | `id`, `type`, `value` | 问题管理操作，支持推荐、置顶和回滚 |
| `publish` | `/api/Question/publish` | `POST` | 需要登录 | - | 发布或修改内容 |
| `relation` | `/api/Question/relation` | `GET` | 公开 | `page`, `page_size`, `question_id` | 返回相关文章 |
| `remove_answer` | `/api/Question/remove_answer` | `POST` | 需要登录 | `id` | 自动生成接口说明，参数：id |
| `remove_answer_comment` | `/api/Question/remove_answer_comment` | `POST` | 需要登录 | `id` | 自动生成接口说明，参数：id |
| `remove_question` | `/api/Question/remove_question` | `GET` | 需要登录 | `id` | 自动生成接口说明，参数：id |
| `save_answer` | `/api/Question/save_answer` | `POST` | 需要登录 | - | 自动生成接口说明 |
| `save_question_invite` | `/api/Question/save_question_invite` | `POST` | 需要登录 | `question_id`, `uid` | 自动生成接口说明，参数：question_id, uid |
| `search_invite` | `/api/Question/search_invite` | `GET` | 公开 | `name`, `page`, `question_id` | 自动生成接口说明，参数：name, page, question_id |
| `search_question` | `/api/Question/search_question` | `GET` | 公开 | `keyword` | 自动生成接口说明，参数：keyword |
| `set_best_answer` | `/api/Question/set_best_answer` | `GET` | 需要登录 | `id` | 自动生成接口说明，参数：id |

### `Rank`

| 方法 | 路由 | HTTP | 鉴权 | 参数 | 说明 |
| --- | --- | --- | --- | --- | --- |
| `agree` | `/api/Rank/agree` | `GET` | 公开 | `page`, `sort`, `uid` | 自动生成接口说明，参数：page, sort, uid |
| `comment` | `/api/Rank/comment` | `GET` | 公开 | - | 自动生成接口说明 |
| `fav` | `/api/Rank/fav` | `GET` | 公开 | - | 自动生成接口说明 |
| `power` | `/api/Rank/power` | `GET` | 公开 | `uid` | 自动生成接口说明，参数：uid |
| `score` | `/api/Rank/score` | `GET` | 公开 | `sort`, `uid` | 自动生成接口说明，参数：sort, uid |

### `Reward`

| 方法 | 路由 | HTTP | 鉴权 | 参数 | 说明 |
| --- | --- | --- | --- | --- | --- |
| `answers` | `/api/Reward/answers` | `GET` | 公开 | `export_answer`, `page`, `per_page`, `reward_id`, `sort` | 自动生成接口说明，参数：export_answer, page, per_page, reward_id, sort |
| `checkLook` | `/api/Reward/checkLook` | `GET` | 公开 | `id` | 自动生成接口说明，参数：id |
| `detail` | `/api/Reward/detail` | `GET` | 公开 | `id` | 返回详情数据 |
| `focus` | `/api/Reward/focus` | `POST` | 公开 | - | 自动生成接口说明 |
| `initialize` | `/api/Reward/initialize` | `GET` | 公开 | - | 自动生成接口说明 |
| `looker_pay` | `/api/Reward/looker_pay` | `POST` | 公开 | - | 自动生成接口说明 |

### `Topic`

- 需要登录的方法：`save_setting`, `create`

| 方法 | 路由 | HTTP | 鉴权 | 参数 | 说明 |
| --- | --- | --- | --- | --- | --- |
| `create` | `/api/Topic/create` | `POST` | 需要登录 | - | 自动生成接口说明 |
| `detail` | `/api/Topic/detail` | `GET` | 公开 | `id` | 返回详情数据 |
| `get_content_topics` | `/api/Topic/get_content_topics` | `GET` | 公开 | `topic_id` | 自动生成接口说明，参数：topic_id |
| `index` | `/api/Topic/index` | `GET` | 公开 | `page`, `pid`, `type`, `uid` | 返回列表数据 |
| `lately_topics` | `/api/Topic/lately_topics` | `GET` | 公开 | - | 自动生成接口说明 |
| `lists` | `/api/Topic/lists` | `GET` | 公开 | `page`, `page_size`, `pid`, `type` | 自动生成接口说明，参数：page, page_size, pid, type |
| `parent_topic` | `/api/Topic/parent_topic` | `GET` | 公开 | - | 自动生成接口说明 |
| `relations` | `/api/Topic/relations` | `GET` | 公开 | `category_id`, `page`, `page_size`, `sort`, `topic_id`, `type`, `words_count` | 自动生成接口说明，参数：category_id, page, page_size, sort, topic_id, type, words_count |
| `save_setting` | `/api/Topic/save_setting` | `POST` | 需要登录 | - | 自动生成接口说明 |
| `search` | `/api/Topic/search` | `GET` | 公开 | `item_id`, `item_type`, `keywords` | 执行站内搜索 |

### `User`

- 需要登录的方法：`my`, `save_profile`, `get_notify_config`, `logout`, `modify_password`, `integral`, `draft`, `remove_draft`, `verified`, `removeUser`

| 方法 | 路由 | HTTP | 鉴权 | 参数 | 说明 |
| --- | --- | --- | --- | --- | --- |
| `draft` | `/api/User/draft` | `GET` | 需要登录 | `page`, `type` | 自动生成接口说明，参数：page, type |
| `dynamic` | `/api/User/dynamic` | `GET` | 公开 | - | 自动生成接口说明 |
| `fans` | `/api/User/fans` | `GET` | 公开 | - | 自动生成接口说明 |
| `focus_column` | `/api/User/focus_column` | `GET` | 公开 | - | 自动生成接口说明 |
| `focus_topic` | `/api/User/focus_topic` | `GET` | 公开 | - | 自动生成接口说明 |
| `friend` | `/api/User/friend` | `GET` | 公开 | - | 自动生成接口说明 |
| `get_notify_config` | `/api/User/get_notify_config` | `POST` | 需要登录 | - | 自动生成接口说明 |
| `get_verify_type` | `/api/User/get_verify_type` | `GET` | 公开 | - | 自动生成接口说明 |
| `homepage` | `/api/User/homepage` | `GET` | 公开 | `name` | 自动生成接口说明，参数：name |
| `integral` | `/api/User/integral` | `GET` | 需要登录 | `page` | 自动生成接口说明，参数：page |
| `lists` | `/api/User/lists` | `GET` | 公开 | - | 自动生成接口说明 |
| `logout` | `/api/User/logout` | `GET` | 需要登录 | - | 自动生成接口说明 |
| `modify_password` | `/api/User/modify_password` | `POST` | 需要登录 | - | 自动生成接口说明 |
| `my` | `/api/User/my` | `GET` | 需要登录 | - | 自动生成接口说明 |
| `removeUser` | `/api/User/removeUser` | `POST` | 需要登录 | - | 自动生成接口说明 |
| `remove_draft` | `/api/User/remove_draft` | `GET` | 需要登录 | `item_id`, `type` | 自动生成接口说明，参数：item_id, type |
| `save_profile` | `/api/User/save_profile` | `POST` | 需要登录 | - | 自动生成接口说明 |
| `verified` | `/api/User/verified` | `POST` | 需要登录 | `type` | 自动生成接口说明，参数：type |

### `Widget`

| 方法 | 路由 | HTTP | 鉴权 | 参数 | 说明 |
| --- | --- | --- | --- | --- | --- |
| `explore` | `/api/Widget/explore` | `GET` | 公开 | `type` | 自动生成接口说明，参数：type |

## 爬虫与训练数据采集隐私声明

- 本站公开可访问的页面、摘要和接口响应，可能被搜索引擎、学术检索、通用爬虫以及 AI 数据采集工具访问，用于索引、摘要、分析或训练。
- 未经授权的采集不得绕过登录态、权限控制、限频策略或 robots.txt 等访问限制。
- 账号资料、私信、后台数据、未公开草稿、用户隐私字段以及任何受权限保护的内容，不应被采集、复制、再分发或用于训练数据集。
- 采集方应遵守适用法律法规，保留必要的审计与来源标记，并在触发高频访问时主动降频。
- 如需对公开内容进行批量数据采集、模型训练或商业再利用，请先获得站点运营方明确许可。
