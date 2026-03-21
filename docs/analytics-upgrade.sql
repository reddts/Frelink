-- Frelink analytics upgrade for AI-assisted operations

CREATE TABLE IF NOT EXISTS `aws_analytics_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录用户ID',
  `visitor_token` varchar(64) NOT NULL DEFAULT '' COMMENT '匿名访客标识',
  `item_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  `item_type` varchar(32) NOT NULL DEFAULT '' COMMENT 'question/article/topic/column',
  `event_type` varchar(32) NOT NULL DEFAULT '' COMMENT 'impression/click/detail_view',
  `source` varchar(64) NOT NULL DEFAULT '' COMMENT '来源页面',
  `list_key` varchar(100) NOT NULL DEFAULT '' COMMENT '列表标识',
  `position` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '列表位置',
  `referrer` varchar(255) DEFAULT NULL COMMENT '来源页',
  `ip` varchar(64) DEFAULT NULL COMMENT '访问IP',
  `user_agent` varchar(500) DEFAULT NULL COMMENT '浏览器标识',
  `extra` text COMMENT '附加信息',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录时间',
  PRIMARY KEY (`id`),
  KEY `event_time` (`event_type`,`create_time`),
  KEY `item_event_time` (`item_type`,`item_id`,`event_type`,`create_time`),
  KEY `list_event_time` (`list_key`,`event_type`,`create_time`),
  KEY `visitor_event_time` (`visitor_token`,`event_type`,`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='运营分析事件表';
