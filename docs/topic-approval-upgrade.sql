-- Frelink topic approval upgrade for existing sites
-- 1. Add topic approval permission switch
-- 2. Backfill the new permission to existing group permission JSON

INSERT INTO `kn_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`)
SELECT 'create_topic_approval', '创建话题审核', '创建话题时是否需要审核', 'radio', 'N', '{"N":"否","Y":"是"}', 0, '', 'common'
WHERE NOT EXISTS (
    SELECT 1 FROM `kn_users_permission` WHERE `name` = 'create_topic_approval'
);

UPDATE `kn_admin_group`
SET `permission` = JSON_SET(COALESCE(`permission`, '{}'), '$.create_topic_approval', COALESCE(JSON_EXTRACT(`permission`, '$.create_topic_approval'), JSON_QUOTE('N')))
WHERE JSON_EXTRACT(COALESCE(`permission`, '{}'), '$.create_topic_approval') IS NULL;

UPDATE `kn_users_integral_group`
SET `permission` = JSON_SET(COALESCE(`permission`, '{}'), '$.create_topic_approval', COALESCE(JSON_EXTRACT(`permission`, '$.create_topic_approval'), JSON_QUOTE('N')))
WHERE JSON_EXTRACT(COALESCE(`permission`, '{}'), '$.create_topic_approval') IS NULL;

UPDATE `kn_users_reputation_group`
SET `permission` = JSON_SET(COALESCE(`permission`, '{}'), '$.create_topic_approval', COALESCE(JSON_EXTRACT(`permission`, '$.create_topic_approval'), JSON_QUOTE('N')))
WHERE JSON_EXTRACT(COALESCE(`permission`, '{}'), '$.create_topic_approval') IS NULL;
