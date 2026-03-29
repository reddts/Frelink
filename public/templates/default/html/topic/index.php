{extend name="$theme_block" /}
{block name="main"}
<div class="container mt-2">
    <div class="row justify-content-between">
        <div class="aw-left radius col-md-9 bg-white mb-2">
            <div class="aw-topic-hero">
                <div class="aw-page-kicker">Topic Atlas</div>
                <h1>{:L('主题地图')}</h1>
                <p>{:L('用主题把相关 FAQ、知识内容和长期讨论聚合在一起，先看变化，再沿着线索继续深入。')}</p>
                <div class="aw-page-chips">
                    <span>{:L('知识聚合')}</span>
                    <span>{:L('高相关线索')}</span>
                    <span>{:L('讨论与内容并列')}</span>
                </div>
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
                    <div class="aw-mod aw-topic-list aw-topic-feed">
                        <div class="row">
                            {volist name="list" id="v"}
                            <div class="col-md-6 mb-3">
                                <div class="px-3 py-2 rounded topic-item aw-topic-card">
                                    <dl class="position-relative">
                                        <dt>
                                            <a href="{:url('topic/detail',['id'=>$v['id']])}">
                                                <img src="{$v.pic|default='/static/common/image/topic.svg'}" class="rounded" alt="{$v.title|default='topic'}" width="64" height="64" loading="lazy" decoding="async" style="object-fit:cover;">
                                            </a>
                                        </dt>
                                        <dd class="info position-relative">
                                            <a href="{:url('topic/detail',['id'=>$v['id']])}" class="font-weight-bold">#{$v.title|raw}</a>
                                            <p class="mb-0 font-8 aw-one-line">{$v.description|raw}</p>
                                            <div class="aw-topic-meta">
                                                <span class="aw-topic-meta-item">{:L('正在讨论')}：{$v.discuss}</span>
                                                <span class="aw-topic-meta-item"><span class="aw-global-focus-count">{:L('关注人数')}：{$v.focus}</span></span>
                                                {if $user_id}
                                                <a href="javascript:;" class="cursor-pointer {$v['has_focus'] ? 'active ygz' : ''}" onclick="AWS.User.focus(this,'topic','{$v.id}')" >{$v['has_focus'] ? '<span><i class="icon-minus-circle text-danger"></i> '.L('已关注').'</span>' : '<span><i class="icon-plus-circle text-primary"></i> '.L('关注').'</span>'}</a>
                                                {/if}
                                            </div>
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
