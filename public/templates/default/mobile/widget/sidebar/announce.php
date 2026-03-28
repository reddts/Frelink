{if !empty($announce_list)}
<style>
    #ajaxPage .aui-noticebar {
        width: calc(100% - 24px);
        max-width: calc(100% - 24px);
        margin: 12px;
        box-sizing: border-box;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
    }
    #ajaxPage .aui-noticebar-main {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        overflow: hidden;
    }
    #ajaxPage .aui-noticebar-left {
        flex: 0 0 auto;
    }
    #ajaxPage .aui-noticebar-center {
        min-width: 0;
        flex: 1 1 auto;
    }
    #ajaxPage .aui-noticebar-texts,
    #ajaxPage .aui-noticebar-col .aui-noticebar-texts,
    #ajaxPage .announce-swiper-container,
    #ajaxPage .announce-swiper-container .swiper-wrapper,
    #ajaxPage .announce-swiper-container .swiper-slide {
        width: 100%;
        min-width: 0;
        max-width: 100%;
    }
    #ajaxPage .aui-noticebar-text {
        width: 100%;
        min-width: 0;
    }
</style>
<div class="aui-noticebar aui-noticebar-col mb-1">
    <div class="aui-noticebar-main  bg-white">
        <div class="aui-noticebar-left"><i class="iconfont text-danger font-weight-bold iconsystemprompt"></i></div>
        <div class="aui-noticebar-center">
            <div class="aui-noticebar-texts">
                <div class="announce-swiper-container">
                    <div class="swiper-wrapper">
                        {volist name="announce_list" id="v"}
                        <div class="swiper-slide">
                            <p class="aui-noticebar-text"><a data-title="{:L('最新公告')}" data-url="{:url('announce/detail',['id'=>$v.id])}" href="javascript:;" class="text-danger aw-ajax-open">{$v.title}</a></p>
                        </div>
                        {/volist}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    /*aui.announce({
        title: '{$announce_list[0]['title']}',
        msg: '{$announce_list[0]['message']|raw}'
    });*/
    var swiper = new Swiper('.announce-swiper-container', {

        autoplay: true,
        direction:'vertical',
        grabCursor:true,
        autoplayDisableOnInteraction:true,
        mousewheelControl:true,
        autoHeight:true,
        speed: 1000,
        loop: true
    });
</script>
{/if}
