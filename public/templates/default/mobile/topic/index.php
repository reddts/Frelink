{extend name="$theme_block" /}
{block name="main"}
<div class="aui-content mescroll" id="ajaxPage">
    <style>
        .aw-mobile-cross-nav a {
            display: block;
            color: #6b7280;
        }
        .aw-mobile-cross-nav a.text-primary {
            color: #1d4ed8 !important;
        }
        .aw-mobile-topic-hero {
            padding: 14px 12px 10px;
            background: linear-gradient(180deg, #fbfdff 0%, #fff 100%);
            border-bottom: 1px solid #eef2f7;
        }
        .aw-mobile-topic-hero h1 {
            margin-bottom: 4px;
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }
        .aw-mobile-topic-hero p {
            margin-bottom: 0;
            color: #64748b;
            font-size: 12px;
            line-height: 1.6;
        }
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
        .aw-mobile-topic-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            padding: 8px 12px 12px;
        }
        .aw-mobile-topic-card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: 172px;
            padding: 12px;
            border-radius: 14px;
            background: #fff;
            border: 1px solid #e5edf6;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
        }
        .aw-mobile-topic-card-cover {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            object-fit: cover;
            background: #f8fafc;
            margin-bottom: 10px;
        }
        .aw-mobile-topic-card-title {
            display: -webkit-box;
            overflow: hidden;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            margin-bottom: 6px;
            color: #0f172a;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.5;
        }
        .aw-mobile-topic-card-desc {
            display: -webkit-box;
            overflow: hidden;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 3;
            color: #64748b;
            font-size: 11px;
            line-height: 1.6;
        }
        .aw-mobile-topic-card-meta {
            margin-top: auto;
            padding-top: 10px;
            color: #475569;
            font-size: 11px;
            line-height: 1.6;
        }
        .aw-mobile-topic-card-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-top: 8px;
            padding: 5px 10px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 11px;
            font-weight: 700;
        }
    </style>
    <div class="bg-white">
        <div class="d-flex p-3 border-bottom aw-mobile-cross-nav">
            <div class="flex-fill text-right">
                <a href="{:url('question/index')}" data-pjax="pageMain" class="font-weight-bold font-11">{:L('FAQ')}</a>
            </div>
            <div class="flex-fill text-center">
                <a href="{:url('article/index')}" data-pjax="pageMain" class="font-weight-bold font-11">{:L('知识内容')}</a>
            </div>
            <div class="flex-fill text-left">
                <a href="{:url('topic/index')}" data-pjax="pageMain" class="font-weight-bold font-11 text-primary">{:L('主题')}</a>
            </div>
        </div>
        <div class="aw-mobile-topic-hero px-3">
            <h1>{:L('主题')}</h1>
            <p>{:L('用主题把相关 FAQ、知识内容和长期讨论聚合在一起，先看变化，再沿着线索继续深入。')}</p>
        </div>
        <div class="px-3 pb-3">
            <div class="aw-mobile-lane-grid">
                <a class="aw-mobile-lane-card" href="{:url('topic/index',['type'=>'new'])}" data-pjax="pageMain">
                    <strong>{:L('最新话题')}</strong>
                    <span>{:L('先看最近更新的话题。')}</span>
                </a>
                <a class="aw-mobile-lane-card" href="{:url('topic/index',['type'=>'focus'])}" data-pjax="pageMain">
                    <strong>{:L('关注最多')}</strong>
                    <span>{:L('优先看稳定长期入口。')}</span>
                </a>
                <a class="aw-mobile-lane-card" href="{:url('topic/index',['type'=>'discuss'])}" data-pjax="pageMain">
                    <strong>{:L('讨论最多')}</strong>
                    <span>{:L('追踪变化与分歧聚集点。')}</span>
                </a>
            </div>
        </div>
    </div>

    <div class="swiper-container mt-1 bg-white">
        <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block px-2 bg-white swiper-wrapper" style="flex-wrap: nowrap;">
            <li class="nav-item swiper-slide">
                <a class="nav-link {if !$type || $type=='new'}active{/if}" href="{:url('topic/index',['type'=>'new'])}" data-pjax="pageMain">{:L('最新')}</a>
            </li>
            <li class="nav-item swiper-slide">
                <a class="nav-link {if $type=='focus'}active{/if}" href="{:url('topic/index',['type'=>'focus'])}" data-pjax="pageMain">{:L('关注最多')}</a>
            </li>
            <li class="nav-item swiper-slide">
                <a class="nav-link {if $type=='discuss'}active{/if}" href="{:url('topic/index',['type'=>'discuss'])}" data-pjax="pageMain">{:L('讨论最多')}</a>
            </li>
        </ul>
    </div>

    <div class="aw-common-list aw-mobile-topic-grid" id="ajaxResult"></div>
</div>
<script>
    var MeScroll = AWS_MOBILE.api.MeScroll('ajaxPage','#ajaxResult',"{:url('ajax/get_topic_list')}",{type:'{$type}',pid:'{$pid}'},perPage);
</script>
{/block}
