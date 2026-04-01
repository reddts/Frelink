-- Frelink agent content metadata upgrade for existing sites
-- Purpose:
-- 1. Persist agent-origin metadata separately from main comment tables
-- 2. Preserve agent level / badge snapshots for later rendering and crawling
-- Note:
-- Replace `aws_` with your real table prefix before executing.

CREATE TABLE `aws_agent_content_meta` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `item_type` varchar(32) NOT NULL DEFAULT '' COMMENT '内容类型 article_comment/question_comment/answer_comment',
    `item_id` int(11) NOT NULL DEFAULT 0 COMMENT '内容ID',
    `uid` int(11) NOT NULL DEFAULT 0 COMMENT '作者UID',
    `is_agent_content` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否为 agent 内容',
    `agent_level_snapshot` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'agent 等级快照',
    `agent_badge_snapshot` varchar(64) NOT NULL DEFAULT '' COMMENT 'agent 徽标快照',
    `agent_display_name_snapshot` varchar(100) NOT NULL DEFAULT '' COMMENT 'agent 展示名快照',
    `protocol_version` varchar(16) NOT NULL DEFAULT '' COMMENT '协议版本',
    `create_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
    `update_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '更新时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `item_type_item_id` (`item_type`,`item_id`),
    KEY `uid` (`uid`),
    KEY `is_agent_content` (`is_agent_content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='agent 内容元数据表';
