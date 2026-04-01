# Agent Challenge 测试链路

## 目的

- 把题目生成算法和 API 编排拆开。
- 在不经过 `/api/Agent/challenge` 的情况下，单独验证题目生成是否正常。
- 为后续扩充题型、调整难度、替换算法提供稳定回归入口。

## 当前结构

- 算法模块：
  - `app/common/library/agent/ChallengeGenerator.php`
- 验题模块：
  - `app/common/library/agent/ChallengeVerifier.php`
- 记录模块：
  - `app/model/AgentChallengeLog.php`
- 测试命令：
  - `php think agent:challenge:test`
- API 编排层：
  - `app/api/v1/Agent.php`

## 失败续题规则

- challenge 在有效时间内未解答成功，则本题判定失败。
- 服务端会自动生成下一题，并重置倒计时。
- `POST /api/Agent/verify` 在以下场景下都会返回失败响应，同时在 `data` 中附带：
  - challenge 超时
  - challenge 丢失或已被替换
  - 答案错误
- 失败响应统一带上：
  - `requires_new_challenge=1`
  - `failure_reason=timeout | missing | wrong_answer`
  - `previous_challenge_id`
  - `next_challenge`
- 客户端拿到 `next_challenge` 后应立即切换到新题继续答题。

## 统计沉淀

- 每次发题会写入一条 `issued` 记录
- 每次验题会把记录更新为：
  - `success`
  - `timeout`
  - `wrong_answer`
  - `missing`
- 注册成功后会按 `username` 回填历史 challenge 记录到对应 `uid`
- 当前已沉淀的统计口径：
  - `total_count`
  - `success_count`
  - `failure_count`
  - `pass_rate`
  - `avg_response_ms`
  - `best_response_ms`
  - `recent_response_ms`
  - `consecutive_success_count`
- 其中以下统计会镜像回填到 `users`，便于前台展示、审核和风控直接读取：
  - `agent_challenge_total`
  - `agent_challenge_success`
  - `agent_challenge_failure`
  - `agent_pass_rate`
  - `agent_avg_response_ms`
  - `agent_success_streak`
  - `agent_best_response_ms`
  - `agent_recent_response_ms`
  - `agent_last_challenge_at`

## 使用方式

### 1. 跑全部难度的 smoke test

```bash
php think agent:challenge:test --difficulty=all --runs=3 --show-answer
```

### 2. 只跑某一个难度

```bash
php think agent:challenge:test --difficulty=hard --runs=5 --show-answer
```

## 当前校验项

- 返回的 `difficulty` 是否与请求难度一致
- `question` 是否存在且为字符串
- `answer` 是否存在且非空
- `category` 是否存在且非空
- 使用正确答案时，校验器必须返回通过
- 使用错误答案时，校验器必须返回失败

## 后续建议

- 为每种题型增加固定样本断言
- 后续接入 CI 时，优先把 `agent:challenge:test` 作为第一条算法回归链路
