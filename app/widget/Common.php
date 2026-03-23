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
use app\model\Feature;
use app\model\Article as ArticleModel;
use app\model\Question as QuestionModel;
use app\model\PostRelation;
use app\model\Category;

/**
 * 通用小部件
 * Class Common
 * @package app\ask\widget
 */
class Common extends Widget
{
    protected function homePerPage(): int
    {
        $default = intval(get_setting('contents_per_page')) ?: 15;
        return max(6, min($default, 8));
    }

    protected function normalizeFeedRow(array $row, string $itemType): array
    {
        $row['item_type'] = $itemType;
        $row['create_time'] = intval($row['create_time'] ?? 0);
        $row['update_time'] = intval($row['update_time'] ?? $row['create_time']);
        $row['popular_value'] = floatval($row['popular_value'] ?? 0);
        $row['is_recommend'] = intval($row['is_recommend'] ?? 0);
        return $row;
    }

    protected function buildHomeMixedList(?string $sort, $topic_ids = null, $category_id = null): array
    {
        $page = $this->request->param('page', 1, 'intval');
        $perPage = $this->homePerPage();
        $querySort = $sort ?: 'new';
        $feedSort = $querySort === 'unresponsive' ? 'new' : $querySort;
        $questionLimit = $querySort === 'recommend' ? 4 : 5;
        $articleLimit = $querySort === 'recommend' ? 4 : 5;

        $questionData = QuestionModel::getQuestionList(
            $this->user_id,
            $feedSort,
            $topic_ids,
            $category_id,
            1,
            $questionLimit,
            0,
            'tabMain'
        );
        $articleData = ArticleModel::getArticleList(
            $this->user_id,
            $feedSort,
            $topic_ids,
            $category_id,
            1,
            $articleLimit,
            0,
            'tabMain',
            'all'
        );

        $items = [];
        foreach (($questionData['list'] ?? []) as $row) {
            $items[] = $this->normalizeFeedRow($row, 'question');
        }
        foreach (($articleData['list'] ?? []) as $row) {
            $items[] = $this->normalizeFeedRow($row, 'article');
        }

        usort($items, static function ($left, $right) use ($querySort) {
            if ($querySort === 'recommend') {
                $recommendCompare = ($right['is_recommend'] <=> $left['is_recommend']);
                if ($recommendCompare !== 0) {
                    return $recommendCompare;
                }
            }
            if ($querySort === 'hot') {
                $hotCompare = ($right['popular_value'] <=> $left['popular_value']);
                if ($hotCompare !== 0) {
                    return $hotCompare;
                }
            }
            $topCompare = (intval($right['set_top_time'] ?? 0) <=> intval($left['set_top_time'] ?? 0));
            if ($topCompare !== 0) {
                return $topCompare;
            }
            $updateCompare = ($right['update_time'] <=> $left['update_time']);
            if ($updateCompare !== 0) {
                return $updateCompare;
            }
            return $right['create_time'] <=> $left['create_time'];
        });

        $offset = max(0, ($page - 1) * $perPage);
        $items = array_slice($items, $offset, $perPage);

        return [
            'list' => $items,
            'page' => '',
            'total' => 1,
        ];
    }

    /**
     * 通用内容列表
     * @param null $item_type
     * @param null $sort
     * @param null $topic_ids
     * @param null $category_id
     * @return mixed
     */
	public function lists($item_type=null, $sort = null, $topic_ids = null, $category_id = null, $article_type = null)
	{
        if($html = hook('widget_common_list',$this->request->param()))
        {
            return $this->view->display($html);
        }

        $page = $this->request->param('page', 1, 'intval');
        $canCache = !$this->user_id && strtolower($this->request->controller()) === 'index';
        $cacheKey = 'widget_common_lists:' . md5(json_encode([
            'item_type' => (string)$item_type,
            'sort' => (string)$sort,
            'topic_ids' => is_array($topic_ids) ? implode(',', $topic_ids) : (string)$topic_ids,
            'category_id' => (string)$category_id,
            'article_type' => (string)$article_type,
            'page' => (int)$page,
            'focus' => (int)($sort === 'focus'),
            'lang' => (string)$this->request->cookie('aws_lang', ''),
        ]));
        if ($canCache && ($cachedHtml = cache($cacheKey))) {
            return $cachedHtml;
        }

        $isHomeController = strtolower($this->request->controller()) === 'index';
        $perPage = $isHomeController ? $this->homePerPage() : 0;

        if ($item_type === 'article') {
            $data = ArticleModel::getArticleList(
                $this->user_id,
                $sort,
                $topic_ids,
                $category_id,
                $this->request->param('page',1),
                $perPage,
                0,
                'tabMain',
                $article_type ?: 'all'
            );
        } elseif ($item_type === 'question' || $sort === 'unresponsive') {
            $data = QuestionModel::getQuestionList(
                $this->user_id,
                $sort,
                $topic_ids,
                $category_id,
                $this->request->param('page',1),
                $perPage,
                0,
                'tabMain'
            );
        } elseif ($isHomeController) {
            $data = $this->buildHomeMixedList($sort, $topic_ids, $category_id);
        } else {
		    $data = PostRelation::getPostRelationList($this->user_id,$item_type,$sort,$topic_ids,$category_id,$this->request->param('page',1));
        }

        if (!empty($data['list']) && is_array($data['list'])) {
            $normalizeType = null;
            if ($item_type === 'article') {
                $normalizeType = 'article';
            } elseif ($item_type === 'question' || $sort === 'unresponsive') {
                $normalizeType = 'question';
            }

            if ($normalizeType) {
                foreach ($data['list'] as $idx => $row) {
                    if (is_array($row) && empty($row['item_type'])) {
                        $data['list'][$idx]['item_type'] = $normalizeType;
                    }
                }
            }
        }
		$this->assign($data);
		if($sort=='focus')
        {
            $html = $this->fetch('common/focus');
            if ($canCache) {
                cache($cacheKey, $html, 60);
            }
            return $html;
        }
        $html = $this->fetch('common/lists');
        if ($canCache) {
            cache($cacheKey, $html, 60);
        }
		return $html;
	}

    /**
     * @param int $category 当前分类id
     * @param string $type 分类分组
     * @param string $show_type 分类显示方式，list列表导航形式，image 图片导航方式
     * @return false|mixed
     */
    public function category(int $category=0,string $type='all',string $show_type='list')
    {
        $this->assign([
            'category_list'=>Category::getCategoryListByType($type),
            'category'=>$category,
            'show_type'=>$show_type
        ]);
        return $this->fetch('common/category');
    }

    /**
     * 友情链接小部件
     * @return mixed
     */
    public function links()
    {
        $links = cache('widget_common_links');
        if ($links === null) {
            $links = db('links')->where('status',1)->select()->toArray();
            cache('widget_common_links', $links, 300);
        }
        foreach ($links as $k=>$v)
        {
            $links[$k]['url'] = htmlspecialchars_decode($v['url']);
        }
        $this->assign('links',$links);
        return $this->fetch('common/links');
    }

    //专题关联内容
    public function feature_content($feature_id = 0,$sort='hot',$theme='lists', $content_type='all')
    {
        if($html = hook('widget_common_features',$this->request->param()))
        {
            return $this->view->display($html);
        }
        $data = Feature::getRelationFeatureList($this->user_id,$feature_id,$sort,$this->request->param('page',1),10,'tabMain',$content_type);
        $this->assign($data);
        return $this->fetch('common/'.$theme);
    }
}
