{extend name="$theme_block" /}
{block name="main"}
<div class="aui-content mescroll" id="ajaxPage">
    <style>
        .aw-mobile-hero {
            margin: 12px;
            padding: 18px 16px 44px;
            border-radius: 22px;
            background:
                radial-gradient(circle at 16% 18%, rgba(96, 165, 250, 0.3), transparent 28%),
                radial-gradient(circle at 82% 20%, rgba(52, 211, 153, 0.2), transparent 24%),
                linear-gradient(135deg, #08152b 0%, #0b3158 44%, #0d6a64 100%);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);
            overflow: hidden;
        }
        .aw-mobile-hero-title {
            margin-bottom: 10px;
            color: #fff;
            font-size: 26px;
            font-weight: 700;
            line-height: 1.25;
        }
        .aw-mobile-hero-desc {
            margin-bottom: 16px;
            color: rgba(226, 232, 240, 0.9);
            font-size: 13px;
            line-height: 1.75;
        }
        .aw-mobile-search-form {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
            padding: 10px;
            border-radius: 18px;
            background: rgba(7, 16, 31, 0.28);
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(8px);
        }
        .aw-mobile-search-input {
            flex: 1;
            height: 50px;
            padding: 0 16px;
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: 14px;
            background: rgba(255,255,255,0.08);
            font-size: 14px;
            color: #fff;
        }
        .aw-mobile-search-input::placeholder {
            color: rgba(255,255,255,.72);
        }
        .aw-mobile-search-btn {
            height: 50px;
            padding: 0 18px;
            border: 0;
            border-radius: 14px;
            background: linear-gradient(90deg, #0e81e9 0%, #33a756 100%);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
        }
        .aw-mobile-quick-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }
        .aw-mobile-quick-card {
            display: block;
            padding: 12px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(8px);
        }
        .aw-mobile-quick-card strong {
            display: block;
            margin-bottom: 4px;
            font-size: 14px;
        }
        .aw-mobile-quick-card span {
            display: block;
            color: rgba(226, 232, 240, 0.82);
            font-size: 12px;
            line-height: 1.5;
        }
        .aw-mobile-hero-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 14px;
        }
        .aw-mobile-hero-standards {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            margin-top: 14px;
        }
        .aw-mobile-hero-standard {
            padding: 10px 11px;
            border-radius: 14px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.12);
            color: #fff;
        }
        .aw-mobile-hero-standard strong {
            display: block;
            margin-bottom: 4px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.35;
        }
        .aw-mobile-hero-standard span {
            display: block;
            color: rgba(226,232,240,0.82);
            font-size: 11px;
            line-height: 1.45;
        }
        .aw-mobile-hero-badge {
            display: inline-flex;
            align-items: center;
            padding: 7px 10px;
            border-radius: 999px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.12);
            color: #fff;
            font-size: 11px;
            font-weight: 600;
        }
        .aw-mobile-hero-panel {
            margin-top: 14px;
            padding: 14px;
            border-radius: 18px;
            background: rgba(7, 16, 31, 0.42);
            border: 1px solid rgba(255,255,255,0.08);
            border-left: 3px solid rgba(103, 232, 249, 0.78);
            color: #fff;
        }
        .aw-mobile-hero-panel strong {
            display: block;
            margin-bottom: 6px;
            font-size: 16px;
        }
        .aw-mobile-hero-panel p {
            margin: 0;
            color: rgba(226, 232, 240, 0.84);
            font-size: 12px;
            line-height: 1.7;
        }
        .aw-mobile-section {
            margin: 12px;
            padding: 14px;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
        }
        .aw-mobile-section-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 12px;
        }
        .aw-mobile-section-head h4 {
            margin: 0 0 4px;
            color: #0f172a;
            font-size: 15px;
            font-weight: 700;
        }
        .aw-mobile-section-head p {
            margin: 0;
            color: #64748b;
            font-size: 12px;
            line-height: 1.6;
        }
        .aw-mobile-section-link {
            flex: 0 0 auto;
            padding: 7px 10px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }
        .aw-mobile-structure {
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        }
        .aw-mobile-section-title {
            margin-bottom: 10px;
            color: #0f172a;
            font-size: 15px;
            font-weight: 700;
        }
        .aw-mobile-chip-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .aw-mobile-chip {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 12px;
        }
        .aw-mobile-feature-item {
            display: block;
            padding: 10px 0;
            border-bottom: 1px solid #eef2f7;
            color: #0f172a;
        }
        .aw-mobile-feature-item:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }
        .aw-mobile-feature-item small {
            display: block;
            margin-bottom: 4px;
            color: #2563eb;
        }
        .aw-mobile-feature-item p {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 12px;
            line-height: 1.5;
        }
        .aw-mobile-explore {
            margin: 12px;
            padding: 14px;
            border-radius: 18px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
        }
        .aw-mobile-explore .aui-card {
            margin-bottom: 0;
        }
        #ajaxPage .aui-noticebar {
            margin: 12px;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
        }
        .aw-mobile-lane-list a {
            display: block;
            padding: 14px;
            border-radius: 16px;
            background: linear-gradient(180deg, #f9fcff 0%, #ffffff 100%);
            border: 1px solid #e5eef8;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
            color: #0f172a;
        }
        .aw-mobile-lane-list a:last-child {
            padding-bottom: 14px;
        }
        .aw-mobile-lane-list small {
            display: block;
            margin-bottom: 4px;
            color: #2563eb;
        }
        .aw-mobile-lane-list p {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 12px;
            line-height: 1.5;
        }
        .aw-mobile-feed-filter {
            display: flex;
            gap: 8px;
            padding: 0 14px 12px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .aw-mobile-feed-filter a {
            flex: 0 0 auto;
            padding: 6px 12px;
            border-radius: 999px;
            border: 1px solid #dbe7f3;
            background: #fff;
            color: #60758b;
            font-size: 12px;
            white-space: nowrap;
        }
        .aw-mobile-feed-filter a.active {
            background: #1d4ed8;
            border-color: #1d4ed8;
            color: #fff;
        }
        .aw-mobile-feed-shell {
            margin: 12px;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }
        .aw-mobile-feed-shell .aw-mobile-section-head {
            margin-bottom: 0;
            padding: 14px 14px 12px;
            align-items: flex-start;
        }
        .aw-mobile-feed-shell .aw-mobile-section-head h4 {
            margin-bottom: 6px;
            font-size: 16px;
        }
        .aw-mobile-feed-shell .aw-mobile-section-head p {
            font-size: 12px;
        }
        .aw-mobile-feed-list {
            padding: 0 14px 14px;
        }
        .aw-mobile-feed-list #ajaxResult {
            margin: 0;
        }
        .aw-mobile-feed-list #ajaxResult > .aui-card {
            margin: 0 0 12px;
        }
        .aw-mobile-feed-list #ajaxResult > .aui-card:last-child {
            margin-bottom: 0;
        }
        .aw-mobile-feed-list #ajaxResult > .aui-card > .aui-card-main > .aui-card {
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }
        .aw-mobile-feed-list #ajaxResult > .aui-card > .aui-card-main > .aui-card .aui-card-title {
            border-top-left-radius: 18px;
            border-top-right-radius: 18px;
        }
    </style>
    <section class="aw-mobile-hero">
        <div class="aw-mobile-hero-title">{:L('公开、开放、可检索的知识系统')}</div>
        <div class="aw-mobile-hero-desc">{:L('用综述整理脉络，用观察保留变化，用 FAQ 承接检索，再通过主题和观察专题把内容长期串起来。')}</div>
        <form class="aw-mobile-search-form" action="{:url('search/index')}" method="get">
            <input class="aw-mobile-search-input" type="search" name="q" placeholder="{:L('搜索综述、观察、FAQ、主题或知识章节')}">
            <button class="aw-mobile-search-btn" type="submit">{:L('搜索')}</button>
        </form>
        <div class="aw-mobile-quick-grid">
            <a class="aw-mobile-quick-card" href="{:url('article/index',['type'=>'research'])}">
                <strong>{:L('综述')}</strong>
                <span>{:L('集中整理资料、分歧和阶段性结论')}</span>
            </a>
            <a class="aw-mobile-quick-card" href="{:url('topic/index')}">
                <strong>{:L('主题')}</strong>
                <span>{:L('按主题聚合变化、资料和后续追踪点')}</span>
            </a>
            <a class="aw-mobile-quick-card" href="{:url('feature/index')}" data-pjax="pageMain">
                <strong>{:L('观察专题')}</strong>
                <span>{:L('进入长期观察专题，持续查看同一主题下的动态、判断和沉淀')}</span>
            </a>
            <a class="aw-mobile-quick-card" href="{:url('question/index')}">
                <strong>{:L('FAQ')}</strong>
                <span>{:L('高频 FAQ、术语解释和可快速复用的明确答案')}</span>
            </a>
        </div>
        <div class="aw-mobile-hero-badges">
            <span class="aw-mobile-hero-badge">{:L('开放检索')}</span>
            <span class="aw-mobile-hero-badge">{:L('长期归档')}</span>
            <span class="aw-mobile-hero-badge">{:L('保留判断')}</span>
        </div>
        <div class="aw-mobile-hero-panel">
            <strong>{:L('公开知识系统')}</strong>
            <p>{:L('这里按综述、观察、FAQ、主题和知识归档组织内容，重点是可检索、可追踪、可持续更新，而不是短期流量展示。')}</p>
        </div>
    </section>
    {if !empty($search_keywords)}
    <section class="aw-mobile-section aw-mobile-structure">
        <div class="aw-mobile-section-title">{:L('最近有人在搜')}</div>
        <div class="aw-mobile-chip-list">
            {volist name="search_keywords" id="item"}
            <a class="aw-mobile-chip" href="{:url('search/index',['q'=>$item['keyword']])}">{$item.keyword}</a>
            {/volist}
        </div>
    </section>
    {/if}
    {if !empty($featured_content)}
    <section class="aw-mobile-section">
        <div class="aw-mobile-section-title">{:L('本周值得看')}</div>
        {volist name="featured_content" id="item"}
        <a class="aw-mobile-feature-item" href="{$item.url}" data-pjax="pageMain">
            <small>{$item.item_type} · {:L('阅读')} {$item.detail_views}</small>
            <strong>{$item.title}</strong>
            {if $item.summary}<p>{$item.summary}</p>{/if}
        </a>
        {/volist}
    </section>
    {/if}
    {if !empty($archive_chapters)}
    <section class="aw-mobile-section">
        <div class="aw-mobile-section-title">{:L('知识归档')}</div>
        <div class="aw-mobile-lane-list">
            {volist name="archive_chapters" id="chapter"}
            <a href="{$chapter.link_url}" data-pjax="pageMain">
                <small>{$chapter.source_label} · {$chapter.metric_value} {$chapter.metric_label}</small>
                <strong>{$chapter.title}</strong>
                <p>{$chapter.summary}</p>
            </a>
            {/volist}
        </div>
    </section>
    {/if}
    {:widget('sidebar/announce')}
    <section class="aw-mobile-explore">
        <div class="aw-mobile-section-head">
            <div>
                <h4>{:L('继续探索')}</h4>
                <p>{:L('先看热门话题和专题，再顺着横向列表继续浏览。')}</p>
            </div>
            <a class="aw-mobile-section-link" href="{:url('topic/index',['type'=>'discuss'])}" data-pjax="pageMain">{:L('去话题广场')}</a>
        </div>
        <!--热门用户、话题-->
        {if get_theme_setting('mobile.index_hot_banner_enable','Y')=='Y'}
        <div class="hotUserTopic d-flex p-2">
                <div class="flex-fill mr-1">
                    <a href="{:get_theme_setting('mobile.index_hot_user_url',(string)url('people/lists'))}">
                    <img src="{:get_theme_setting('mobile.index_hot_user_image','/templates/mobile/img/hot-user.png')}" style="width: 100%;display: inline-block" loading="lazy" decoding="async" alt="{:L('热门用户')}">
                    </a>
                </div>
                <div class="flex-fill ml-1">
                    <a href="{:get_theme_setting('mobile.index_hot_topic_url',(string)url('topic/index'))}">
                    <img src="{:get_theme_setting('mobile.index_hot_topic_image','/templates/mobile/img/hot-topic.png')}" style="width: 100%;display: inline-block" loading="lazy" decoding="async" alt="{:L('热门话题')}">
                    </a>
                </div>
            </div>
        {/if}

        {:widget('sidebar/hotTopic',['uid'=>$user_id])}
    </section>
    <section class="aw-mobile-feed-shell">
        <div class="aw-mobile-section-head">
            <div>
                <h4>{:L('持续更新')}</h4>
            </div>
        </div>
        <div class="aw-mobile-feed-filter">
            <a class="{if !$type || $current_sort=='unresponsive'}active{/if}" data-pjax="pageMain" href="{:url('index/index',['sort'=>$current_sort])}">{:L('全部内容')}</a>
            <a class="{if $type=='question'}active{/if}" data-pjax="pageMain" href="{:url('index/index',['sort'=>$current_sort,'type'=>'question'])}">{:L('FAQ')}</a>
            {if $current_sort!='unresponsive'}
            <a class="{if $type=='article' && $article_type=='research'}active{/if}" data-pjax="pageMain" href="{:url('index/index',['sort'=>$current_sort,'type'=>'article','article_type'=>'research'])}">{:L('综述')}</a>
            <a class="{if $type=='article' && $article_type=='fragment'}active{/if}" data-pjax="pageMain" href="{:url('index/index',['sort'=>$current_sort,'type'=>'article','article_type'=>'fragment'])}">{:L('观察')}</a>
            {/if}
        </div>
        <div class="aw-mobile-feed-list">
            <div class="aw-common-list" id="ajaxResult"></div>
        </div>
    </section>
