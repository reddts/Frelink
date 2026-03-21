-- Frelink navigation upgrade for existing sites
-- Purpose:
-- 1. Align menu semantics with an open knowledge retrieval site
-- 2. Promote core knowledge paths into the first-level navigation
-- Note:
-- Replace `aws_` with your real table prefix before executing, for example `kn_`.

UPDATE `aws_menu_rule` SET `title` = '首页', `sort` = 10 WHERE `id` = 1;
UPDATE `aws_menu_rule` SET `title` = '主题', `sort` = 20, `status` = 1 WHERE `id` = 6;
UPDATE `aws_menu_rule` SET `title` = '问题', `sort` = 30, `status` = 1 WHERE `id` = 2;
UPDATE `aws_menu_rule` SET `title` = '文章', `sort` = 40, `status` = 1 WHERE `id` = 3;
UPDATE `aws_menu_rule` SET `title` = '专题', `sort` = 50, `status` = 1 WHERE `id` = 10;
UPDATE `aws_menu_rule` SET `title` = '帮助中心', `sort` = 60 WHERE `id` = 9;
UPDATE `aws_menu_rule` SET `title` = '专栏', `sort` = 70, `status` = 0 WHERE `id` = 4;
UPDATE `aws_menu_rule` SET `title` = '创作者', `sort` = 80, `status` = 0 WHERE `id` = 5;
