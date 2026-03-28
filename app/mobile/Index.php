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

namespace app\mobile;
use app\common\controller\Frontend;
use app\model\Help as HelpModel;
use app\model\Insight as InsightModel;

class Index extends Frontend
{
    protected function resolveSort(): string
    {
        $allowed = ['focus', 'recommend', 'new', 'hot', 'unresponsive'];
        $sort = $this->request->param('sort', '', 'sqlFilter');
        if ($sort && in_array($sort, $allowed, true)) {
            return $sort;
        }

        $candidates = [
            trim((string)$this->request->pathinfo(), '/'),
            (string)$this->request->url(),
            (string)$this->request->baseUrl(),
            (string)$this->request->server('REQUEST_URI'),
        ];

        foreach ($candidates as $candidate) {
            if ($candidate && preg_match('#/(?:m/)?explore/(focus|recommend|new|hot|unresponsive)\.html#i', '/' . ltrim($candidate, '/'), $matches)) {
                return strtolower($matches[1]);
            }
        }

        return 'new';
    }

    public function index()
    {
        $sort = $this->resolveSort();
        $type = $this->request->param('type', '');
        $articleType = frelink_normalize_article_type($this->request->param('article_type', 'all', 'sqlFilter'), 'all');
        $feedQuery = [];
        if ($type) {
            $feedQuery['type'] = $type;
            if ($type === 'article' && $articleType !== 'all') {
                $feedQuery['article_type'] = $articleType;
            }
        }
        $searchKeywords = [];
        $featuredContent = [];

        if (checkTableExist('analytics_event')) {
            $searchKeywords = InsightModel::getTopKeywords(7, 6);
            $featuredContent = InsightModel::getContentTrends(7, 3);
        }

        $this->assign([
            'sort'=> $sort,
            'current_sort' => $sort,
            'type'=> $type,
            'article_type' => $articleType,
            'feed_query' => $feedQuery,
            'article_type_options' => frelink_article_type_options(true),
            'search_keywords' => $searchKeywords,
            'featured_content' => $featuredContent,
            'archive_chapters' => HelpModel::getHomepageArchiveHighlights(3, 2),
        ]);

        $linksCacheKey = 'mobile_home_links:status1';
        $links = cache($linksCacheKey);
        if ($links === null) {
            $links = db('links')->where('status',1)->select()->toArray();
            cache($linksCacheKey, $links, 600);
        }
        $this->assign('links', $links);
        $this->TDK(get_setting('site_name'));
		return $this->fetch();
	}
}
