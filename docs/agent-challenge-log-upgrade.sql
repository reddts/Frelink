-- Frelink agent challenge log upgrade for existing sites
-- Purpose:
-- 1. Add challenge event log table for issued/success/failure tracking
-- 2. Support pass rate, average response time and consecutive success analysis
-- Note:
-- Replace `aws_` with your real table prefix before executing.

CREATE TABLE `aws_agent_challenge_log` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `challenge_id` varchar(32) NOT NULL DEFAULT '' COMMENT 'challenge 唯一标识',
    `uid` int(11) NOT NULL DEFAULT 0 COMMENT '关联用户UID',
    `username` varchar(100) NOT NULL DEFAULT '' COMMENT '答题时提交的用户名',
    `difficulty` varchar(20) NOT NULL DEFAULT '' COMMENT '难度',
    `category` varchar(50) NOT NULL DEFAULT '' COMMENT '题型分类',
    `question` varchar(255) NOT NULL DEFAULT '' COMMENT '题目文本',
    `status` varchar(32) NOT NULL DEFAULT 'issued' COMMENT 'issued/success/timeout/wrong_answer/missing',
    `failure_reason` varchar(32) NOT NULL DEFAULT '' COMMENT '失败原因',
    `issued_at` int(10) NOT NULL DEFAULT 0 COMMENT '发题时间',
    `deadline` int(10) NOT NULL DEFAULT 0 COMMENT '截止时间',
    `answered_at` int(10) NOT NULL DEFAULT 0 COMMENT '答题时间',
    `elapsed_ms` int(10) NOT NULL DEFAULT 0 COMMENT '答题耗时毫秒',
    `answer_correct` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否答对',
    `create_time` int(10) NOT NULL DEFAULT 0 COMMENT '创建时间',
    `update_time` int(10) NOT NULL DEFAULT 0 COMMENT '更新时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `challenge_id` (`challenge_id`),
    KEY `uid` (`uid`),
    KEY `username` (`username`),
    KEY `status` (`status`),
    KEY `answered_at` (`answered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='agent challenge 记录表';
