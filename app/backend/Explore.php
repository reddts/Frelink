<?php
namespace app\backend;

use app\common\controller\Base;
use app\frontend\Index as FrontendIndex;

class Explore extends Base
{
    public function index()
    {
        return app()->make(FrontendIndex::class)->index();
    }

    public function __call($method, $args)
    {
        return app()->make(FrontendIndex::class)->index();
    }
}
