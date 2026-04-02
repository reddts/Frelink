<?php
namespace app\backend;
use app\common\controller\Backend;
use app\common\library\helper\FileHelper;
use app\common\library\helper\MailHelper;
use app\common\library\helper\UpgradeHelper;
use app\model\admin\MenuRule;
use app\model\Help as HelpModel;
use think\App;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Request;
use app\common\logic\common\StatisticLogic;
use app\model\Insight as InsightModel;

class Index extends Backend
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    public function index()
    {
        $sysInfoCacheKey = 'admin_index_sys_info:' . md5($this->request->server('SERVER_SOFTWARE') . '|' . $this->request->server('HTTP_HOST'));
        $sys_info = cache($sysInfoCacheKey);
        if (!$sys_info) {
            $sys_info['os'] = PHP_OS;
            $sys_info['zlib'] = function_exists('gzclose') ? 'YES' : 'NO';//zlib
            $sys_info['safe_mode'] = (boolean)ini_get('safe_mode') ? 'YES' : 'NO';//safe_mode = Off
            $sys_info['timezone'] = function_exists("date_default_timezone_get") ? date_default_timezone_get() : "no_timezone";
            $sys_info['curl'] = function_exists('curl_init') ? 'YES' : 'NO';
            $sys_info['web_server'] = $this->request->server('SERVER_SOFTWARE');
            $sys_info['php_version'] = phpversion();
            $sys_info['ip'] = getServerIp();
            $sys_info['file_upload'] = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknown';
            $sys_info['max_ex_time'] = @ini_get("max_execution_time").'s'; //脚本最大执行时间
            $sys_info['domain'] = $this->request->server('HTTP_HOST');
            $sys_info['memory_limit'] = ini_get('memory_limit');
            $sys_info['version'] = config('version.version');
            $mysqlInfo = \think\facade\Db::query("SELECT VERSION() as version");
            $sys_info['mysql_version'] = $mysqlInfo[0]['version'];
            if (function_exists("gd_info"))
            {
                $gd = gd_info();
                $sys_info['gd_info'] = $gd['GD Version'];
            } else
            {
                $sys_info['gd_info'] = "未知";
            }
            cache($sysInfoCacheKey, $sys_info, 300);
        }

        // 用户数据

        $usersInfoCacheKey = 'admin_index_users_info';
        $users_info = cache($usersInfoCacheKey);
        if (!$users_info) {
            $users_info = [
                'users_count' => db('users')->where('status', 1)->count(),
                'users_valid_email_count' => db('users')->where('is_valid_email', 1)->count(),
                'column_count' => db('column')->count(),
                'article_count' => db('article')->where('status', 1)->count(),
                'question_count' => db('question')->where('status', 1)->count(),
                'answer_count' => db('answer')->where('status', 1)->count(),
                'no_answer_count' => db('question')->where('answer_count', 0)->count(),
                'best_answer_count' => db('answer')->where('is_best', 1)->count(),
                'topic_count' => db('topic')->where('status', 1)->count(),
                'attach_count' => db('attach')->where('status', 1)->count(),
                'approval_question_count' => db('approval')->where(['status' => 0, 'type' => 'question'])->count(),
                'approval_answer_count' => db('approval')->where(['status' => 0, 'type' => 'answer'])->count(),
            ];
            cache($usersInfoCacheKey, $users_info, 60);
        }

        $insightDays = InsightModel::normalizeDays(intval($this->request->param('insight_days', 7)));
        $insight = $this->getInsightPayload($insightDays);
        $archive = HelpModel::getUnarchivedContentSummary(6);
        $archiveBacklog = HelpModel::getArchiveChapterBacklogSummary(8);
        $coldStart = $this->getColdStartPayload();
        $weeklyPlan = InsightModel::getWeeklyExecutionPlan($insightDays, 3);
        if (!empty($weeklyPlan['cold_start']) && is_array($weeklyPlan['cold_start'])) {
            $coldStart = array_merge($coldStart, $weeklyPlan['cold_start']);
        }
        $weeklyExecution = $weeklyPlan['tasks'] ?? [];
        $sitemapUrl = rtrim($this->request->domain(), '/') . '/sitemap.xml';

        $this->view->assign('sysInfo',$sys_info);
        $this->view->assign('usersInfo', $users_info);
        $this->view->assign('insight', $insight);
        $this->view->assign('archive', $archive);
        $this->view->assign('archiveBacklog', $archiveBacklog);
        $this->view->assign('coldStart', $coldStart);
        $this->view->assign('weeklyExecution', $weeklyExecution);
        $this->view->assign('sitemapUrl', $sitemapUrl);
        return $this->view->fetch();
    }

    public function insightBrief()
    {
        $days = InsightModel::normalizeDays(intval($this->request->param('days', 7)));
        $format = strtolower(trim((string)$this->request->param('format', 'markdown')));
        $insight = $this->getInsightPayload($days, false);

        if ($insight['error']) {
            $this->error($insight['error']);
        }

        if (!$insight['enabled']) {
            $this->error('运营洞察表尚未安装，请先执行 docs/analytics-upgrade.sql');
        }

        if ($format === 'json') {
            return json([
                'window_days' => $days,
                'generated_at' => date('Y-m-d H:i:s'),
                'summary' => $insight['summary'],
                'opportunities' => $insight['opportunities'],
                'content' => $insight['content'],
                'topics' => $insight['topics'],
                'recommendations' => $insight['recommendations'],
                'agent_brief' => $insight['agent_brief'],
            ]);
        }

        return response(
            $insight['agent_brief'],
            200,
            ['Content-Type' => 'text/plain; charset=utf-8']
        );
    }

    public function weeklyExecutionBrief()
    {
        $days = InsightModel::normalizeDays(intval($this->request->param('days', 7)));
        $format = strtolower(trim((string)$this->request->param('format', 'markdown')));
        $plan = InsightModel::getWeeklyExecutionPlan($days, 3);

        if ($format === 'json') {
            return json($plan);
        }

        return response(
            InsightModel::renderWeeklyExecutionBrief($plan),
            200,
            ['Content-Type' => 'text/plain; charset=utf-8']
        );
    }

    public function sitemapBrief()
    {
        $format = strtolower(trim((string) $this->request->param('format', 'markdown')));
        $sitemapUrl = rtrim($this->request->domain(), '/') . '/sitemap.xml';
        $brief = [
            'generated_at' => date('Y-m-d H:i:s'),
            'sitemap_url' => $sitemapUrl,
            'submission_links' => [
                [
                    'name' => 'Google Search Console',
                    'url' => 'https://search.google.com/search-console',
                ],
                [
                    'name' => 'Bing Webmaster Tools',
                    'url' => 'https://www.bing.com/webmasters',
                ],
            ],
            'steps' => [
                '登录对应站长平台。',
                '提交 sitemap.xml 地址。',
                '检查抓取/索引状态并修复报错链接。',
            ],
        ];

        if ($format === 'json') {
            return json($brief);
        }

        $lines = [];
        $lines[] = '# Sitemap 提交清单';
        $lines[] = '';
        $lines[] = '- 生成时间：' . $brief['generated_at'];
        $lines[] = '- Sitemap：' . $brief['sitemap_url'];
        $lines[] = '- 提交入口：';
        foreach ($brief['submission_links'] as $link) {
            $lines[] = '  - ' . $link['name'] . '：' . $link['url'];
        }
        $lines[] = '- 推荐动作：';
        foreach ($brief['steps'] as $step) {
            $lines[] = '  - ' . $step;
        }

        return response(
            implode("\n", $lines),
            200,
            ['Content-Type' => 'text/plain; charset=utf-8']
        );
    }

    public function archivePicker()
    {
        $itemType = trim((string)$this->request->param('item_type', ''));
        $itemId = intval($this->request->param('item_id', 0));
        if (!in_array($itemType, ['question', 'article'], true) || !$itemId) {
            $this->error('参数错误');
        }

        if ($this->request->isPost()) {
            $chapterIds = array_values(array_unique(array_filter(array_map('intval', $this->request->post('help_chapter_ids', [])))));
            HelpModel::syncItemArchiveChapters($itemType, $itemId, $chapterIds);
            $this->success('归档更新成功');
        }

        $table = $itemType === 'question' ? 'question' : 'article';
        $itemInfo = db($table)->where('id', $itemId)->field('id,title')->find();
        if (!$itemInfo) {
            $this->error('内容不存在');
        }

        $selectedHelpChapterIds = HelpModel::getItemArchiveChapterIds($itemType, $itemId);
        $helpChapterOptions = HelpModel::getActiveChapterList();
        foreach ($helpChapterOptions as $k => $chapter) {
            $helpChapterOptions[$k]['selected'] = in_array($chapter['id'], $selectedHelpChapterIds);
        }

        $this->assign([
            'item_type' => $itemType,
            'item_id' => $itemId,
            'item_info' => $itemInfo,
            'help_chapter_options' => $helpChapterOptions,
        ]);

        return $this->fetch('index/archive_picker');
    }

    public function chapterCandidates()
    {
        $chapterId = intval($this->request->param('chapter_id', 0));
        if (!$chapterId) {
            $this->error('参数错误');
        }

        $candidates = HelpModel::getChapterCandidateContent($chapterId, 6);
        if (empty($candidates['chapter'])) {
            $this->error('章节不存在');
        }

        $this->assign($candidates);
        return $this->fetch('index/chapter_candidates');
    }

    public function attachArchive()
    {
        $chapterId = intval($this->request->param('chapter_id', 0));
        $itemType = trim((string)$this->request->param('item_type', ''));
        $itemId = intval($this->request->param('item_id', 0));
        if (!$chapterId || !$itemId || !in_array($itemType, ['question', 'article'], true)) {
            $this->error('参数错误');
        }

        if (!HelpModel::attachItemArchiveChapter($itemType, $itemId, $chapterId)) {
            $this->error('归档失败');
        }

        $this->success('已归档到章节');
    }

    public function autoKnowledgeMap()
    {
        $chapterLimit = intval($this->request->param('chapter_limit', 5));
        $itemsPerChapter = intval($this->request->param('items_per_chapter', 6));
        $result = HelpModel::bootstrapKnowledgeMap($chapterLimit, $itemsPerChapter);

        if (empty($result['chapter_count']) && empty($result['attached_count'])) {
            $this->error($result['message'] ?? '未生成可用的知识地图数据');
        }

        $message = $result['message'] ?? '知识地图已自动初始化完成';
        $message .= '，新建章节 ' . intval($result['chapter_count'] ?? 0) . ' 个，复用章节 ' . intval($result['reused_count'] ?? 0) . ' 个，归档内容 ' . intval($result['attached_count'] ?? 0) . ' 条。';
        $this->success($message);
    }

    protected function getInsightPayload(int $insightDays, bool $useCache = true): array
    {
        $insight = [
            'enabled' => checkTableExist('analytics_event'),
            'window_days' => $insightDays,
            'summary' => [],
            'content' => [],
            'topics' => [],
            'opportunities' => [],
            'recommendations' => [],
            'agent_brief' => '',
            'daily_report' => [],
            'error' => '',
        ];

        if (!$insight['enabled']) {
            return $insight;
        }

        $insightCacheKey = 'admin_index_insight:' . $insightDays;
        $cachedInsight = $useCache ? cache($insightCacheKey) : null;

        if (!$cachedInsight) {
            try {
                $cachedInsight = [
                    'summary' => InsightModel::getWindowSummary($insightDays),
                    'content' => InsightModel::getContentTrends($insightDays, 6),
                    'topics' => InsightModel::getTopicTrends($insightDays, 6),
                    'opportunities' => InsightModel::getSearchOpportunities($insightDays, 6),
                    'recommendations' => InsightModel::getRecommendations($insightDays, 6),
                ];
                $cachedInsight['agent_brief'] = $this->buildInsightBrief($insightDays, $cachedInsight);
                $cachedInsight['daily_report'] = InsightModel::getDailyReportSnapshot(7, 5);

                if ($useCache) {
                    cache($insightCacheKey, $cachedInsight, 300);
                }
            } catch (\Throwable $e) {
                $insight['error'] = $e->getMessage();
            }
        }

        if (!$insight['error'] && is_array($cachedInsight)) {
            $insight = array_merge($insight, $cachedInsight);
        }

        return $insight;
    }

    public function dailyInsightBrief()
    {
        $days = InsightModel::normalizeDays(intval($this->request->param('days', 7)));
        $limit = max(1, min(20, intval($this->request->param('limit', 5))));
        $format = strtolower(trim((string) $this->request->param('format', 'markdown')));
        $refresh = intval($this->request->param('refresh', 0)) === 1;
        $report = InsightModel::getDailyReportSnapshot($days, $limit, $refresh);

        if ($format === 'json') {
            return json($report);
        }

        return response(
            $report['markdown'] ?? '',
            200,
            ['Content-Type' => 'text/plain; charset=utf-8']
        );
    }

    protected function buildInsightBrief(int $days, array $insight): string
    {
        $lines = [];
        $summary = $insight['summary'] ?? [];
        $lines[] = 'Frelink 运营简报';
        $lines[] = '统计窗口：最近 ' . $days . ' 天';
        $lines[] = '搜索次数：' . intval($summary['search_count'] ?? 0)
            . '，曝光次数：' . intval($summary['impression_count'] ?? 0)
            . '，点击次数：' . intval($summary['click_count'] ?? 0)
            . '，详情阅读：' . intval($summary['detail_view_count'] ?? 0)
            . '，窗口CTR：' . ($summary['ctr'] ?? 0);

        if (!empty($insight['opportunities'])) {
            $lines[] = '';
            $lines[] = '搜索缺口：';
            foreach (array_slice($insight['opportunities'], 0, 3) as $item) {
                $lines[] = '- ' . $item['keyword'] . '：搜索 ' . intval($item['search_count']) . ' 次，覆盖 ' . intval($item['matched_content_count']) . '，建议 ' . $item['suggestion'];
            }
        }

        if (!empty($insight['content'])) {
            $lines[] = '';
            $lines[] = '内容热点：';
            foreach (array_slice($insight['content'], 0, 3) as $item) {
                $lines[] = '- [' . $item['item_type'] . '] ' . $item['title'] . '：曝光 ' . intval($item['impressions']) . '，点击 ' . intval($item['clicks']) . '，阅读 ' . intval($item['detail_views']) . '，CTR ' . $item['ctr'];
            }
        }

        if (!empty($insight['recommendations'])) {
            $lines[] = '';
            $lines[] = '建议动作：';
            foreach (array_slice($insight['recommendations'], 0, 3) as $item) {
                $lines[] = '- [' . $item['priority'] . '] ' . $item['title'] . '：' . $item['suggestion'];
            }
        }

        return implode(PHP_EOL, $lines);
    }

    protected function getColdStartPayload(): array
    {
        $targets = [
            'research' => ['label' => '综述', 'target' => 12],
            'fragment' => ['label' => '观察', 'target' => 20],
            'faq' => ['label' => 'FAQ', 'target' => 30],
            'help' => ['label' => '帮助', 'target' => 12],
            'chapter' => ['label' => '知识章节', 'target' => 8],
        ];

        $counts = [
            'research' => db('article')->where(['status' => 1, 'article_type' => 'research'])->count(),
            'fragment' => db('article')->where(['status' => 1, 'article_type' => 'fragment'])->count(),
            'faq' => db('question')->where(['status' => 1])->count(),
            'help' => db('article')->where(['status' => 1])->whereIn('article_type', ['tutorial', 'faq'])->count(),
            'chapter' => db('help_chapter')->where(['status' => 1])->count(),
        ];

        $items = [];
        $completedTargets = 0;
        foreach ($targets as $key => $meta) {
            $current = intval($counts[$key] ?? 0);
            $target = intval($meta['target']);
            $progress = $target > 0 ? min(100, round(($current / $target) * 100)) : 0;
            $gap = max(0, $target - $current);
            if ($current >= $target) {
                $completedTargets++;
            }
            $items[$key] = [
                'label' => $meta['label'],
                'current' => $current,
                'target' => $target,
                'gap' => $gap,
                'progress' => $progress,
                'status' => $current >= $target ? '已达标' : ($progress >= 60 ? '接近达标' : '待补足'),
            ];
        }

        $overallProgress = round(($completedTargets / count($targets)) * 100);

        $recentResearch = db('article')
            ->where(['status' => 1, 'article_type' => 'research'])
            ->field('id,title,update_time')
            ->order(['update_time' => 'DESC', 'id' => 'DESC'])
            ->limit(3)
            ->select()
            ->toArray();
        $recentFragment = db('article')
            ->where(['status' => 1, 'article_type' => 'fragment'])
            ->field('id,title,update_time')
            ->order(['update_time' => 'DESC', 'id' => 'DESC'])
            ->limit(3)
            ->select()
            ->toArray();
        $recentFaq = db('question')
            ->where(['status' => 1])
            ->field('id,title,update_time')
            ->order(['update_time' => 'DESC', 'id' => 'DESC'])
            ->limit(3)
            ->select()
            ->toArray();
        $recentHelp = db('article')
            ->where(['status' => 1])
            ->whereIn('article_type', ['tutorial', 'faq'])
            ->field('id,title,update_time,article_type')
            ->order(['update_time' => 'DESC', 'id' => 'DESC'])
            ->limit(3)
            ->select()
            ->toArray();

        foreach ($recentHelp as $k => $item) {
            $recentHelp[$k]['article_type_label'] = frelink_article_type_label($item['article_type'] ?? 'faq');
        }

        $recommendations = [];
        foreach ($items as $key => $item) {
            if ($item['gap'] <= 0) {
                continue;
            }

            $action = '';
            $primaryLabel = '立即处理';
            $primaryUrl = '';
            $secondaryLabel = '查看现有内容';
            $secondaryUrl = '';
            switch ($key) {
                case 'research':
                    $action = '优先补 1-2 篇研究综述，建立可被搜索和引用的核心判断内容。';
                    $primaryLabel = '去写综述';
                    $primaryUrl = get_url('article/publish', ['article_type' => 'research']);
                    $secondaryUrl = get_url('article/index', ['type' => 'research']);
                    break;
                case 'fragment':
                    $action = '优先补一批观察记录，保证主题页和首页有持续更新的轻内容。';
                    $primaryLabel = '去写观察';
                    $primaryUrl = get_url('article/publish', ['article_type' => 'fragment']);
                    $secondaryUrl = get_url('article/index', ['type' => 'fragment']);
                    break;
                case 'faq':
                    $action = '优先从高频问题里补 FAQ，先覆盖检索入口，再慢慢升级为综述或帮助。';
                    $primaryLabel = '去补 FAQ';
                    $primaryUrl = get_url('question/publish');
                    $secondaryUrl = get_url('question/index');
                    break;
                case 'help':
                    $action = '优先补帮助型内容，把规则、术语和方法沉淀成稳定知识资产。';
                    $primaryLabel = '去写帮助';
                    $primaryUrl = get_url('article/publish', ['article_type' => 'faq']);
                    $secondaryUrl = get_url('article/index', ['type' => 'faq']);
                    break;
                case 'chapter':
                    $action = '优先补知识章节，把已有内容归档成可长期维护的知识地图结构。';
                    $primaryLabel = '管理章节';
                    $primaryUrl = (string)url('content.help/index');
                    $secondaryLabel = '查看知识地图';
                    $secondaryUrl = get_url('help/index');
                    break;
            }

            $recommendations[] = [
                'key' => $key,
                'label' => $item['label'],
                'gap' => $item['gap'],
                'progress' => $item['progress'],
                'status' => $item['status'],
                'action' => $action,
                'primary_label' => $primaryLabel,
                'primary_url' => $primaryUrl,
                'secondary_label' => $secondaryLabel,
                'secondary_url' => $secondaryUrl,
            ];
        }

        usort($recommendations, function ($a, $b) {
            if (intval($a['gap']) === intval($b['gap'])) {
                return intval($a['progress']) <=> intval($b['progress']);
            }
            return intval($b['gap']) <=> intval($a['gap']);
        });

        return [
            'overall_progress' => $overallProgress,
            'completed_targets' => $completedTargets,
            'target_total' => count($targets),
            'items' => $items,
            'recommendations' => array_slice($recommendations, 0, 3),
            'recent' => [
                'research' => $recentResearch,
                'fragment' => $recentFragment,
                'faq' => $recentFaq,
                'help' => $recentHelp,
            ],
        ];
    }

    //后台统计
    public function statistic()
    {
        if (!$start_time = strtotime($_GET['start_date'] . ' 00:00:01'))
        {
            $start_time = strtotime('-12 months');
        }

        if (!$end_time = strtotime($_GET['end_date'] . ' 23:59:59'))
        {
            $end_time = time();
        }

        $statistic_tag = $_GET['tag'] ? explode(',', $_GET['tag']) : [];
        if (empty($statistic_tag)) exit;
        if (!$month_list = get_month_list($start_time, $end_time, 'y')) exit;
        $data = $labels = $statistic = $data_template = [];
        foreach ($month_list AS $key => $val)
        {
            $labels[] = $val['year'] . '-' . $val['month'];
            $data_template[] = 0;
        }

        foreach ($statistic_tag AS $key => $val)
        {
            switch ($val)
            {
                case 'new_article':
                case 'new_answer':  // 新增答案
                case 'new_question':    // 新增问题
                case 'new_user':    // 新注册用户
                case 'user_valid':  // 新激活用户
                case 'new_topic':   // 新增话题
                case 'new_answer_vote': // 新增答案投票
                case 'new_answer_thanks': // 新增答案感谢
                    $statistic[] = StatisticLogic::singleTagData($val, $start_time, $end_time);
                    break;
            }
        }

        foreach($statistic AS $key => $val)
        {
            $statistic_data = $data_template;
            foreach ($val AS $k => $v)
            {
                $data_key = array_search($v['date'], $labels);
                $statistic_data[$data_key] = $v['count'];
            }
            $data[] = $statistic_data;
        }

        $this->success('', null, ['labels' => $labels, 'data' => $data]);
    }

    //系统登录
    public function login()
    {
        if (session('admin_login_uid'))
        {
            $this->redirect(url('index'));
        }

        if($this->request->isPost())
        {
            $postData = $this->request->post();
            $postData['password'] = authCode($postData['password'],'DECODE',$postData['token']);
            if(!$this->auth->login($postData['username'],$postData['password']))
            {
                $this->error('账号或密码错误');
            }
            session('admin_logout_locked', null);
            $this->success('登录成功',url('index/index'));
        }

        if($login_user_info = get_user_info((int) session('admin_login_uid')))
        {
            $this->assign('user_info',$login_user_info);
        }

        return $this->fetch();
    }

    //退出登录
    public function logout()
    {
        session('admin_logout_locked', 1);
        session('admin_user_info',null);
        session('admin_login_user_info',null);
        session('admin_login_uid',null);
        $this->success('退出成功', url('index/login'));
    }

    //清除缓存
    public function clear()
    {
        $type = $this->request->param('type','cache');
        $path = runtime_path();
        if($type!='all')
        {
            $path = runtime_path($type);
        }

        if($type=='cache' || $type=='all')
        {
            Cache::clear();
        }

        if (FileHelper::delDir($path)) {
            $this->success('清除成功');
        }
        $this->error('清除失败');
    }

    /**
     * 图标
     * @return mixed
     */
    public function icons()
    {
        return $this->view->fetch('global/icons');
    }

    //发送测试邮件
    public function send_test_email()
    {
        if($this->request->isPost())
        {
            $email = $this->request->post('email');
            $subject = get_setting('site_name').'测试邮件';
            $message = '该邮件为测试邮件，请勿回复';
            $res = MailHelper::sendEmail($email, $subject, $message);
            if ($res['code'] == 0) $this->error($res['message']);
            $this->success('测试邮件发送成功');
        }
        return $this->formBuilder
            ->addText('email','邮箱地址','请输入测试邮箱地址')
            ->fetch();
    }

    //检查缓存启用状态
    public function cache_type_check()
    {
        if($this->request->isPost())
        {
            $cache_type = $this->request->post('cache_type','file');
            $message = '';
            Config::set([
                // 服务器地址
                'host' => $this->request->post('cache_host','127.0.0.1'),
                // 端口号
                'port' => $this->request->post('cache_port','11211'),
                // 密码
                'password'=> $this->request->post('cache_password','11211'),
            ],'aws');
            try {
                Cache::store($cache_type)->set('aws_cache_test', 'WeCenter');
            }catch (\Exception $e){
                $message = $e->getMessage();
            }
            if(!$message)
            {
                $data = $this->request->post();

                foreach ($data as $k => $v) {
                    if (is_array($v) && isset($v['key']) && isset($v['value'])) {
                        $value = [];
                        foreach ($v['key'] as $k1=>$v1)
                        {
                            $value[$v1] = $v['value'][$k1];
                        }
                        $data[$k] = $value;
                    }
                }
                $configList = [];
                foreach (db('config')->select()->toArray() as $v)
                {
                    if (isset($data[$v['name']])) {
                        $value = $data[$v['name']];
                        $option = json_decode($v['option'],true);
                        if(in_array($v['type'],['array','images','files'])){
                            $option = $value;
                            $value = 0;
                        } else{
                            $value = is_array($value) ? implode(',', $value) : $value;
                        }
                        $v['value'] = $value;
                        $v['option'] = json_encode($option,JSON_UNESCAPED_UNICODE);
                        $configList[] = $v;
                    }
                }
                $ConfigModel = new \app\model\Config();
                $ConfigModel->saveAll($configList);
                $this->success('修改成功');
            }

            $this->error($message);
        }
        $memcacheEnable = class_exists('Memcache')?1:0;
        $memcachedEnable = class_exists('Memcached')?1:0;
        $redisEnable = class_exists('Redis')?1:0;

        $html = 'Memcache模块：'.($memcacheEnable?'<span class="text-green">已开启</span>':'<span class="text-danger">已禁用</span>').'<br>';
        $html.='Memcached模块：'.($memcachedEnable?'<span class="text-green">已开启</span>':'<span class="text-danger">已禁用</span>').'<br>';
        $html.='Redis模块：'.($redisEnable?'<span class="text-green">已开启</span>':'<span class="text-danger">已禁用</span>');
        return $this->formBuilder
            ->setPageTips($html,'info')
            ->addRadio('cache_type','缓存方式','',['redis'=>'Redis','memcached'=>'Memcached','memcache'=>'Memcache'],'redis')
            ->addText('cache_host','主机地址','','127.0.0.1')
            ->addText('cache_port','主机端口')
            ->addText('cache_password','主机密码','没有可不填')
            ->setBtnTitle('submit','检查并配置')
            ->fetch();
    }
}
