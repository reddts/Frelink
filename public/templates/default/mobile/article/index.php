{extend name="$theme_block" /}
{block name="style"}
<style>
    .aw-mobile-cross-nav a {
        display: block;
        color: #6b7280;
    }
    .aw-mobile-cross-nav a.text-primary {
        color: #1d4ed8 !important;
    }
    .aw-mobile-type-tabs {
        border-top: 1px solid #eef2f7;
    }
    .aw-mobile-type-tabs .nav-link {
        padding: 0.45rem 0.85rem;
        border-radius: 999px;
        border: 1px solid #dbe7f3;
        color: #60758b;
        background: #fff;
        margin-right: 0.5rem;
        font-size: 0.78rem;
    }
    .aw-mobile-type-tabs .nav-link.active {
        background: #1d4ed8;
        color: #fff;
        border-color: #1d4ed8;
    }
</style>
{/block}
{block name="main"}
<div class="aui-content mescroll" id="ajaxPage">
    <style>
        .aw-mobile-lane-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
        }
        .aw-mobile-lane-card {
            display: block;
            padding: 10px;
            border-radius: 12px;
            background: #f8fbff;
            border: 1px solid #e5edf6;
            color: #0f172a;
        }
        .aw-mobile-lane-card strong {
            display: block;
            margin-bottom: 4px;
            font-size: 12px;
        }
        .aw-mobile-lane-card span {
            display: block;
            color: #64748b;
            font-size: 11px;
            line-height: 1.5;
        }
    </style>
    <div class="bg-white">
        <div class="d-flex p-3 border-bottom aw-mobile-cross-nav">
            <div class="flex-fill text-right">
                <a href="{:url('question/index')}" data-pjax="pageMain" class="font-weight-bold font-11">{:frelink_nav_label('问题')}</a>
            </div>
            <div class="flex-fill text-center">
                <a href="{:url('article/index')}" data-pjax="pageMain" class="font-weight-bold font-11 text-primary">{:frelink_nav_label('文章')}</a>
            </div>
            <div class="flex-fill text-left">
                <a href="{:url('topic/index')}" data-pjax="pageMain" class="font-weight-bold font-11">{:frelink_nav_label('主题')}</a>
            </div>
        </div>
        <div class="px-3 pb-3">
            <div class="font-weight-bold font-12 mb-1">{:frelink_article_type_label($article_type)}</div>
            <div class="text-muted font-9">{:frelink_content_description($article_type)}</div>
        </div>
        <div class="px-3 pb-3">
            <div class="aw-mobile-lane-grid">
                <a class="aw-mobile-lane-card" href="{:url('article/index',['sort'=>'new','category_id'=>$category,'type'=>$article_type])}" data-pjax="pageMain">
                    <strong>{:L('最新更新')}</strong>
                    <span>{:L('先看这一类最近补了什么。')}</span>
                </a>
                <a class="aw-mobile-lane-card" href="{:url('article/index',['sort'=>'hot','category_id'=>$category,'type'=>$article_type])}" data-pjax="pageMain">
                    <strong>{:L('高关注')}</strong>
                    <span>{:L('优先看被持续阅读的条目。')}</span>
                </a>
                <a class="aw-mobile-lane-card" href="{:url('topic/index')}" data-pjax="pageMain">
                    <strong>{:L('转到主题')}</strong>
                    <span>{:L('沿主题继续追踪资料脉络。')}</span>
                </a>
            </div>
        </div>
        <!--热门用户、话题-->
        <!--<div class="hotUserTopic d-flex p-2">
            <div class="flex-fill mr-1">
                <a href="{:url('people/lists')}">
                    <img src="{$static_url}mobile/img/hot-user.png" style="width: 100%;display: inline-block">
                </a>
            </div>
            <div class="flex-fill ml-1">
                <a href="{:url('topic/index')}">
                    <img src="{$static_url}mobile/img/hot-topic.png" style="width: 100%;display: inline-block">
                </a>
            </div>
        </div>-->
    </div>

    <div id="wrapMain">
        {if $setting.enable_category=='Y'}
        {:widget('common/category',['type'=>'article','category'=>$category,'show_type'=>'list'])}
        {/if}

        <div class="swiper-container mt-1 bg-white">
            <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block px-2 bg-white swiper-wrapper" style="flex-wrap: nowrap;">
                <li class="nav-item swiper-slide" data-type="new">
                    <a class="nav-link {if $sort=='new'}active{/if}" data-pjax="pageMain" href="{:url('article/index',['sort'=>'new','category_id'=>$category,'type'=>$article_type])}">{:L('更新')}</a>
                </li>
                <li class="nav-item swiper-slide" data-type="recommend">
                    <a class="nav-link {if $sort=='recommend'}active{/if}" data-pjax="pageMain" href="{:url('article/index',['sort'=>'recommend','category_id'=>$category,'type'=>$article_type])}">{:L('精选')}</a>
                </li>
                <li class="nav-item swiper-slide" data-type="hot">
                    <a class="nav-link {if $sort=='hot'}active{/if}" data-pjax="pageMain" href="{:url('article/index',['sort'=>'hot','category_id'=>$category,'type'=>$article_type])}">{:L('高关注')}</a>
                </li>
            </ul>
        </div>

        <div class="px-2 py-2 bg-white aw-mobile-type-tabs">
            <div class="swiper-container articleTypeSwiper">
                <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block swiper-wrapper" style="flex-wrap: nowrap;">
                    {foreach $article_type_options as $typeKey => $label}
                    <li class="nav-item swiper-slide">
                        <a class="nav-link {if $article_type==$typeKey}active{/if}" data-pjax="pageMain" href="{:url('article/index',['sort'=>$sort,'category_id'=>$category,'type'=>$typeKey])}">{$label}</a>
                    </li>
                    {/foreach}
                </ul>
            </div>
        </div>

        <div class="aw-common-list" id="ajaxResult"></div>
    </div>
</div>

<script>
    new Swiper('.articleTypeSwiper', {
        speed: 600,
        grabCursor: true,
        slidesPerView: "auto",
        slidesPerGroup: 1
    });
    var MeScroll = AWS_MOBILE.api.MeScroll('ajaxPage','#ajaxResult',"{:url('ajax/lists')}",{sort:'{$sort}',item_type:'article',category_id:"{$category}",article_type:'{$article_type}'},perPage);
</script>
{/block}
