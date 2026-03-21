<?php
namespace think;

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', dirname(__DIR__) . DS);
define('ENTRANCE', 'api');

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

if (!is_file(ROOT_PATH . 'install' . DS . 'lock' . DS . 'install.lock')) {
    header('location:/install.php');
    exit;
}

$http = (new App())->http;
$response = $http->run();
$response->send();
$http->end($response);
