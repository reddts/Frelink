<?php
// +----------------------------------------------------------------------
// | WeCenter 简称 WC
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://wecenter.isimpo.com
// +----------------------------------------------------------------------
// | WeCenter团队一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: WeCenter团队 <devteam@wecenter.com>
// +----------------------------------------------------------------------

namespace app\common\library\helper;
use app\common\library\helper\TreeHelper as TreeService;

class UserAuthHelper {
	/**
	 * @var object 对象实例
	 */
	protected static $instance;

	/**
	 * 当前请求实例
	 * @var $request
	 */
	protected $request;
	protected $rules = [];

	protected $error;

	/**
	 * 类架构函数
	 * AuthService constructor.
	 */
	public function __construct() {
		// 初始化request
		$this->request = request();
	}

    /**
     * 初始化
     * @return UserAuthHelper
     */
	public static function instance(): UserAuthHelper
    {
		if (is_null(self::$instance)) {
			self::$instance = new static();
		}
		return self::$instance;
	}

    /**
     * 检测当前控制器和方法是否匹配传递的数组.
     * @param mixed $authArr 需要验证权限的数组
     * @return bool
     */
    public function match($authArr = null): bool
    {
        $authArr = is_array($authArr) ? $authArr : explode(',', $authArr=$authArr?$authArr:'');
        if (!$authArr) {
            return false;
        }
        $arr = array_map('strtolower', $authArr);
        // 是否存在
        return in_array(strtolower(request()->action()), $authArr) || in_array('*', $arr);
    }

	public function getNav($checkPath='',$menu_type='nav')
    {
        if(!$checkPath)
        {
            $checkPath = strtolower(request()->controller()).'/'.strtolower(request()->action());
        }

        $isLogin = $this->getUserId() ? 1 : 0;
        $cacheKey = 'user_auth_nav:' . md5($menu_type . '|' . $checkPath . '|' . $isLogin);
        $cachedNav = cache($cacheKey);
        if ($cachedNav !== null) {
            return $cachedNav;
        }

        $_menu =db('menu_rule')
            ->where(['status'=>1,'group'=>$menu_type])
            ->order(['sort'=>'asc','id'=>'asc'])
            ->select()
            ->toArray();
        if(!$_menu)
        {
            return false;
        }
        $menu = [];

        foreach ($_menu as $key => $val)
        {
            if($val['auth_open'] && !$this->getUserId())
            {
                unset($_menu[$key]);
                continue;
            }
            $menu[$val['id']] = $val;
        }

        $tree_menu = TreeService::instance()->init($menu);
        foreach ($menu as $key => $val)
        {
            if (strtolower($val['name']) == $checkPath)
            {
                $menu[$key]['active'] = 1;
                if($tree_menu->getParentsIds($val['id']))
                {
                    foreach ($tree_menu as $k=>$v)
                    {
                        $menu[$v]['active'] = 1;
                    }
                }
            }

            if($val['param']!='')
            {
                $val['param'] = htmlspecialchars_decode($val['param']);
                parse_str($val['param'],$output_array);
                $menu[$key]['url'] = (string)url($val['name'],$output_array);
            }else{
                $menu[$key]['url'] = (string)url($val['name']);
            }

            if($val['type']==2)
            {
                $menu[$key]['url'] = $val['name'];
            }
            $menu[$key]['url'] = htmlspecialchars_decode($menu[$key]['url']);
        }
        $result = TreeHelper::instance()->init($menu)->getTreeArray(0);
        if ($menu_type === 'nav') {
            $result = $this->normalizePublicNav($result);
        }
        cache($cacheKey, $result, 300);
        return $result;
    }

    protected function normalizePublicNav(array $items): array
    {
        if (!$items) {
            return [];
        }

        $hiddenTitles = ['专栏', '创作者'];
        $priorityMap = [
            '首页' => 10,
            '主题' => 20,
            '问题' => 30,
            '文章' => 40,
            '专题' => 50,
            '帮助中心' => 60,
            '帮助' => 60,
        ];

        $normalized = [];
        foreach ($items as $item) {
            $title = trim((string)($item['title'] ?? ''));
            if (in_array($title, $hiddenTitles, true)) {
                continue;
            }

            if (!empty($item['child_list']) && is_array($item['child_list'])) {
                $item['child_list'] = $this->normalizePublicNav($item['child_list']);
            }

            $item['_frelink_priority'] = $priorityMap[$title] ?? (1000 + intval($item['sort'] ?? 0));
            $normalized[] = $item;
        }

        usort($normalized, function ($a, $b) {
            $priorityCompare = intval($a['_frelink_priority'] ?? 9999) <=> intval($b['_frelink_priority'] ?? 9999);
            if ($priorityCompare !== 0) {
                return $priorityCompare;
            }

            $sortCompare = intval($a['sort'] ?? 0) <=> intval($b['sort'] ?? 0);
            if ($sortCompare !== 0) {
                return $sortCompare;
            }

            return intval($a['id'] ?? 0) <=> intval($b['id'] ?? 0);
        });

        foreach ($normalized as $key => $item) {
            unset($normalized[$key]['_frelink_priority']);
        }

        return array_values($normalized);
    }

    public function getUserId()
    {
        $uid = session('login_uid');
        if (!$uid) {
            $uid = 0;
        }
        return $uid;
    }

	/**
	 * 设置错误信息
	 * @param $error
	 */
	public function setError($error): void
    {
		$this->error = $error;
	}

	/**
	 * 获取错误信息
	 * @return mixed
	 */
	public function getError()
    {
		return $this->error;
	}
}
