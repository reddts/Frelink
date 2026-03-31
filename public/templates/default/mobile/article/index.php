{extend name="$theme_block" /}
{block name="style"}
<style>
    .aw-mobile-filter-bar {
        border-top: 1px solid #eef2f7;
        overflow: hidden;
    }
    .aw-mobile-filter-bar .articleFilterSwiper {
        width: 100%;
        overflow: hidden;
    }
    .aw-mobile-filter-bar .swiper-wrapper {
        display: inline-flex;
        align-items: center;
        min-width: max-content;
    }
    .aw-mobile-filter-bar .swiper-slide,
    .aw-mobile-filter-bar .nav-item {
        flex: 0 0 auto;
        height: auto;
        line-height: normal;
        margin-bottom: 0;
    }
    .aw-mobile-filter-bar .nav-tabs {
        min-height: auto;
        border-bottom: 0;
    }
    .aw-mobile-filter-bar .nav-link {
        display: inline-flex;
        align-items: center;
        padding: 0.45rem 0.85rem;
        border-radius: 999px;
        border: 1px solid #dbe7f3;
        color: #60758b;
        background: #fff;
        margin-right: 0.5rem;
        font-size: 0.78rem;
        white-space: nowrap;
    }
    .aw-mobile-filter-bar .nav-link.active {
        background: #1d4ed8;
        color: #fff;
        border-color: #1d4ed8;
    }
    .aw-mobile-filter-bar .nav-link.aw-mobile-filter-type.active {
        color: #1d4ed8;
        border-color: #bfdbfe;
    }
    .aw-mobile-filter-divider {
        display: inline-flex;
        align-items: center;
        margin-right: 0.5rem;
        color: #94a3b8;
        font-size: 0.72rem;
        white-space: nowrap;
    }
</style>
{/block}
{block name="main"}
<div class="aui-content mescroll" id="ajaxPage">
    <div class="bg-white">
        <div class="aw-mobile-article-hero px-3">
            <h1>{:L('知识内容')}</h1>
            <p>{:frelink_content_description($article_type)}</p>
        </div>
        {if $article_type=='all' && !empty($article_type_spotlights)}
        <div class="aw-mobile-spotlight-grid">
            {foreach $article_type_spotlights as $spotlight}
            <a class="aw-mobile-spotlight-card" href="{:url('article/index',['sort'=>'new','category_id'=>$category,'type'=>$spotlight['type']])}" data-pjax="pageMain">
                <span class="aw-mobile-spotlight-label">{$spotlight.label}</span>
                <p>{$spotlight.description}</p>
                <div class="aw-mobile-spotlight-meta">
                    <div>{:L('已发布 %s 篇',$spotlight['count'])}</div>
                    {if !empty($spotlight['latest'])}
                    <div>{:L('最近一篇：%s',$spotlight['latest']['title'])}</div>
                    {/if}
                </div>
                <span class="aw-mobile-spotlight-action">{if $spotlight['type']=='research'}{:L('去看综述')}{else/}{:L('去看观察')}{/if}</span>
            </a>
            {/foreach}
        </div>
        {/if}
        <!--热门用户、话题-->
        <!--<div class="hotUserTopic d-flex p-2">
            <div class="flex-fill mr-1">
                <a href="{:url('people/lists')}">
                    <img src="{$static_url}mobile/img/hot-user.png" style="width: 100%;display: inline-block" loading="lazy" decoding="async">
                </a>
            </div>
            <div class="flex-fill ml-1">
                <a href="{:url('topic/index')}">
                    <img src="{$static_url}mobile/img/hot-topic.png" style="width: 100%;display: inline-block" loading="lazy" decoding="async">
                </a>
            </div>
        </div>-->
    </div>

    <div id="wrapMain">
        {if $setting.enable_category=='Y'}
        {:widget('common/category',['type'=>'article','category'=>$category,'show_type'=>'list'])}
        {/if}

        <div class="px-2 py-2 bg-white aw-mobile-filter-bar">
            <div class="swiper-container articleFilterSwiper">
            <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block swiper-wrapper" style="flex-wrap: nowrap;">
                <li class="nav-item swiper-slide" data-type="new">
                    <a class="nav-link {if $sort=='new'}active{/if}" data-pjax="pageMain" href="{:url('article/index',['sort'=>'new','category_id'=>$category,'type'=>$article_type])}">{:L('更新')}</a>
                </li>
                <li class="nav-item swiper-slide" data-type="recommend">
                    <a class="nav-link {if $sort=='recommend'}active{/if}" data-pjax="pageMain" href="{:url('article/index',['sort'=>'recommend','category_id'=>$category,'type'=>$article_type])}">{:L('精选')}</a>
                </li>
                <li class="nav-item swiper-slide" data-type="hot">
                    <a class="nav-link {if $sort=='hot'}active{/if}" data-pjax="pageMain" href="{:url('article/index',['sort'=>'hot','category_id'=>$category,'type'=>$article_type])}">{:L('高关注')}</a>
                </li>
                <li class="swiper-slide aw-mobile-filter-divider">{:L('分类')}</li>
                {foreach $article_type_options as $typeKey => $label}
                <li class="nav-item swiper-slide">
                    <a class="nav-link aw-mobile-filter-type {if $article_type==$typeKey}active{/if}" data-pjax="pageMain" href="{:url('article/index',['sort'=>$sort,'category_id'=>$category,'type'=>$typeKey])}">{$label}</a>
                </li>
                {/foreach}
            </ul>
            </div>
        </div>

        <div class="aw-common-list" id="ajaxResult"></div>
    </div>
</div>

<script>
    new Swiper('.articleFilterSwiper', {
        speed: 600,
        grabCursor: true,
        slidesPerView: "auto",
        slidesPerGroup: 1
    });
    var MeScroll = AWS_MOBILE.api.MeScroll('ajaxPage','#ajaxResult',"{:url('ajax/lists')}",{sort:'{$sort}',item_type:'article',category_id:"{$category}",article_type:'{$article_type}'},perPage);
</script>
{/block}
