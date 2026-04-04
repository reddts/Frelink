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

namespace app\frontend;
use app\common\controller\Frontend;
use app\model\Article as ArticleModel;
use app\model\Help as HelpModel;
use think\response\Response;

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
        $type = $this->request->param('type');
        $articleType = trim((string)$this->request->param('article_type', 'all', 'sqlFilter'));
        $articleTypeOptions = frelink_public_article_type_options(true);
        if (!isset($articleTypeOptions[$articleType])) {
            $articleType = 'all';
        }
        $page = $this->request->param('page', 1, 'intval');
        $isAjax = $this->request->isAjax() || $this->request->isPjax() || intval($this->request->param('_ajax', 0)) === 1 || intval($this->request->param('_ajax_open', 0)) === 1;
        $canPageCache = !$this->user_id && !$isAjax && $page === 1;
        $feedQuery = [];
        if ($type) {
            $feedQuery['type'] = $type;
            if ($type === 'article' && $articleType !== 'all') {
                $feedQuery['article_type'] = $articleType;
            }
        }
        $cacheKey = 'page_cache:index:' . md5(json_encode([
            'sort' => (string)$sort,
            'type' => (string)$type,
            'article_type' => (string)$articleType,
            'article_version' => ArticleModel::getHomepageCacheVersion(),
            'lang' => (string)$this->request->cookie('aws_lang', ''),
        ]));

        if ($canPageCache && ($cachedHtml = cache($cacheKey))) {
            return response($cachedHtml)->header(['X-Page-Cache' => 'HIT']);
        }

        $this->assign([
            'sort'=> $sort,
            'current_sort' => $sort,
            'type'=>$type,
            'article_type'=>$articleType,
            'feed_query' => $feedQuery,
            'article_type_options'=>$articleTypeOptions,
            'homepage_research_cards' => ArticleModel::getHomepageFeaturedArticles('research', 2),
            'homepage_fragment_cards' => ArticleModel::getHomepageFeaturedArticles('fragment', 2),
            'archive_chapters'=>HelpModel::getHomepageArchiveHighlights(4, 3),
            'knowledge_map_summary' => HelpModel::getKnowledgeMapSummary(),
            'knowledge_map_connections' => HelpModel::getKnowledgeMapTopicConnections(3, 2),
        ]);
        $this->TDK(get_setting('site_name'));
        $html = $this->fetch();
        if ($canPageCache) {
            cache($cacheKey, $html, 60);
            return response($html)->header(['X-Page-Cache' => 'MISS']);
        }
		return $html;
	}
}
