<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', dirname(__DIR__) . DS);

require __DIR__ . '/../vendor/autoload.php';

if (!function_exists('bootstrap_env_value')) {
    function bootstrap_env_value(string $section, string $key, string $default = ''): string
    {
        static $config = null;
        if ($config === null) {
            $envFile = ROOT_PATH . '.env';
            $config = is_file($envFile) ? parse_ini_file($envFile, true, INI_SCANNER_RAW) : [];
        }
        return isset($config[$section][$key]) ? trim((string) $config[$section][$key]) : $default;
    }
}

define('G_PRIVATEKEY', bootstrap_env_value('APP', 'PRIVATE_KEY', ''));
define('G_IV', bootstrap_env_value('APP', 'IV', ''));
// 判断是否安装程序
if (!is_file(ROOT_PATH . 'install' . DS . 'lock' . DS . 'install.lock')) {
    header("location:./install.php");exit;
}
// 执行HTTP应用并响应
$http = (new App())->http;
$response = $http->run();
$response->send();
$http->end($response);
