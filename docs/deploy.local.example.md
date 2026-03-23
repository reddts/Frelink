# 本地部署说明模板

这个文件用于记录你机器上的真实部署流程，但不要提交敏感信息。

建议复制为 `docs/deploy.local.md`，该文件已加入 `.gitignore`。

## 当前服务器

- SSH：
  - `ssh -i /home/redt/.sshbot/azcomm.pem azureuser@20.191.157.253`
- 项目路径：
  - `/www/wwwroot/knoledge`

## 私钥权限说明

- 如果你的私钥原始文件在 `/mnt/c/...` 这类 Windows 挂载目录下，OpenSSH 可能会因为权限过宽拒绝加载
- 建议复制到 Linux 本地路径，例如：
  - `mkdir -p /home/redt/.sshbot`
  - `cp /mnt/c/Users/redt/.sshbot/azcomm.pem /home/redt/.sshbot/azcomm.pem`
  - `chmod 600 /home/redt/.sshbot/azcomm.pem`
- 然后把 `deploy.local.json` 里的 `identity_file` 改到 Linux 路径

## 标准发布命令

- 仅同步代码：
  - `bash scripts/deploy.sh sync`
- 仅远程验证：
  - `bash scripts/deploy.sh verify`
- 同步并验证：
  - `bash scripts/deploy.sh deploy`
- 查看当前配置：
  - `bash scripts/deploy.sh show-config`

## 当前默认验证项

- 远程 PHP 语法检查：
  - `app/function.inc.php`
  - `app/frontend/Article.php`
- 缓存刷新：
  - `sudo php think clear`
- smoke test：
  - `/`
  - `/questions/`
  - `/articles/`

## 维护规则

- 真实服务器信息只写在 `deploy.local.json` 或 `docs/deploy.local.md`
- 变更服务器时，优先更新 `deploy.local.json`
- 每轮改动后统一走：
  - `bash scripts/deploy.sh deploy`
