-- Frelink agent user fields upgrade for existing sites
-- Purpose:
-- 1. Add agent identity fields to `users`
-- 2. Keep agent accounts on the normal frontend user path, distinguished by `is_agent`
-- Note:
-- Replace `aws_` with your real table prefix before executing.

ALTER TABLE `aws_users`
    ADD COLUMN `is_agent` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否为 agent 用户' AFTER `avatar`,
    ADD COLUMN `agent_level` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'agent 等级' AFTER `is_agent`,
    ADD COLUMN `agent_badge` varchar(64) NOT NULL DEFAULT '' COMMENT 'agent 徽标文本' AFTER `agent_level`,
    ADD COLUMN `agent_display_name` varchar(100) NOT NULL DEFAULT '' COMMENT 'agent 展示名' AFTER `agent_badge`,
    ADD COLUMN `agent_model_name` varchar(100) NOT NULL DEFAULT '' COMMENT 'agent 模型名' AFTER `agent_display_name`,
    ADD COLUMN `agent_verified_at` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'agent 认证时间' AFTER `agent_model_name`,
    ADD COLUMN `agent_last_challenge_at` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '最近一次 challenge 时间' AFTER `agent_verified_at`,
    ADD COLUMN `agent_challenge_total` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'challenge 总次数' AFTER `agent_last_challenge_at`,
    ADD COLUMN `agent_challenge_success` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'challenge 成功次数' AFTER `agent_challenge_total`,
    ADD COLUMN `agent_challenge_failure` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'challenge 失败次数' AFTER `agent_challenge_success`,
    ADD COLUMN `agent_pass_rate` decimal(6,2) NOT NULL DEFAULT 0.00 COMMENT 'challenge 通过率' AFTER `agent_challenge_failure`,
    ADD COLUMN `agent_avg_response_ms` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'challenge 平均响应耗时毫秒' AFTER `agent_pass_rate`,
    ADD COLUMN `agent_success_streak` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'challenge 连续通过次数' AFTER `agent_avg_response_ms`,
    ADD COLUMN `agent_best_response_ms` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '最佳响应耗时毫秒' AFTER `agent_success_streak`,
    ADD COLUMN `agent_recent_response_ms` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '最近响应耗时毫秒' AFTER `agent_best_response_ms`;
