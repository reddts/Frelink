# API 安全与逻辑优化（`app/api/v1`）

## 本轮审计结论

### 高风险问题（已修复）
- `User::homepage` 使用 `whereRaw` 拼接用户输入，存在 SQL 注入风险。
  - 修复：改为参数化 `where/whereOr` 查询，并限制参数长度。
- `Question::search_question` 通过 `whereRaw + regexp` 拼接关键词，存在注入风险。
  - 修复：改为参数化查询 `where('title', 'regexp', $keyword)`，并限制关键词长度。
- `Common::sms` 将短信验证码直接返回给客户端，存在验证码泄露风险。
  - 修复：不再返回验证码，仅返回发送结果；增加 60 秒发送频率限制（按手机号+IP）。
- `Invitation` 存在明显代码错误与拼接查询风险。
  - 修复：
  - 修正语法错误（多余 `$`）。
  - `whereRaw` 改为条件构造器。
  - 邀请用户列表 `IN (...)` 改为安全数组条件。
- 微信小程序绑定流程存在逻辑缺陷。
  - 修复：
  - `Account::wxminiapp_bind` 错误读取 `trim` 字段改为 `email`。
  - 增加 `bindWxPlatformAccount()`，防止同一 `openid` 绑定到不同账号。
  - `wxminiapp_login` 增加 `code` 空值校验。
- 跨域策略过宽（默认 `*` + 凭证），存在被滥用风险。
  - 修复：
  - 收敛 `Api.php` 顶层 CORS 响应头。
  - `Jump::checkCrossRequest()` 改为按 `app.cors_request_domain` + 当前站点域名校验，不再默认全放行。

### 认证与越权（本轮已增强）
- 新增/收紧 `needLogin`（敏感接口）：
  - `Account`: `my`
  - `Article`: `publish`, `manager`, `remove_article`
  - `Question`: `publish`, `remove_answer`, `remove_question`, `manager`, `save_question_invite`, `answer_editor`, `save_answer`, `set_best_answer`, `remove_answer_comment`
  - `Common`: `set_vote`, `update_focus`, `get_access_key`, `upload`, `remove_attach`, `save_draft`
  - `Comment`: `save_comment`, `remove_comment`
  - `Column`: `my`, `apply`, `collect`
  - `Favorite`: `*`
  - `Inbox`: `*`
  - `Invitation`: `*`
  - `Notify`: `*`
  - `Pay`: `createOrder`, `qrcode`, `balance`
  - `Topic`: `save_setting`, `create`
  - `Permission`: `report`

## 下一步计划

### P1（建议优先）
- 梳理所有 `app/api/v1` 接口的“公开/登录必需”矩阵，补齐 `needLogin`。
- 增加关键接口防刷：
  - 登录、注册、重置密码、短信发送、评论发布、内容发布。
- 统一接口参数长度与类型白名单（分页、排序、关键词）。

### P2（稳定性与可观测性）
- 统一 API 错误码与错误文案规范。
- 增加安全审计日志（登录失败、短信发送频繁、越权操作）。
- 补充接口级回归用例（含登录态/未登录态差异）。

## 变更文件（本轮）
- `app/common/controller/Api.php`
- `app/common/traits/Jump.php`
- `app/api/v1/User.php`
- `app/api/v1/Question.php`
- `app/api/v1/Common.php`
- `app/api/v1/Invitation.php`
- `app/api/v1/Account.php`
- `app/api/v1/Article.php`
- `app/api/v1/Comment.php`
- `app/api/v1/Column.php`
- `app/api/v1/Favorite.php`
- `app/api/v1/Inbox.php`
- `app/api/v1/Notify.php`
- `app/api/v1/Pay.php`
- `app/api/v1/Topic.php`
- `app/api/v1/Permission.php`
