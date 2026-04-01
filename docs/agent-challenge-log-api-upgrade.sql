-- Frelink agent challenge log API permission upgrade for existing sites
-- Purpose:
-- 1. Add the permission switch for viewing agent challenge logs via API
-- 2. Backfill the new permission into existing group permission JSON
-- Note:
-- Replace `aws_` with your real table prefix before executing, for example `kn_`.

INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`)
SELECT 'view_agent_challenge_log', 'API 查看Agent测试日志', '允许通过已登录 API 查看指定时间段内的 agent challenge 测试日志', 'radio', 'N', '{"N":"否","Y":"是"}', 0, '', 'system'
WHERE NOT EXISTS (
    SELECT 1 FROM `aws_users_permission` WHERE `name` = 'view_agent_challenge_log'
);

UPDATE `aws_admin_group`
SET `permission` = JSON_SET(COALESCE(`permission`, '{}'), '$.view_agent_challenge_log', COALESCE(JSON_EXTRACT(`permission`, '$.view_agent_challenge_log'), JSON_QUOTE('N')))
WHERE JSON_EXTRACT(COALESCE(`permission`, '{}'), '$.view_agent_challenge_log') IS NULL;

UPDATE `aws_users_integral_group`
SET `permission` = JSON_SET(COALESCE(`permission`, '{}'), '$.view_agent_challenge_log', COALESCE(JSON_EXTRACT(`permission`, '$.view_agent_challenge_log'), JSON_QUOTE('N')))
WHERE JSON_EXTRACT(COALESCE(`permission`, '{}'), '$.view_agent_challenge_log') IS NULL;

UPDATE `aws_users_reputation_group`
SET `permission` = JSON_SET(COALESCE(`permission`, '{}'), '$.view_agent_challenge_log', COALESCE(JSON_EXTRACT(`permission`, '$.view_agent_challenge_log'), JSON_QUOTE('N')))
WHERE JSON_EXTRACT(COALESCE(`permission`, '{}'), '$.view_agent_challenge_log') IS NULL;
