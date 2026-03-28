{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="{:url('feature/index')}" class="text-muted" data-pjax="pageMain"><i class="fa fa-angle-left font-14"></i></a></div>
    <div class="aui-header-title">{:L('观察专题')}</div>
    <div class="aui-header-right"><a href="{$baseUrl}" class="text-muted" data-pjax="pageMain"><i class="fa fa-home font-11"></i></a></div>
</header>
{/block}

{block name="main"}
<div class="main-container mescroll" id="ajaxPage">
    <style>
        .aw-mobile-feature-detail-hero {
            margin: 12px;
            padding: 18px 16px;
            border-radius: 22px;
            background:
                linear-gradient(180deg, rgba(3,7,18,.18) 0%, rgba(3,7,18,.52) 100%),
                url('{$info.image}') center center / cover no-repeat;
            color: #fff;
            box-shadow: 0 16px 38px rgba(15, 23, 42, 0.16);
        }
        .aw-mobile-feature-detail-hero h1 {
            margin: 0 0 10px;
            font-size: 24px;
            font-weight: 700;
            line-height: 1.35;
        }
        .aw-mobile-feature-detail-hero p {
            margin: 0;
            color: rgba(226, 232, 240, 0.9);
            font-size: 13px;
            line-height: 1.75;
        }
        .aw-mobile-feature-detail-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 14px;
        }
        .aw-mobile-feature-detail-meta span {
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
        .aw-mobile-feature-detail-note {
            margin: 0 12px 12px;
            padding: 14px;
            border-radius: 18px;
            background: #fff;
            color: #64748b;
            font-size: 12px;
            line-height: 1.8;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        }
        .aw-mobile-feature-topics {
            margin: 0 12px 12px;
            padding: 14px;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        }
        .aw-mobile-feature-topics strong {
            display: block;
            margin-bottom: 10px;
            color: #0f172a;
            font-size: 14px;
        }
        .aw-mobile-feature-topic-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .aw-mobile-feature-topic-tags a {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 11px;
            font-weight: 600;
        }
        .aw-mobile-feature-feed-note {
            padding: 10px 12px 0;
            color: #64748b;
            font-size: 12px;
            line-height: 1.7;
        }
        .aw-mobile-feature-type-tabs {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding: 10px 12px 0;
            white-space: nowrap;
        }
        .aw-mobile-feature-type-tabs a {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            border: 1px solid #dbe7f3;
            background: #fff;
            color: #60758b;
            font-size: 12px;
        }
        .aw-mobile-feature-type-tabs a.active {
            background: #1d4ed8;
            border-color: #1d4ed8;
            color: #fff;
        }
    </style>

    <section class="aw-mobile-feature-detail-hero">
        <h1>{$info.title}</h1>
        <p>{$info.description|raw}</p>
        <div class="aw-mobile-feature-detail-meta">
            <span>{:L('观察专题')}</span>
            <span>{:L('持续更新')}</span>
            <span>{:L('围绕主题')}</span>
        </div>
    </section>

    <div class="aw-mobile-feature-detail-note">
        {:L('这个观察专题会持续收录同一主题下的重要动态、代表内容和阶段判断。优先把高价值观察沉淀到这里，再逐步升级成综述或知识章节。')}
    </div>

    {if !empty($topics)}
    <div class="aw-mobile-feature-topics">
        <strong>{:L('相关主题')}</strong>
        <div class="aw-mobile-feature-topic-tags">
            {foreach $topics as $topic}
            <a href="{:url('topic/detail',['id'=>$topic['id']])}" data-pjax="pageMain">{$topic.title}</a>
            {/foreach}
        </div>
    </div>
    {/if}

    <div class="swiper-container">
        <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block px-2 bg-white swiper-wrapper" style="flex-wrap: nowrap;">
            <li class="nav-item swiper-slide">
                <a class="nav-link {if $sort=='new'}active{/if}" data-pjax="pageMain" href="{:url('feature/detail',['token'=>$info['url_token'],'sort'=>'new'])}">{:L('观察动态')}</a>
            </li>
            <li class="nav-item swiper-slide">
                <a class="nav-link {if $sort=='hot'}active{/if}" data-pjax="pageMain" href="{:url('feature/detail',['token'=>$info['url_token'],'sort'=>'hot'])}">{:L('热门内容')}</a>
            </li>
            <li class="nav-item swiper-slide">
                <a class="nav-link {if $sort=='best'}active{/if}" data-pjax="pageMain" href="{:url('feature/detail',['token'=>$info['url_token'],'sort'=>'best'])}">{:L('最佳回复')}</a>
            </li>
        </ul>
    </div>

    <div class="aw-mobile-feature-feed-note">
        <div class="aw-mobile-feature-type-tabs">
            <a class="{if $content_type=='all'}active{/if}" data-pjax="pageMain" href="{:url('feature/detail',['token'=>$info['url_token'],'sort'=>$sort,'content_type'=>'all'])}">{:frelink_content_label('all')}</a>
            <a class="{if $content_type=='question'}active{/if}" data-pjax="pageMain" href="{:url('feature/detail',['token'=>$info['url_token'],'sort'=>$sort,'content_type'=>'question'])}">{:frelink_content_label('question')}</a>
            <a class="{if $content_type=='research'}active{/if}" data-pjax="pageMain" href="{:url('feature/detail',['token'=>$info['url_token'],'sort'=>$sort,'content_type'=>'research'])}">{:frelink_content_label('research')}</a>
            <a class="{if $content_type=='fragment'}active{/if}" data-pjax="pageMain" href="{:url('feature/detail',['token'=>$info['url_token'],'sort'=>$sort,'content_type'=>'fragment'])}">{:frelink_content_label('fragment')}</a>
            <a class="{if $content_type=='faq'}active{/if}" data-pjax="pageMain" href="{:url('feature/detail',['token'=>$info['url_token'],'sort'=>$sort,'content_type'=>'faq'])}">{:frelink_content_label('faq')}</a>
        </div>
        {if $sort=='best' && !in_array($content_type,['all','question'])}
        {:L('最佳回复当前只适用于 FAQ 条目，切回 FAQ 或全部内容可查看代表性回答。')}
        {elseif $content_type=='question'}
        {:frelink_content_description('question')}
        {elseif $content_type=='research' /}
        {:frelink_content_description('research')}
        {elseif $content_type=='fragment' /}
        {:frelink_content_description('fragment')}
        {elseif $content_type=='faq' /}
        {:frelink_content_description('faq')}
        {elseif $sort=='new'}
        {:L('按时间查看这个观察专题里的持续更新和新近判断。')}
        {elseif $sort=='hot' /}
        {:L('优先查看在这个观察专题里更受关注、更容易引发后续讨论的内容。')}
        {else/}
        {:L('优先查看在这个观察专题里更具代表性、反馈质量更高的内容。')}
        {/if}
    </div>
    <div id="ajaxResult">
        {include file="ajax/lists" /}
    </div>
</div>

<script>
    var mescroll = new MeScroll("ajaxPage", {
        down: {
            callback: downCallback
        },
        up: {
            callback: upCallback,
            page: {
                num: 0,
                size: perPage
            },
            htmlNodata: '<p class="nodata">-- 暂无更多数据 --</p>',
            noMoreSize: 5,
            toTop: {
                src: "static/common/image/back_top.png",
                offset: 1000
            },
            empty: {
                warpId: "ajaxPage",
                icon: "static/common/image/no-data.png",
                tip: "暂无相关数据~"
            },
            lazyLoad: {
                use: true,
                attr: 'url'
            }
        }
    });

    function downCallback() {
        mescroll.resetUpScroll();
    }

    function upCallback(page) {
        var pageNum = page.num;
        $.ajax({
            url: baseUrl + '/ajax/lists?page=' + pageNum,
            type: "POST",
            data: {
                sort: '{$sort}',
                feature_id: '{$info.id}',
                content_type: '{$content_type}'
            },
            success: function (result) {
                var curPageData = result.data.list || [];
                var totalPage = result.data.total || 0;
                mescroll.endByPage(curPageData.length, totalPage);
                if (pageNum === 1) {
                    $('#ajaxResult').empty();
                }
                $('#ajaxResult').append(result.data.html);
            },
            error: function () {
                mescroll.endErr();
            }
        });
    }
</script>
{/block}
