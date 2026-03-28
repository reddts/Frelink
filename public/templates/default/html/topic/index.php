{extend name="$theme_block" /}
{block name="main"}
<style>
    .swiper-slide {width: auto!important}
    .nav-pills .nav-link {
        background: #eee;
        padding: 8px 14px;
        margin-right: 10px;
        font-size: 14px;
    }
    .aw-topic-hero {
        padding: 20px 24px 12px;
        border-bottom: 1px solid #eef2f7;
        background: linear-gradient(180deg, #fbfdff 0%, #fff 100%);
    }
    .aw-topic-hero h1 {
        margin: 0 0 8px;
        font-size: 24px;
        font-weight: 700;
        color: #0f172a;
    }
    .aw-topic-hero p {
        margin: 0;
        color: #64748b;
        line-height: 1.7;
    }
    .aw-topic-lanes {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        padding: 14px 24px 18px;
        border-bottom: 1px solid #eef2f7;
        background: #fff;
    }
    .aw-topic-lane {
        display: block;
        padding: 14px;
        border: 1px solid #e5edf6;
        border-radius: 14px;
        background: #fbfdff;
        color: #0f172a;
    }
    .aw-topic-lane:hover {
        text-decoration: none;
        transform: translateY(-1px);
        transition: all .2s ease;
    }
    .aw-topic-lane strong {
        display: block;
        margin-bottom: 6px;
        font-size: 15px;
    }
    .aw-topic-lane span {
        display: block;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
    }
    @media (max-width: 991.98px) {
        .aw-topic-lanes {
            grid-template-columns: 1fr;
        }
    }
</style>
<div class="container mt-2">
    <div class="row justify-content-between">
        <div class="aw-left radius col-md-9 bg-white mb-2">
            <div class="aw-topic-hero">
                <h1>{:L('主题')}</h1>
                <p>{:L('用主题把相关 FAQ、知识内容和长期讨论聚合在一起，先看变化，再沿着线索继续深入。')}</p>
            </div>
            <div class="aw-topic-lanes">
                <a class="aw-topic-lane" href="{:url('topic/index',['type'=>'new','pid'=>$pid])}" data-pjax="pageMain">
                    <strong>{:L('最新话题')}</strong>
                    <span>{:L('先看最近更新的主题，快速跟上当前讨论和内容补充。')}</span>
                </a>
                <a class="aw-topic-lane" href="{:url('topic/index',['type'=>'focus','pid'=>$pid])}" data-pjax="pageMain">
                    <strong>{:L('关注最多')}</strong>
                    <span>{:L('优先查看关注人数更高的主题，直接进入更稳定的长期入口。')}</span>
                </a>
                <a class="aw-topic-lane" href="{:url('topic/index',['type'=>'discuss','pid'=>$pid])}" data-pjax="pageMain">
                    <strong>{:L('讨论最多')}</strong>
                    <span>{:L('跟着讨论最活跃的主题继续追踪分歧、背景和后续变化。')}</span>
                </a>
            </div>
            <nav>
                <div class="nav nav-tabs pl-4 pr-4 aw-pjax-a" role="tablist">
                    <a class="nav-item nav-link {if !$type || $type=='new'}active{/if}" data-pjax="pageMain" href="{:url('topic/index',['type'=>'new','pid'=>$pid])}">
                        {:L('最新')} </a>
                    <a class="nav-item nav-link {if $type=='focus'}active{/if}" data-pjax="pageMain" href="{:url('topic/index',['type'=>'focus','pid'=>$pid])}" >
                        {:L('关注最多')} </a>
                    <a class="nav-item nav-link {if $type=='discuss'}active{/if}" data-pjax="pageMain" href="{:url('topic/index',['type'=>'discuss','pid'=>$pid])}">
                        {:L('讨论最多')} </a>
                </div>
            </nav>
            <div id="tabMain">
                <div class="swiper-container mx-3 my-3" style="margin: 0">
                    <ul class="nav nav-pills n-nav swiper-wrapper" style="flex-wrap: nowrap;">
                        <li class="nav-item swiper-slide">
                            <a class="nav-link mb-0 {if !$pid}active c-active{/if}" data-k="0" data-pjax="pageMain" href="{:url('topic/index',['type'=>$type,'pid'=>0])}">{:L('全部话题')}</a>
                        </li>
                        {foreach $parent_list as $k => $v}
                        <li class="nav-item swiper-slide">
                            <a class="nav-link {if $pid==$v.id} active c-active{/if}" data-k="{$k}" data-pjax="pageMain" href="{:url('topic/index',['type'=>$type,'pid'=>$v.id])}">{$v.title}</a>
                        </li>
                        {/foreach}
                    </ul>
                </div>

                <div class="aw-overflow-hidden rounded">
                    <div class="aw-mod aw-topic-list">
                        <div class="row">
                            {volist name="list" id="v"}
                            <div class="col-md-6 mb-3">
                                <div class="px-3 py-2 rounded topic-item">
                                    <dl class="position-relative">
                                        <dt>
                                            <a href="{:url('topic/detail',['id'=>$v['id']])}">
                                                <img src="{$v.pic|default='/static/common/image/topic.svg'}" class="rounded" alt="{$v.title|default='topic'}" width="64" height="64" loading="lazy" decoding="async" style="object-fit:cover;">
                                            </a>
                                        </dt>
                                        <dd class="info position-relative">
                                            <a href="{:url('topic/detail',['id'=>$v['id']])}" class="font-weight-bold">#{$v.title|raw}</a>
                                            <p class="mb-0 font-8 aw-one-line">{$v.description|raw}</p>
                                            <p class="font-8 text-muted position-absolute" style="bottom: 0">
                                                <span class="mr-3">{:L('正在讨论')}：{$v.discuss}</span>
                                                <span class="mr-3"><span class="aw-global-focus-count">{:L('关注人数')}：{$v.focus}</span></span>
                                                {if $user_id}
                                                <a href="javascript:;" class="cursor-pointer {$v['has_focus'] ? 'active ygz' : ''}" onclick="AWS.User.focus(this,'topic','{$v.id}')" >{$v['has_focus'] ? '<span><i class="icon-minus-circle text-danger"></i> '.L('已关注').'</span>' : '<span><i class="icon-plus-circle text-primary"></i> '.L('关注').'</span>'}</a>
                                                {/if}
                                            </p>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                            {/volist}
                        </div>
                    </div>
                </div>
                {$page|raw}
            </div>
        </div>
        <div class="aw-right radius col-md-3 px-xs-0">
            {:widget('sidebar/writeNav')}
            {:widget('sidebar/focusTopic',['uid'=>$user_id])}
            {:widget('sidebar/hotTopic',['uid'=>$user_id])}
        </div>
    </div>
</div>
<script>
    let c_a = $('.c-active'),
        k = parseInt(c_a.data('k'))
    let navSwiper = new Swiper('.swiper-container', {
        speed: 600,
        grabCursor: true,
        slidesPerView: "auto",
        initialSlide: k,
        slidesPerGroup: 3
    })
</script>
{/block}
