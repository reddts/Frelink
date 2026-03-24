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
            background: rgba(7, 16, 31, 0.56);
            border: 1px solid rgba(255,255,255,0.1);
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
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
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
        .aw-mobile-secondary {
            margin: 12px;
        }
        .aw-mobile-lane-list a {
            display: block;
            padding: 10px 0;
            border-bottom: 1px solid #eef2f7;
            color: #0f172a;
        }
        .aw-mobile-lane-list a:last-child {
            border-bottom: 0;
            padding-bottom: 0;
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
        .aw-mobile-secondary summary {
            color: #64748b;
            font-size: 13px;
            cursor: pointer;
            list-style: none;
        }
        .aw-mobile-secondary summary::-webkit-details-marker {
            display: none;
        }
        .aw-mobile-feed-filter {
            display: flex;
            gap: 8px;
            padding: 0 12px 12px;
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
        .aw-mobile-feed-note {
            padding: 0 12px 12px;
            color: #64748b;
            font-size: 12px;
            line-height: 1.7;
        }
        .aw-mobile-feed-head {
            padding: 0 12px 10px;
        }
        .aw-mobile-feed-head h4 {
            margin: 0 0 4px;
            color: #0f172a;
            font-size: 15px;
            font-weight: 700;
        }
        .aw-mobile-feed-head p {
            margin: 0;
            color: #64748b;
            font-size: 12px;
            line-height: 1.6;
        }
    </style>
    <section class="aw-mobile-hero">
        <div class="aw-mobile-hero-title">{:L('公开、开放、可检索的知识系统')}</div>
        <div class="aw-mobile-hero-desc">{:L('用综述沉淀脉络，用主题追踪变化，用观察保留判断，用 FAQ 承接检索，不再依赖问答社区逻辑。')}</div>
        <form class="aw-mobile-search-form" action="{:url('search/index')}" method="get">
            <input class="aw-mobile-search-input" type="search" name="q" placeholder="{:L('搜索综述、观察、FAQ、主题或帮助')}">
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
                <strong>{:L('观察')}</strong>
                <span>{:L('进入长期观察专题，持续查看同一主题下的动态、判断和沉淀')}</span>
            </a>
            <a class="aw-mobile-quick-card" href="{:url('question/index')}">
                <strong>{:L('FAQ')}</strong>
                <span>{:L('高频问题、术语解释和可快速复用的明确答案')}</span>
            </a>
            <a class="aw-mobile-quick-card" href="{:url('help/index')}">
                <strong>{:L('帮助')}</strong>
                <span>{:L('适合查看术语、规则和入门说明')}</span>
            </a>
        </div>
        <div class="aw-mobile-hero-badges">
            <span class="aw-mobile-hero-badge">{:L('开放检索')}</span>
            <span class="aw-mobile-hero-badge">{:L('长期归档')}</span>
            <span class="aw-mobile-hero-badge">{:L('保留判断')}</span>
        </div>
        <div class="aw-mobile-hero-panel">
            <strong>{:L('让真正有价值的思想被看见')}</strong>
            <p>{:L('不做博眼球的碎片流，把问题、观察、综述和知识章节组织成可以持续追踪的公开知识系统。')}</p>
        </div>
    </section>
    {if !empty($search_keywords)}
    <section class="aw-mobile-section">
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
            <a href="{:url('help/detail',['token'=>$chapter['url_token']])}" data-pjax="pageMain">
                <small>{:L('归档章节')} · {$chapter.relation_count|default=0} {:L('条内容')}</small>
                <strong>{$chapter.title}</strong>
                <p>{if !empty($chapter['chapters'][0]['info']['title'])}{$chapter['chapters'][0]['info']['title']}{else/}{:str_cut(strip_tags((string)$chapter['description']),0,60)}{/if}</p>
            </a>
            {/volist}
        </div>
    </section>
    {/if}
    <section class="aw-mobile-section">
        <div class="aw-mobile-section-title">{:L('内容结构')}</div>
        <div class="aw-mobile-lane-list">
            <a href="{:url('article/index',['type'=>'research'])}" data-pjax="pageMain">
                <small>{:L('综述')}</small>
                <strong>{:L('先看脉络、分歧和阶段结论')}</strong>
                <p>{:L('适合做长期沉淀和系统理解。')}</p>
            </a>
            <a href="{:url('feature/index')}" data-pjax="pageMain">
                <small>{:L('观察')}</small>
                <strong>{:L('先看长期观察专题和阶段判断')}</strong>
                <p>{:L('适合围绕同一主题持续追踪变化、判断和后续沉淀。')}</p>
            </a>
            <a href="{:url('question/index')}" data-pjax="pageMain">
                <small>{:L('FAQ')}</small>
                <strong>{:L('先看高频问题和明确答案')}</strong>
                <p>{:L('适合快速检索、复用和补齐站内知识缺口。')}</p>
            </a>
        </div>
    </section>
    {:widget('sidebar/announce')}
    <details class="aw-mobile-secondary">
        <summary>{:L('展开次级入口')}</summary>
        <!--热门用户、话题-->
    {if get_theme_setting('mobile.index_hot_banner_enable','Y')=='Y'}
    <div class="hotUserTopic d-flex p-2">
        <div class="flex-fill mr-1">
            <a href="{:get_theme_setting('mobile.index_hot_user_url',(string)url('people/lists'))}">
                <img src="{:get_theme_setting('mobile.index_hot_user_image',$static_url.'mobile/img/hot-user.png')}" style="width: 100%;display: inline-block" loading="lazy" decoding="async">
            </a>
        </div>
        <div class="flex-fill ml-1">
            <a href="{:get_theme_setting('mobile.index_hot_topic_url',(string)url('topic/index'))}">
                <img src="{:get_theme_setting('mobile.index_hot_topic_image',$static_url.'mobile/img/hot-topic.png')}" style="width: 100%;display: inline-block" loading="lazy" decoding="async">
            </a>
        </div>
    </div>
    {/if}

    {:widget('sidebar/hotTopic',['uid'=>$user_id])}
    </details>
    <div class="aw-mobile-feed-head">
        <h4>{:L('持续更新')}</h4>
        <p>{:L('这里保留会持续滚动更新的知识流，用来补充综述、观察、FAQ 和帮助内容。')}</p>
    </div>
    <div class="aw-mobile-feed-filter">
        <a class="{if !$type || $current_sort=='unresponsive'}active{/if}" data-pjax="pageMain" href="{:url('index/index',['sort'=>$current_sort])}">{:L('全部内容')}</a>
        <a class="{if $type=='question'}active{/if}" data-pjax="pageMain" href="{:url('index/index',['sort'=>$current_sort,'type'=>'question'])}">{:L('FAQ')}</a>
        {if $current_sort!='unresponsive'}
        <a class="{if $type=='article' && $article_type=='research'}active{/if}" data-pjax="pageMain" href="{:url('index/index',['sort'=>$current_sort,'type'=>'article','article_type'=>'research'])}">{:L('综述')}</a>
        <a class="{if $type=='article' && $article_type=='fragment'}active{/if}" data-pjax="pageMain" href="{:url('index/index',['sort'=>$current_sort,'type'=>'article','article_type'=>'fragment'])}">{:L('观察')}</a>
        <a class="{if $type=='article' && $article_type=='faq'}active{/if}" data-pjax="pageMain" href="{:url('index/index',['sort'=>$current_sort,'type'=>'article','article_type'=>'faq'])}">{:L('帮助')}</a>
        {/if}
    </div>
    <div class="aw-mobile-feed-note">
        {if $current_sort=='unresponsive'}
        {:L('当前只看仍待补充的 FAQ 条目，方便快速找到仍缺答案的内容。')}
        {elseif $type=='question'}
        {:L('当前只看 FAQ 条目，优先承接检索和高频问题。')}
        {elseif $type=='article' && $article_type=='research'/}
        {:L('当前只看综述，适合系统理解和长期跟踪。')}
        {elseif $type=='article' && $article_type=='fragment'/}
        {:L('当前只看观察，适合快速判断和思考现场。')}
        {elseif $type=='article' && $article_type=='faq'/}
        {:L('当前只看帮助内容，适合快速检索和直接取用。')}
        {else/}
        {:L('首页会混排综述、观察、FAQ 和帮助条目，帮助用户先找到合适的知识入口。')}
        {/if}
    </div>

    <div class="swiper-container">
        <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block px-2 bg-white swiper-wrapper" style="flex-wrap: nowrap;">
            {if $user_id}
            <li class="nav-item swiper-slide">
                <a class="nav-link {if $current_sort=='focus'}active{/if}" data-pjax="pageMain" href="{:url('index/index',['sort'=>'focus'])}">{:L('关注主题')}</a>
            </li>
            {/if}
            <li class="nav-item swiper-slide" data-type="new">
        <a class="nav-link {if $current_sort=='new'}active{/if}" data-pjax="pageMain" href="{:url('index/index',array_merge(['sort'=>'new'],$feed_query))}">{:L('更新')}</a>
            </li>
            <li class="nav-item swiper-slide" data-type="recommend">
        <a class="nav-link {if $current_sort=='recommend'}active{/if}" data-pjax="pageMain" href="{:url('index/index',array_merge(['sort'=>'recommend'],$feed_query))}">{:L('精选')}</a>
            </li>
            <li class="nav-item swiper-slide" data-type="hot">
        <a class="nav-link {if $current_sort=='hot'}active{/if}" data-pjax="pageMain" href="{:url('index/index',array_merge(['sort'=>'hot'],$feed_query))}">{:L('高关注')}</a>
            </li>
            <li class="nav-item swiper-slide" data-type="unresponsive">
                <a class="nav-link {if $current_sort=='unresponsive'}active{/if}" data-pjax="pageMain" href="{:url('index/index',['sort'=>'unresponsive'])}" >{:L('待补充 FAQ')}</a>
            </li>
        </ul>
    </div>
    <div class="aw-common-list" id="ajaxResult"></div>
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
