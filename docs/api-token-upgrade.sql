-- Frelink API token upgrade for existing sites
-- Purpose:
-- 1. Allow backend-managed API auth tokens
-- 2. Bind tokens to a user account for direct API login
-- 3. Track token status and usage metadata
-- 4. Restore the backend menu entry for existing installations
-- Note:
-- Replace `aws_` with your real table prefix before executing, for example `kn_`.

ALTER TABLE `aws_app_token`
  ADD COLUMN `uid` int(11) NOT NULL DEFAULT 0 COMMENT '绑定用户UID' AFTER `type`,
  ADD COLUMN `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态 1启用 0禁用' AFTER `uid`,
  ADD COLUMN `expire_time` int(10) NOT NULL DEFAULT 0 COMMENT '过期时间' AFTER `status`,
  ADD COLUMN `last_use_time` int(10) NOT NULL DEFAULT 0 COMMENT '最后使用时间' AFTER `expire_time`,
  ADD COLUMN `last_use_ip` varchar(64) NOT NULL DEFAULT '' COMMENT '最后使用IP' AFTER `last_use_time`,
  ADD COLUMN `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注' AFTER `last_use_ip`;

INSERT IGNORE INTO `aws_admin_auth`
(`id`, `pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`)
VALUES
(353, 4, 'extend.Token/index', '接口请求', 1, 1, '', 61, 1, 'fa fa-anchor', 0, 0, '', 'system'),
(354, 353, 'extend.Token/delete', '操作-删除', 0, 0, '', 5, 1, '', 0, 0, '', 'system'),
(355, 353, 'extend.Token/add', '操作-添加', 1, 0, '', 7, 1, '', 0, 0, '', 'system'),
(356, 353, 'extend.Token/edit', '操作-编辑', 1, 0, '', 61, 1, 'fa fa-link', 0, 0, '', 'system');
