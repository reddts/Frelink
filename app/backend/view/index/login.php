<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>管理后台登录 - {:get_setting('site_name')}</title>
    <meta name="description" content="">
    <link rel="stylesheet" href="/static/admin/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="/static/admin/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="/static/admin/css/admin.css">
    <link rel="stylesheet" type="text/css" href="/static/libs/captcha/css/captcha.css" />
    <script src="/static/libs/layui/layui.js"></script>
    <script src="/static/admin/plugins/jquery/jquery.min.js"></script>
    <script>
        layui.use('layer', function () {
            var layer = layui.layer;
        });

        window.baseUrl = '{$baseUrl}';
    </script>
    <script src="/static/common/js/tools.js"></script>
    <script src="/static/common/js/aws.js"></script>
    <script type="text/javascript" src="/static/libs/captcha/captcha.js"></script>
</head>
<body class="admin-login-page">
<div class="admin-login-shell">
    <div class="admin-login-aside">
        <div class="admin-login-brand">
            <div class="admin-login-badge">Admin</div>
            <h1>{$setting.site_name|default='FreCenter'}</h1>
            <p>统一后台管理入口，按新规范整理视觉、表单和交互体验。</p>
        </div>
        <div class="admin-login-meta">
            <div class="admin-login-meta-item">
                <span>站点</span>
                <strong>{$setting.site_name|default='FreCenter'}</strong>
            </div>
            <div class="admin-login-meta-item">
                <span>入口</span>
                <strong>/admin.php</strong>
            </div>
            <div class="admin-login-meta-item">
                <span>版本</span>
                <strong>v{:config('version.version')}</strong>
            </div>
        </div>
    </div>
    <div class="admin-login-panel">
        <div class="admin-login-card">
            <div class="admin-login-card-header">
                <h2>管理后台登录</h2>
                <p>请输入管理员账号和密码继续。</p>
            </div>
            <form class="js-validation-signin" action="" method="POST">
                <input type="hidden" name="captcha" id="captcha">
                {:token_field()}
                <div class="admin-login-form">
                    <div class="form-group">
                        <label for="username">用户名</label>
                        <div class="admin-input-wrap">
                            <i class="fas fa-user"></i>
                            <input id="username" type="text" class="form-control" {if isset($user_info)} value="{$user_info['user_name']}" readonly {/if} name="username" placeholder="请输入用户名" autocomplete="username">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">密码</label>
                        <div class="admin-input-wrap">
                            <i class="fas fa-lock"></i>
                            <input type="password" class="form-control" id="password" name="password" placeholder="请输入密码" autocomplete="current-password">
                        </div>
                    </div>
                </div>
                <div class="admin-login-actions">
                    <button type="button" class="btn btn-primary btn-lg btn-block login-button">登录</button>
                    <a class="btn btn-outline-secondary btn-lg btn-block mt-3" href="/admin-vben/#/login">进入新版管理端</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $(".login-button").click(function () {
            {if $setting.enable_backend_captcha=='Y'}
            $('#captcha').captcha({
                callback: function () {
                    loginAction();
                }
            });
            {else/}
            loginAction();
            {/if}
        });
    });

    function loginAction()
    {
        var token = $("input[name='__token__']").val();
        var name = $("#username").val();
        var rawPassword = $("#password").val();
        var password = authCode(rawPassword, 'ENCODE', token);
        if ((name == null) || (name === ""))
        {
            AWS.api.error("用户名不能为空！");
            return false;
        }
        else if ((rawPassword == null) || (rawPassword === "")) {
            AWS.api.error("密码不能为空！");
            return false;
        } else {
            $.ajax({
                url: "{:url('Index/login')}",
                type: "POST",
                data: {username: name, password: password, token: token},
                dataType: "json",
                success: function (result) {
                    let msg = result.msg ? result.msg : '操作成功';
                    if (result.code > 0) {
                        AWS.api.success(msg, result.url);
                    } else {
                        AWS.api.error(msg, result.url);
                    }
                },
                error: function (error) {
                    if ($.trim(error.responseText) !== '') {
                        AWS.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                    }
                }
            });
        }
    }
</script>
</body>
</html>
