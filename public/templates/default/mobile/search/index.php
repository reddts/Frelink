{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header mb-1">
    <div class="aui-header-title" style="left: 0.5rem">
        <div class="headerSearch">
            <form class="searchForm" id="searchForm" method="get" action="{:url('search/index')}">
                <input type="text" class="searchInput" name="q" value="{$keywords}" placeholder="{:L('输入您想搜索的内容')}...">
                <label><i class="iconfont iconsearch1" onclick="$('#searchForm').submit()"></i></label>
            </form>
        </div>
    </div>
    <a class="aui-header-right text-muted" href="javascript:;" onclick="javascript :history.back();">{:L('取消')}</a>
</header>
{/block}
{block name="main"}
<style>
    .aw-mobile-search-page {
        min-height: calc(100vh - 3rem);
        padding: 0 0 1rem;
        background:
            radial-gradient(circle at top left, rgba(15, 118, 110, 0.10), transparent 28%),
            linear-gradient(180deg, #eef4f7 0%, #f7fafc 34%, #f7fafc 100%);
    }

    .aw-mobile-search-hero {
        position: relative;
        overflow: hidden;
        margin: 0.75rem;
        padding: 1rem 1rem 0.95rem;
        border-radius: 1rem;
        background: linear-gradient(135deg, #111827 0%, #1d4ed8 56%, #0f766e 100%);
        color: #fff;
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.14);
    }

    .aw-mobile-search-hero::after {
        content: "";
        position: absolute;
        right: -2.8rem;
        bottom: -3.2rem;
        width: 8rem;
        height: 8rem;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.18) 0%, rgba(255, 255, 255, 0.02) 52%, transparent 74%);
        pointer-events: none;
    }

    .aw-mobile-search-kicker {
        position: relative;
        z-index: 1;
        display: inline-flex;
        align-items: center;
        margin-bottom: 0.5rem;
        padding: 0.22rem 0.55rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.16);
        border: 1px solid rgba(255, 255, 255, 0.18);
        color: rgba(255, 255, 255, 0.92);
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .aw-mobile-search-hero h1 {
        position: relative;
        z-index: 1;
        margin-bottom: 0.45rem;
        color: #fff;
        font-size: 1.15rem;
        font-weight: 800;
        line-height: 1.4;
    }

    .aw-mobile-search-hero p {
        position: relative;
        z-index: 1;
        margin-bottom: 0;
        color: rgba(255, 255, 255, 0.84);
        font-size: 0.78rem;
        line-height: 1.7;
    }

    .aw-mobile-search-chips {
        position: relative;
        z-index: 1;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.75rem;
    }

    .aw-mobile-search-chips span {
        display: inline-flex;
        align-items: center;
        padding: 0.32rem 0.6rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.18);
        color: rgba(255, 255, 255, 0.88);
        font-size: 0.72rem;
    }

    .aw-mobile-search-panel,
    .aw-mobile-search-empty,
    .aw-mobile-search-summary {
        margin: 0 0.75rem 0.75rem;
        padding: 0.95rem 1rem;
        border-radius: 1rem;
        border: 1px solid #d9e4ec;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    }

    .aw-mobile-search-panel h3,
    .aw-mobile-search-empty h3,
    .aw-mobile-search-summary h3 {
        margin-bottom: 0.45rem;
        color: #0f172a;
        font-size: 0.96rem;
        font-weight: 800;
    }

    .aw-mobile-search-panel p,
    .aw-mobile-search-empty p,
    .aw-mobile-search-summary p {
        margin-bottom: 0.45rem;
        color: #64748b;
        font-size: 0.76rem;
        line-height: 1.7;
    }

    .aw-mobile-search-taglist {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.7rem;
    }

    .aw-mobile-search-tag {
        display: inline-flex;
        align-items: center;
        padding: 0.38rem 0.72rem;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 0.76rem;
        font-weight: 600;
        box-shadow: inset 0 0 0 1px #dbeafe;
    }

    .aw-mobile-search-tag:hover,
    .aw-mobile-search-tag:focus {
        color: #0f172a;
        text-decoration: none;
    }

    .aw-mobile-search-tabs {
        margin: 0 0.75rem;
        padding: 0.7rem 0.75rem 0;
        border-radius: 1rem 1rem 0 0;
        background: #fff;
        border: 1px solid #d9e4ec;
        border-bottom: 0;
    }

    .aw-mobile-search-tabs .nav-link {
        margin-right: 0.45rem;
        margin-bottom: 0.6rem;
        border: 1px solid #d9e4ec;
        border-radius: 999px;
        background: #f3f7fa;
        color: #334155;
        font-weight: 700;
        padding: 0.52rem 0.85rem;
        font-size: 0.76rem;
    }

    .aw-mobile-search-tabs .nav-link.active {
        background: linear-gradient(135deg, #1d4ed8 0%, #0f766e 100%);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 10px 18px rgba(29, 78, 216, 0.18);
    }

    .aw-mobile-search-results {
        margin: 0 0.75rem 0.75rem;
        padding: 0.8rem 0.75rem 0.2rem;
        border: 1px solid #d9e4ec;
        border-top: 0;
        border-radius: 0 0 1rem 1rem;
        background: #fff;
        min-height: 8rem;
    }

    .aw-mobile-search-results .aw-mobile-search-feed {
        margin: 0;
    }

    .aw-mobile-search-results .aw-mobile-search-card,
    .aw-mobile-search-results .aw-mobile-search-user-card,
    .aw-mobile-search-results .aw-mobile-search-topic-card,
    .aw-mobile-search-results .aw-mobile-search-feed > dl {
        margin-bottom: 0.8rem;
        border-radius: 1rem;
        border: 1px solid #d9e4ec;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .aw-mobile-search-results .aw-mobile-search-card .aui-card-image,
    .aw-mobile-search-results .aw-mobile-search-user-card,
    .aw-mobile-search-results .aw-mobile-search-feed > dl {
        background: transparent;
    }

    .aw-mobile-search-results .aw-mobile-search-card .aui-card-main,
    .aw-mobile-search-results .aw-mobile-search-user-card .aui-card-main {
        padding-bottom: 0 !important;
    }

    .aw-mobile-search-results .aw-mobile-search-card .aui-card-title,
    .aw-mobile-search-results .aw-mobile-search-card .img,
    .aw-mobile-search-results .aw-mobile-search-card .desc,
    .aw-mobile-search-results .aw-mobile-search-user-card .aui-card-main,
    .aw-mobile-search-results .aw-mobile-search-topic-card {
        background: transparent !important;
    }

    .aw-mobile-search-results .aw-mobile-search-card .img-mask a,
    .aw-mobile-search-results .aw-mobile-search-feed .n-title a,
    .aw-mobile-search-results .aw-mobile-search-topic-card .font-weight-bold {
        color: #0f172a;
    }

    .aw-mobile-search-results .aw-mobile-search-card .img-mask a:hover,
    .aw-mobile-search-results .aw-mobile-search-feed .n-title a:hover,
    .aw-mobile-search-results .aw-mobile-search-topic-card .font-weight-bold:hover {
        color: #1d4ed8;
        text-decoration: none;
    }

    .aw-mobile-search-results .aw-mobile-search-card .aui-card-down,
    .aw-mobile-search-results .aw-mobile-search-feed > dl > dd:last-child {
        border-top-color: #e5edf4 !important;
        background: rgba(248, 250, 252, 0.72);
    }

    .aw-mobile-search-empty ul {
        margin: 0.6rem 0 0;
        padding-left: 1rem;
        color: #64748b;
        font-size: 0.75rem;
        line-height: 1.7;
    }

    .aw-mobile-search-empty ul li {
        margin-bottom: 0.35rem;
    }

    .aw-mobile-search-empty-cta {
        margin-top: 0.8rem;
    }
</style>

<div class="main-container mescroll aw-mobile-search-page" id="ajaxPage">
    {if !$keywords}
    <section class="aw-mobile-search-hero">
        <div class="aw-mobile-search-kicker">Knowledge Search</div>
        <h1>{:L('搜索 FAQ、综述、主题与知识条目')}</h1>
        <p>{:L('输入问题、概念、主题或作者名后，结果会按内容类型整理，减少空白页和找不到入口的情况。')}</p>
        <div class="aw-mobile-search-chips">
            <span>{:L('支持 FAQ / 文章 / 主题 / 用户')}</span>
            <span>{:L('优先返回可复用内容')}</span>
        </div>
    </section>
    <section class="aw-mobile-search-panel">
        <h3>{:L('还没开始搜索')}</h3>
        <p>{:L('可以直接输入你想解决的问题，或者从热门搜索里挑一个主题继续查看。')}</p>
        <div class="aw-mobile-search-taglist">
            <a href="{:url('search/index',['q'=>L('FAQ')])}" class="aw-mobile-search-tag">{:L('FAQ')}</a>
            <a href="{:url('search/index',['q'=>L('主题')])}" class="aw-mobile-search-tag">{:L('主题')}</a>
            <a href="{:url('search/index',['q'=>L('综述')])}" class="aw-mobile-search-tag">{:L('综述')}</a>
        </div>
    </section>
    {if $search_list}
    <section class="aw-mobile-search-panel">
        <h3>{:L('热门搜索')}</h3>
        <p>{:L('从近期高频主题开始，通常更容易找到成型内容。')}</p>
        <div class="aw-mobile-search-taglist">
            {foreach $search_list as $key=>$v}
            <a href="{:url('search/index',['q'=>$v['keyword']])}" class="aw-mobile-search-tag">{$v.keyword}</a>
            {/foreach}
        </div>
    </section>
    {/if}
    {else/}
    <section class="aw-mobile-search-hero">
        <div class="aw-mobile-search-kicker">Search Result</div>
        <h1>{:L('“%s” 的搜索结果', $keywords)}</h1>
        <p>{:L('结果会按知识类型拆分整理。继续切换标签，可以更快定位到 FAQ、知识内容或主题入口。')}</p>
        <div class="aw-mobile-search-chips">
            <span>{:L('关键词：%s', $keywords)}</span>
            <span>{:L('持续支持下拉刷新与分页加载')}</span>
        </div>
    </section>
    <section class="aw-mobile-search-summary">
        <h3>{:L('结果概览')}</h3>
        <p>{:L('当前结果正在按类型加载。如果没有结果，页面会直接给出改写建议和热门搜索，不再留空。')}</p>
    </section>
    <div id="SearchResultMain">
    <div class="swiper-container aw-mobile-search-tabs">
        <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block swiper-wrapper" style="flex-wrap: nowrap;">
            <li class="nav-item swiper-slide"><a class="nav-link {if $type=='all' || !$type}active{/if}" data-pjax="SearchResultMain" href="{:url('search/index',['q'=>$keywords,'sort'=>$sort,'type'=>'all'])}">{:L('综合')}</a></li>
            {volist name="tab_list" id="v"}
            <li class="nav-item swiper-slide"><a class="nav-link {if $type==$v.name}active{/if}" data-pjax="SearchResultMain" href="{:url('search/index',['q'=>$keywords,'sort'=>$sort,'type'=>$v.name])}">{$v.title}</a></li>
            {/volist}
        </ul>
    </div>

    <div id="ajaxResult" class="aw-mobile-search-results"></div>

    <script>
        function renderSearchEmptyState(keyword) {
            var hotLinks = '';
            {if $search_list}
            hotLinks += '<div class="aw-mobile-search-taglist">';
            {foreach $search_list as $key=>$v}
            hotLinks += '<a class="aw-mobile-search-tag" href="{:url('search/index',['q'=>$v['keyword']])}">{$v.keyword}</a>';
            {/foreach}
            hotLinks += '</div>';
            {/if}

            return '' +
                '<section class="aw-mobile-search-empty">' +
                    '<h3>{:L('没有找到直接匹配的内容')}</h3>' +
                    '<p>{:L('当前没有找到和关键词直接匹配的结果。你可以尝试更短的主题词、别名，或者换一个更常见的表达方式。')}</p>' +
                    '<ul>' +
                        '<li>{:L('检查是否有错别字或多余符号。')}</li>' +
                        '<li>{:L('换一个更短、更常见的关键词。')}</li>' +
                        '<li>{:L('先搜主题，再从主题页继续找 FAQ 或知识内容。')}</li>' +
                    '</ul>' +
                    '<div class="aw-mobile-search-empty-cta">' +
                        '<p>{:L('当前关键词')}：' + keyword + '</p>' +
                        hotLinks +
                    '</div>' +
                '</section>';
        }

        var mescroll = new MeScroll("ajaxPage", {
            down: {
                callback: downCallback
            },
            up: {
                callback: upCallback,
                page: {
                    num: 0,
                    size: 15
                },
                htmlNodata: '<p class="nodata">-- 暂无更多数据 --</p>',
                noMoreSize: 5,
                toTop: {
                    src: "static/common/image/back_top.png",
                    offset: 1000
                },
                lazyLoad: {
                    use: true,
                    attr: 'url'
                }
            }
        });
        function downCallback() {
            mescroll.resetUpScroll()
        }
        function upCallback(page) {
            var pageNum = page.num;
            $.ajax({
                url: baseUrl+'/search/ajax_search?page=' + pageNum,
                type:"POST",
                data:{sort:'{$sort}',type:'{$type}',q:'{$keywords}'},
                success: function(result) {
                    var curPageData = result.data.list || [];
                    var totalPage = parseInt(result.data.total || 0, 10);
                    if(pageNum == 1){
                        $('#ajaxResult').empty();
                    }
                    if(pageNum === 1 && !curPageData.length){
                        $('#ajaxResult').html(renderSearchEmptyState('{$keywords|htmlspecialchars}'));
                    } else if (result.data.html) {
                        $('#ajaxResult').append(result.data.html);
                    }
                    mescroll.endByPage(curPageData.length, totalPage);
                },
                error: function(e) {
                    mescroll.endErr();
                }
            });
        }
    </script>
    </div>
    {/if}
</div>
{/block}

{block name="footer"}{/block}
