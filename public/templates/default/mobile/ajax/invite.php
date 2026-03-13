{extend name="$theme_block" /}
{block name="main"}
<div style="min-height: 70vh">
    <div class="headerSearch bg-white">
        <form class="searchForm">
            <input type="text" class="searchInput" name="username" id="invite-users" placeholder="{:L('输入您想邀请的用户')}...">
            <label><i class="iconfont iconsearch1"></i></label>
        </form>
    </div>
    {if $invite_list}
    <div class="pt-3 mt-3">
        <dl class="bg-white overflow-auto mb-1">
            <dt class="float-left">{:L('已邀请用户')}:</dt>
            {volist name="$invite_list" id="v"}
            <dd class="float-left ml-2 mb-0">
                <a href="javascript:;">
                    <img src="{$v.avatar}" style="border-radius:50%;width: 18px;height: 18px;" title="{$v.nick_name}" alt="{$v.nick_name}"/>
                </a>
            </dd>
            {/volist}
        </dl>
    </div>
    {/if}

    <div id="inviteAjaxPage" class="mt-3">
        <div id="ajaxList"></div>
    </div>
</div>

<script>
    var selector = $('#invite-users');
    $(selector).bind('compositionstart', function (e) {
        e.target.composing = true
    })
    $(selector).bind('compositionend', function (e) {
        e.target.composing = false
        trigger(e.target, 'input')
    });
    function trigger(el, type) {
        var e = document.createEvent('HTMLEvents')
        e.initEvent(type, true, false)
        el.dispatchEvent(e)
    }

    $(selector).bind('input', function (e) {
        if (e.target.composing) {
            return
        }

        $('#ajaxList').empty();
        var name = $(this).val();
        var mescroll1 = new MeScroll('inviteAjaxPage', {
            down: {
                callback: function (){
                    mescroll1.resetUpScroll();
                }
            },
            up: {
                callback: function (page)
                {
                    var pageNum = page.num;
                    $.ajax({
                        url: "{:url('ajax/invite_users')}?page="+pageNum,
                        type:"POST",
                        data:{name:name,question_id:'{$question_id}'},
                        success: function(result) {
                            var curPageData = result.data.list || result.data.data;
                            var totalPage = result.data.total;
                            mescroll1.endByPage(curPageData.length, totalPage)
                            if(pageNum == 1){
                                $('#ajaxList').empty();
                            }

                            $('#ajaxList').append(result.data.html);
                        },
                        error: function(e) {
                            //联网失败的回调,隐藏下拉刷新和上拉加载的状态
                            mescroll1.endErr();
                        }
                    });
                },
                page: {
                    num: 0, //当前页 默认0,回调之前会加1; 即callback(page)会从1开始
                    size: perPage //每页数据条数,默认10
                },
                htmlNodata: '<p class="nodata">-- 暂无更多数据 --</p>',
                noMoreSize: 5,
                toTop: {
                    //回到顶部按钮
                    src: "static/common/image/back_top.png", //图片路径,默认null,支持网络图
                    offset: 1000 //列表滚动1000px才显示回到顶部按钮
                },
                empty: {
                    //列表第一页无任何数据时,显示的空提示布局; 需配置warpId才显示
                    warpId:	"ajaxPage", //父布局的id (1.3.5版本支持传入dom元素)
                    icon: "static/common/image/no-data.png", //图标,默认null,支持网络图
                    tip: "暂无相关数据~" //提示
                },
                lazyLoad: {
                    use: true, // 是否开启懒加载,默认false
                    attr: 'url' // 标签中网络图的属性名 : <img imgurl='网络图  src='占位图''/>
                }
            }
        });
    })

    //邀请回答按钮
    $(document).on('click', '.question-invite', function () {
        var that = $(this);
        var uid = that.data('uid');
        var isInvite = that.data('invite');
        var questionId = that.data('id');
        var url = baseUrl+"/ajax/save_question_invite?question_id="+questionId;
        AWS_MOBILE.api.post(url,{uid:uid,has_invite:isInvite},function (res) {
            if(res.data.invite)
            {
                that.addClass('active');
                that.removeClass('question-invite');
                that.data('invite',1);
                that.text('已邀请');
            }else{
                that.removeClass('active');
                that.data('invite',0);
                that.text('邀请回答');
            }
            AWS_MOBILE.api.error(res.msg);
        });
    })
</script>
{/block}