</div>

<script>
    var mescroll = new MeScroll('ajaxPage', {
        down: {
            callback: function (){
                mescroll.resetUpScroll();
            }
        },
        up: {
            callback: function (page)
            {
                var pageNum = page.num;
                $.ajax({
                    url: "{:url('ajax/lists')}?page="+pageNum,
                    type:"POST",
                    data:{sort:'{$sort}',item_type:'{$type}',article_type:'{$article_type}'},
                    success: function(result) {
                        var curPageData = result.data.list || result.data.data;
                        var totalPage = result.data.total;
                        mescroll.endByPage(curPageData.length, totalPage)
                        if(pageNum == 1){
                            $('#ajaxResult').empty();
                        }

                        $('#ajaxResult').append(result.data.html);
                    },
                    error: function(e) {
                        //联网失败的回调,隐藏下拉刷新和上拉加载的状态
                        mescroll.endErr();
                    }
                });
            },
            page: {
                num: 0, //当前页 默认0,回调之前会加1; 即callback(page)会从1开始
                size: perPage //每页数据条数,默认10
            },
            htmlNodata: '<p class="nodata">-- 暂无更多数据 --</p>',
            noMoreSize: 5,
            toTop: {
                //回到顶部按钮
                src: "static/common/image/back_top.png", //图片路径,默认null,支持网络图
                offset: 1000 //列表滚动1000px才显示回到顶部按钮
            },
            empty: {
                //列表第一页无任何数据时,显示的空提示布局; 需配置warpId才显示
                warpId:	"ajaxPage", //父布局的id (1.3.5版本支持传入dom元素)
                icon: "static/common/image/no-data.png", //图标,默认null,支持网络图
                tip: "暂无相关数据~" //提示
            },
            lazyLoad: {
                use: true, // 是否开启懒加载,默认false
                attr: 'url' // 标签中网络图的属性名 : <img imgurl='网络图  src='占位图''/>
            }
        }
    });
</script>
{/block}
