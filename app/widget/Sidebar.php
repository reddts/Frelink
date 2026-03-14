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

namespace app\widget;
use app\common\controller\Widget;
use app\model\Users;
use app\model\Topic;

/**
 * 侧边栏小部件
 * Class Sidebar
 */
class Sidebar extends Widget
{
    /**
     * 用户信息
     * @param $user_info
     * @param $uid
     * @return mixed
     */
	public function profile($user_info,$uid)
	{
		$this->assign('user_info',$user_info);
		$this->assign('uid',$uid);
		return $this->fetch('sidebar/profile');
	}

    /**
     * 当前登录用户信息
     * @return mixed
     */
	public function loginUser()
	{
		return $this->fetch('sidebar/login_user');
	}

    /**
     * 我感兴趣的话题
     * @param $uid
     * @return mixed
     */
    public function focusTopic($uid)
    {
        $cache_key = 'widget_sidebar_focus_topic_' . intval($uid);
        $topic_list = cache($cache_key);
        if ($topic_list === null) {
            $topic_list = Topic::getFocusTopicByRand($uid);
            cache($cache_key, $topic_list, 120);
        }
        $this->assign('topic_list',$topic_list);
        return $this->fetch('sidebar/focus_topic');
    }

    /**
     * 热门话题
     * @param $uid
     * @param array $where
     * @param array $order
     * @param int $limit
     * @return mixed
     */
	public function hotTopic($uid,array $where=[],array $order=[],int $limit=5)
	{
        $cache_key = 'widget_sidebar_hot_topic_' . intval($uid) . '_' . md5(json_encode([$where,$order,$limit]));
        $topic_list = cache($cache_key);
        if ($topic_list === null) {
            $topic_list = Topic::getHotTopics($uid,$where,$order,$limit);
            cache($cache_key, $topic_list, 120);
        }
		$this->assign('topic_list',$topic_list['data']);
		return $this->fetch('sidebar/hot_topic');
	}

    /**
     * 热门用户
     * @param int $uid
     * @param array $where
     * @param array $order
     * @param int $limit
     * @return mixed
     */
	public function hotUsers(int $uid=0, $where=[], $order=[], $limit=5)
    {
        $cache_key = 'widget_sidebar_hot_users_' . intval($uid) . '_' . md5(json_encode([$where,$order,$limit]));
        $people_list = cache($cache_key);
        if ($people_list === null) {
            $people_list = Users::getHotUsers($uid,$where,$order,$limit);
            cache($cache_key, $people_list, 120);
        }
        $this->assign('people_list',$people_list);
        return $this->fetch('sidebar/hot_users');
    }

    /**
     * 快捷菜单
     * @return mixed
     */
    public function writeNav()
    {
        return $this->fetch('sidebar/write_nav');
    }

	//侧边分类列表
    public function category($sort='',$category='')
    {
        $where =  ['status'=>1,'type'=>'all','pid'=>0];
        $list = db('category')->where($where)->order('sort','DESC')->column('id,title,icon');
        if($list)
        {
            /*foreach ($list as $key=>$val)
            {
                $list[$key]['post_count'] = db('post_relation')->where(['category_id'=>$val['id'],'status'=>1])->count();
            }*/

            $this->assign([
                'list'=>$list,
                'sort'=>$sort,
                'category'=>$category,
                //'total'=>db('post_relation')->where(['status'=>1])->count()
            ]);
            return $this->fetch('sidebar/category');
        }
    }

    /**
     * 侧边热门专栏
     * @param $uid
     * @param string $sort
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function hotColumn($uid,string $sort='new',int $page=1,int $per_page=5)
    {
        $order = array();
        $where[] = ['verify','=',1];
        switch ($sort)
        {
            case 'new':
                $order['create_time'] = 'DESC';
                break;
            case 'hot':
                $order['view_count'] = 'DESC';
                break;
            case 'recommend':
                $order['view_count'] = 'DESC';
                $where[]=['recommend','=',1];
                break;
        }
        $cache_key = 'widget_sidebar_hot_column_' . intval($uid) . '_' . $sort . '_' . intval($page) . '_' . intval($per_page);
        $list = cache($cache_key);
        if ($list === null) {
            $list =  db('column')->where($where)->order($order)->page($page,$per_page)->select()->toArray();
            cache($cache_key, $list, 120);
        }
        foreach ($list as $key => $value)
        {
            $list[$key]['description'] = str_cut(strip_tags(htmlspecialchars_decode($value['description'])),0,50) ;
        }
        $this->assign([
            'list'=>$list,
        ]);
        return $this->fetch('sidebar/column');
    }

    //公告
    public function announce($page=1,$per_page=3)
    {
        $cache_key = 'widget_sidebar_announce_' . intval($page) . '_' . intval($per_page);
        $announce_list = cache($cache_key);
        if ($announce_list === null) {
            $announce_list =  db('announce')
                ->where(['status'=>1])
                ->order(['set_top_time'=>'DESC','sort'=>'DESC'])
                ->page($page,$per_page)
                ->select()
                ->toArray();
            cache($cache_key, $announce_list, 120);
        }

        if($announce_list)
        {
            foreach ($announce_list as $k=>$item) {
                $announce_list[$k]['message'] = str_cut(strip_tags(htmlspecialchars_decode($item['message'])),0,150);
            }
            $this->assign('announce_list',$announce_list);
            return $this->fetch('sidebar/announce');
        }
    }
}
