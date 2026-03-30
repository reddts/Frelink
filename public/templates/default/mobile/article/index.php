{extend name="$theme_block" /}
{block name="style"}
<style>
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
        .aw-mobile-article-hero {
            padding: 14px 12px 10px;
            background: linear-gradient(180deg, #fbfdff 0%, #fff 100%);
            border-bottom: 1px solid #eef2f7;
        }
        .aw-mobile-article-hero h1 {
            margin-bottom: 4px;
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }
        .aw-mobile-article-hero p {
            margin-bottom: 0;
            color: #64748b;
            font-size: 12px;
            line-height: 1.6;
        }
        .aw-mobile-article-note {
            margin: 0 12px 12px;
            padding: 10px 12px;
            border-radius: 12px;
            background: #f8fbff;
            border: 1px solid #e5edf6;
            color: #64748b;
            font-size: 11px;
            line-height: 1.6;
        }
        .aw-mobile-article-note a {
            color: #1d4ed8;
            font-weight: 700;
        }
        .aw-mobile-spotlight-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin: 0 12px 12px;
        }
        .aw-mobile-spotlight-card {
            display: block;
            padding: 11px;
            border-radius: 14px;
            background: linear-gradient(135deg, #f8fbff 0%, #eef6ff 100%);
            border: 1px solid #dbe7f3;
            color: #0f172a;
        }
        .aw-mobile-spotlight-label {
            display: inline-flex;
            margin-bottom: 8px;
            padding: 4px 8px;
            border-radius: 999px;
            background: rgba(29, 78, 216, 0.08);
            color: #1d4ed8;
            font-style: normal;
            font-size: 10px;
            font-weight: 700;
        }
        .aw-mobile-spotlight-card strong {
            display: block;
            margin-bottom: 4px;
            font-size: 14px;
        }
        .aw-mobile-spotlight-card p {
            margin: 0 0 6px;
            color: #64748b;
            font-size: 11px;
            line-height: 1.55;
        }
        .aw-mobile-spotlight-meta {
            color: #334155;
            font-size: 10px;
            line-height: 1.5;
        }
        .aw-mobile-spotlight-action {
            display: inline-block;
            margin-top: 6px;
            color: #1d4ed8;
            font-size: 11px;
            font-weight: 700;
        }
        @media (max-width: 360px) {
            .aw-mobile-spotlight-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <div class="bg-white">
        <div class="aw-mobile-article-hero px-3">
            <h1>{:frelink_article_type_label($article_type)}</h1>
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
        <div class="aw-mobile-article-note">
            {if $article_type=='all'}
            {:L('先从上面的主入口区分综述和观察；如果想沿同一主题继续追踪变化，再去')}
            <a href="{:url('feature/index')}" data-pjax="pageMain">{:L('观察专题')}</a>。
            {else/}
            {:L('这一页先按当前内容形态继续阅读；如果你想沿同一主题看长期变化，可转到')}
            <a href="{:url('feature/index')}" data-pjax="pageMain">{:L('观察专题')}</a>。
            {/if}
        </div>
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
