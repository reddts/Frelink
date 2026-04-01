-- Frelink agent challenge admin menu upgrade for existing sites
-- Purpose:
-- 1. Add backend menu for agent challenge logs
-- 2. Let administrators inspect testing volume, failures and response time
-- Note:
-- Replace `aws_` with your real table prefix before executing.

INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`)
VALUES (4, 'extend.AgentChallenge/index', 'Agent挑战日志', 1, 1, '', 62, 1, 'fa fa-robot', 0, 0, '', 'system');
