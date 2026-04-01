# Frelink

我把 Frelink 作为一个面向个人站长、垂直社区和团队知识库的开源知识检索与问答系统来持续演进。

我希望它不制造付费门槛、不诱导关注、也不把内容割裂成只能在站内反复跳转的碎片流，而是把问题、文章、主题、专题和帮助文档组织成一个可以持续检索、持续沉淀、持续复用的开放知识站。

## 产品定位

- 公开知识检索站：让访客优先看到完整内容，而不是登录墙和付费提示。
- 垂直主题社区：用主题聚合问题、文章、专题和帮助文档，形成稳定的知识结构。
- 可二次开发的开源程序：提供 Web、移动端和 API 基础能力，方便继续扩展自动化和 AI 能力。

## 适用场景

- 个人公开知识站
- 适合行业垂直问答社区
- 作为企业内部知识库底座
- 适合开源项目文档与 FAQ 中心
- 围绕主题聚合内容的搜索型站点方案

## 安装方式

### 1. 环境要求
- PHP >= 8.2
- MySQL 5.7+
- Nginx/Apache（站点根目录指向 `public/`）
- PHP 扩展：`json`、`iconv`、`zip`、`curl`、`gd`、`zlib`、`openssl`

### 2. 获取代码与依赖
```bash
git clone https://github.com/reddts/Frelink.git
cd Frelink
composer install
```

### 3. 配置数据库
1. 创建数据库（建议 `utf8mb4`）。
2. 导入安装 SQL：`install/sql/install.sql`。
3. 配置数据库连接（按你的部署环境修改配置文件/环境变量）。

### 4. 安装锁与目录权限
- 安装完成后创建安装锁文件：`install/lock/install.lock`（避免重复进入安装流程）。
- 确保以下目录可写：
  - `runtime/`
  - `public/storage/`
  - `public/backup/`（如启用备份功能）

### 5. Web 服务器配置
- 站点入口：`public/index.php`
- 后台入口：`public/admin.php`
- 伪静态可参考：`public/nginx.htaccess`、项目根目录 `nginx.htaccess`

## 核心能力
- 知识问答能力：提问、回答、评论、收藏、关注
- 开放检索：问题、文章、主题、帮助文档统一搜索
- 主题聚合：相关主题、主题合并、无限级主题
- 多种内容组织形态：文章、专题、帮助、页面等
- 社区治理能力：审核记录、敏感词、内容审核流程
- 用户体系：用户组、权限、积分、威望、认证
- 支持多端：网页端、移动端、API
- 保留扩展机制：插件系统、钩子、定时任务

## API 与自动化

### OpenAPI 导出

- 机器可读规范默认输出到 `public/docs/api-v1.openapi.json`
- 浏览器可直接访问 `https://your-domain/docs/api-v1.openapi.json`
- 生成命令：`php think api:doc --format=openapi --output public/docs/api-v1.openapi.json`
- 当前 API 响应统一包含 `request_id`；失败响应还会附带 `error_code`，方便日志串联和程序分支判断
- 当前接口语义保持 `code=1` 表示成功、`code=0` 表示失败，与 `msg` / `data` / `request_id` 一起构成响应包
- API 登录态仍可使用 `UserToken`；如果你在后台创建了 `app_token` 的 API 认证项，也可以改用 `ApiToken` 或 `AccessToken`，并绑定到指定用户后直接作为该用户访问接口

### 推荐的 agent 使用边界

- 允许：
  - 搜索词采集与选题建议
  - 内容健康巡检
  - 发布后链接检查
  - 收录状态巡检
  - 低风险草稿生成
  - 先调用 `agent_brief` 获取整合后的运营与写作上下文
- 不建议直接开放：
  - 删除内容
  - 修改权限
  - 自动正式发布
  - 无审批的生产运维操作

### 接入前建议

1. 为 agent 创建独立前台账号，并通过 `is_agent` 字段识别，不复用人工账号。
2. 先补齐接口 smoke test，再接入自动化发布流程。
3. 为发布、删除、推荐、置顶等动作记录审计日志。
4. 对登录、短信、发布、评论等接口增加限频与监控。

### smoke test 建议

```bash
curl -H "version: v1" https://your-domain/api/Common/config
curl -H "version: v1" "https://your-domain/api/Common/search?q=frelink"
curl -H "version: v1" "https://your-domain/api/Question/index?page=1&page_size=5"
curl -H "version: v1" "https://your-domain/api/Article/index?page=1&page_size=5"
curl -H "version: v1" -H "UserToken: <token>" "https://your-domain/api/Insight/agent_brief?days=7&limit=3"
curl -H "version: v1" -H "UserToken: <token>" "https://your-domain/api/Insight/agent_brief?days=7&limit=3&format=markdown"
curl -H "version: v1" -H "UserToken: <token>" "https://your-domain/api/Insight/weekly_execution?days=7&limit=3"
curl -H "version: v1" -H "UserToken: <token>" "https://your-domain/api/Insight/weekly_execution?days=7&limit=3&format=markdown"
curl -H "version: v1" -H "UserToken: <token>" "https://your-domain/api/Insight/writing_workflow?mode=all&days=7&limit=3"
curl -X POST -H "version: v1" -H "Content-Type: application/json" \
  -d '{"event_type":"detail_view","item_type":"article","item_id":1,"visitor_token":"debug-token","source":"smoke_test"}' \
  https://your-domain/api/Insight/track
```

### 后续改造方向

- 增加 OpenAPI 文档
- 统一错误码规范
- 为 agent 提供只读巡检 token
- 为发布流程增加审批与回滚机制
- 为 Insight 增加后台面板与定时报表
