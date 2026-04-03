-- Frelink insight permission upgrade for existing sites
-- Purpose:
-- 1. Backfill the `recommend_post` permission definition for older sites
-- 2. Rename it to "推荐内容 / 运营洞察" so old admin pages can find it
-- 3. Backfill the permission switch into existing system-group permission JSON
-- Note:
-- Replace `aws_` with your real table prefix before executing, for example `kn_`.

INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`)
SELECT 'recommend_post', '推荐内容 / 运营洞察', '允许推荐内容，并查看运营洞察、写作建议和选题辅助能力', 'radio', 'Y', '{"N":"否","Y":"是"}', 0, NULL, 'system'
WHERE NOT EXISTS (
    SELECT 1 FROM `aws_users_permission` WHERE `name` = 'recommend_post'
);

UPDATE `aws_users_permission`
SET
    `title` = '推荐内容 / 运营洞察',
    `tips` = '允许推荐内容，并查看运营洞察、写作建议和选题辅助能力',
    `type` = 'radio',
    `value` = 'Y',
    `option` = '{"N":"否","Y":"是"}',
    `group` = 'system'
WHERE `name` = 'recommend_post';

UPDATE `aws_admin_group`
SET `permission` = JSON_SET(
    COALESCE(`permission`, '{}'),
    '$.recommend_post',
    COALESCE(JSON_EXTRACT(`permission`, '$.recommend_post'), JSON_QUOTE('Y'))
)
WHERE JSON_EXTRACT(COALESCE(`permission`, '{}'), '$.recommend_post') IS NULL;
