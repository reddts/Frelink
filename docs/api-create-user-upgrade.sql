-- Frelink API create-user permission upgrade for existing sites
-- Purpose:
-- 1. Add the permission switch for creating normal users via API
-- 2. Backfill the new permission into existing group permission JSON
-- Note:
-- Replace `aws_` with your real table prefix before executing, for example `kn_`.

INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`)
SELECT 'create_api_user', 'API 新增用户', '允许通过已登录 API 创建普通用户并绑定访问 token', 'radio', 'N', '{"N":"否","Y":"是"}', 0, '', 'system'
WHERE NOT EXISTS (
    SELECT 1 FROM `aws_users_permission` WHERE `name` = 'create_api_user'
);

UPDATE `aws_admin_group`
SET `permission` = JSON_SET(COALESCE(`permission`, '{}'), '$.create_api_user', COALESCE(JSON_EXTRACT(`permission`, '$.create_api_user'), JSON_QUOTE('N')))
WHERE JSON_EXTRACT(COALESCE(`permission`, '{}'), '$.create_api_user') IS NULL;

UPDATE `aws_users_integral_group`
SET `permission` = JSON_SET(COALESCE(`permission`, '{}'), '$.create_api_user', COALESCE(JSON_EXTRACT(`permission`, '$.create_api_user'), JSON_QUOTE('N')))
WHERE JSON_EXTRACT(COALESCE(`permission`, '{}'), '$.create_api_user') IS NULL;

UPDATE `aws_users_reputation_group`
SET `permission` = JSON_SET(COALESCE(`permission`, '{}'), '$.create_api_user', COALESCE(JSON_EXTRACT(`permission`, '$.create_api_user'), JSON_QUOTE('N')))
WHERE JSON_EXTRACT(COALESCE(`permission`, '{}'), '$.create_api_user') IS NULL;
