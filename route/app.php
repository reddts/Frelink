<?php
use think\facade\Route;

if(get_setting('url_rewrite_enable')=='Y' && ENTRANCE!='api')
{
    $prefix = app()->db->getConfig('connections.mysql.prefix');
    $routeTableExists = cache('route_rule_table_exists');
    if ($routeTableExists === null) {
        $routeTableExists = (bool) db()->query("SELECT COUNT(*) FROM information_schema.TABLES WHERE table_name ='{$prefix}route_rule'")[0]['COUNT(*)'];
        cache('route_rule_table_exists', $routeTableExists ? 1 : 0, 300);
    }
    if($routeTableExists)
    {
        $cacheKey = 'route_rules_non_api';
        $routes = cache($cacheKey);
        if ($routes === null) {
            $routes = db('route_rule')->where(['status'=>1])->where('entrance','<>','api')->select()->toArray();
            cache($cacheKey, $routes, 300);
        }
        foreach ($routes as $k=>$v)
        {
            if (ENTRANCE === 'backend' && $v['entrance'] === 'all' && $v['rule'] === 'explore/[:sort]' && $v['url'] === 'index/index') {
                continue;
            }
            //通用路由
            if($v['entrance']=='all'){
                if($v['method']!='*')
                {
                    $method = $v['method'];
                    Route::$method($v['rule'], $v['url']);
                }else{
                    Route::rule($v['rule'], $v['url']);
                }
            }

            //当前访问路由
            if($v['entrance']==ENTRANCE)
            {
                if($v['method']!='*')
                {
                    $method = $v['method'];
                    Route::$method($v['rule'], $v['url']);
                }else{
                    Route::rule($v['rule'], $v['url']);
                }
            }
        }
    }else{
        $urlRewrite = get_setting('url_rewrite');
        $urlRewrite = explode("\n", $urlRewrite);
        $routes = [];
        if($urlRewrite)
        {
            foreach ($urlRewrite as $key => $val)
            {
                $val = trim($val);
                list($replace, $pattern) = explode('===', $val);
                $routes[] = array($pattern, $replace);
            }

            foreach ($routes as $k=>$v)
            {
                Route::rule($v[1], $v[0]);
            }
        }
    }
}

//第三方登录插件重写地址
Route::rule('third/callback/[:platform]-[:token]', 'ThirdAuth/callback');
Route::rule('api-docs', 'page/api');
Route::rule('help/api', 'page/api');

//接口处理
if(ENTRANCE=='api')
{
    $version = request()->header('version');
    if($version==null)$version = "v1";
    Route::rule(':controller/:function', $version.'.:controller/:function');
    if(get_setting('url_rewrite_enable')=='Y')
    {
        $cacheKey = 'route_rules_api';
        $routes = cache($cacheKey);
        if ($routes === null) {
            $routes = db('route_rule')->where(['status'=>1,'entrance'=>'api'])->select()->toArray();
            cache($cacheKey, $routes, 300);
        }
        foreach ($routes as $k=>$v)
        {
            if($v['method']!='*')
            {
                $method = $v['method'];
                Route::$method($v['rule'], $v['url']);
            }else{
                Route::rule($v['rule'], $v['url']);
            }
        }
    }
}
