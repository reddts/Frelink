{extend name="$theme_block" /}
{block name="main"}
<div class="aui-content mescroll" id="ajaxPage">
    <div class="bg-white px-3 pt-3 pb-2 border-bottom">
        <div class="font-weight-bold font-18 text-dark">{:L('主题')}</div>
        <div class="text-muted font-9 mt-1">{:L('用主题把相关 FAQ、知识内容和长期讨论聚合在一起，先看变化，再沿着线索继续深入。')}</div>
        <div class="row no-gutters mt-3">
            <div class="col-4 pr-2">
                <a class="d-block border rounded bg-light px-2 py-2 text-dark" href="{:url('topic/index',['type'=>'new','pid'=>$pid])}" data-pjax="pageMain">
                    <div class="font-weight-bold font-11">{:L('最新话题')}</div>
                    <div class="text-muted font-8 mt-1">{:L('先看最近更新')}</div>
                </a>
            </div>
            <div class="col-4 px-1">
                <a class="d-block border rounded bg-light px-2 py-2 text-dark" href="{:url('topic/index',['type'=>'focus','pid'=>$pid])}" data-pjax="pageMain">
                    <div class="font-weight-bold font-11">{:L('关注最多')}</div>
                    <div class="text-muted font-8 mt-1">{:L('稳定长期入口')}</div>
                </a>
            </div>
            <div class="col-4 pl-2">
                <a class="d-block border rounded bg-light px-2 py-2 text-dark" href="{:url('topic/index',['type'=>'discuss','pid'=>$pid])}" data-pjax="pageMain">
                    <div class="font-weight-bold font-11">{:L('讨论最多')}</div>
                    <div class="text-muted font-8 mt-1">{:L('追踪变化分歧')}</div>
                </a>
            </div>
        </div>
    </div>
    <div class="swiper-container bg-white">
        <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block px-3 bg-white swiper-wrapper" style="flex-wrap: nowrap;">
            <li class="nav-item swiper-slide">
                <a class="nav-link {if !$type || $type=='new'}active{/if}" href="{:url('topic/index',['type'=>'new','pid'=>$pid])}" data-pjax="pageMain">{:L('最新')}</a>
            </li>
            <li class="nav-item swiper-slide">
                <a class="nav-link {if $type=='focus'}active{/if}" href="{:url('topic/index',['type'=>'focus','pid'=>$pid])}" data-pjax="pageMain">{:L('关注最多')}</a>
            </li>

            <li class="nav-item swiper-slide">
                <a class="nav-link {if $type=='discuss'}active{/if}" href="{:url('topic/index',['type'=>'discuss','pid'=>$pid])}" data-pjax="pageMain">{:L('讨论最多')}</a>
            </li>
        </ul>
    </div>
    <div class="bg-white py-2">
        <div class="swiper-container mx-3" style="margin: 0">
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
    </div>

    <div class="aw-common-list aw-topic-list" id="ajaxResult"></div>
</div>
<script>
    var MeScroll = AWS_MOBILE.api.MeScroll('ajaxPage','#ajaxResult',"{:url('ajax/get_topic_list')}",{type:'{$type}',pid:'{$pid}'},perPage);
</script>
{/block}
