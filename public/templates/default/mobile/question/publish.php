{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="{$baseUrl}" class="text-muted" data-pjax="pageMain"><i class="fa fa-home font-11"></i></a></div>
    <div class="aui-header-title font-10">{:L('新建 FAQ 条目')}</div>
    <a class="aui-header-right font-10 saveQuestion">{:L('发布')}</a>
</header>
{/block}

{block name="main"}
<div class="main-container mt-1 bg-white">
    <div class="card border-0">
        <div class="card-body p-2">
            {if !empty($publish_insight)}
            <div class="border rounded px-3 py-2 mb-3" style="background:#f8fbff;border-color:#e4eef8 !important;">
                <div class="font-weight-bold mb-2">{:L('最近 %s 天可写方向',$publish_insight.window_days)}</div>
                {if !empty($publish_insight.top_keywords)}
                <div class="text-muted font-9 mb-2">{:L('最近有人在搜')}：
                    {volist name="publish_insight.top_keywords" id="v"}
                    <span class="mr-2 text-primary js-fill-title" data-title="{$v.keyword|htmlspecialchars}">{$v.keyword}</span>
                    {/volist}
                </div>
                {/if}
                {if !empty($publish_insight.title_ideas)}
                <div class="font-9">
                    {volist name="publish_insight.title_ideas" id="v"}
                    <div class="mb-2 text-primary js-fill-title" data-title="{$v.title|htmlspecialchars}">{$v.title}</div>
                    {/volist}
                </div>
                {/if}
            </div>
            {/if}
            <div class="border rounded px-3 py-2 mb-3" style="background:#fbfdff;border-color:#e6edf5 !important;">
                <div class="font-weight-bold mb-2">{:L('发布前检查')}</div>
                <ul class="text-muted font-9 pl-3 mb-0">
                    <li class="mb-2">{:L('用户能否在 3 秒内知道这篇和自己有关')}</li>
                    <li class="mb-2">{:L('用户能否在 30 秒内获得一个新判断')}</li>
                    <li class="mb-2">{:L('用户看完后是否愿意继续点下一篇')}</li>
                    <li>{:L('标题是否真实反映正文，不靠夸张承诺骗点击')}</li>
                </ul>
            </div>
            <form id="question_form"  method="post" action="{:url('question/publish')}" onsubmit="return false">
                {:token_field()}
                <input type="hidden" id="captcha">
                <input type="hidden" name="access_key" value="{$access_key}">
                <input type="hidden" name="id" value="{$question_info['id']|default=0}">
                <div class="border rounded px-3 py-2 mb-3" style="background:#fbfdff;border-color:#e6edf5 !important;">
                <div class="font-weight-bold mb-2">{:L('FAQ 条目建议')}</div>
                <div class="text-muted font-9">{:frelink_content_description('question')}</div>
                </div>
                <div class="form-group mb-3">
                    <input id="title" name="title" value="{$question_info.title|default=''}" class="aw-form-control" type="text" placeholder="{:L('输入 FAQ 标题')}">
                </div>
                <div class="form-group">
                    <select class="aw-form-control" name="category_id" title="{:L('请选择一项分类')}" required>
                        <option value="0">{:L('选择分类')}</option>
                        {volist name="category_list" id="v"}
                        <option value="{$v.id}" {if isset($question_info['category_id']) && $question_info['category_id']==$v['id']}selected {/if}>{$v.title}</option>
                        {if !empty($v.childs)}
                        {foreach $v.childs as $child}
                        <option value="{$child.id}" {if isset($question_info['category_id']) && $question_info['category_id']==$child['id']}selected {/if}>
                        &nbsp;&nbsp;&nbsp;&nbsp;|__{$child.title}
                        </option>
                        {/foreach}
                        {/if}
                        {/volist}
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label class="mb-3 w-100">
                        {:L('绑定主题')}
                        <a href="javascript:;" class="text-primary font-weight-bold aw-ajax-open float-right" data-url="{:url('topic/select',['item_type'=>'question','item_id'=>isset($question_info['id']) ? $question_info['id'] : 0])}">
                            <i class="icon-add"></i>{:L('添加话题')}</a>
                        {if(get_setting('topic_enable')=='Y')}<span class="font-9 text-primary">({:L('至少添加一个')})</span>{/if}
                    </label>
                    <div class="page-detail-topic swiper-container py-1">
                        <ul class="swiper-wrapper" id="awTopicList">
                            {if !empty($question_info['topics'])}
                            {volist name="question_info['topics']" id="v"}
                            <li class="swiper-slide"><a href="{:url('topic/detail',['id'=>$v['id']])}"><em class="tag">{$v.title}</em></a></li>
                            {/volist}
                            <input type="hidden" name="topics" value="{:implode(',',array_column($question_info['topics'],'id'))}">
                            {/if}
                        </ul>
                    </div>
                </div>
                {if !empty($help_chapter_options)}
                <div class="form-group mb-3">
                    <label class="mb-2">{:L('知识归档')}</label>
                    <div class="border rounded px-3 py-2" style="background:#fbfdff;border-color:#e6edf5 !important;">
                        <div class="text-muted font-9 mb-2">{:L('把这条 FAQ 直接归到知识章节，后续更容易做结构化整理。')}</div>
                        {if !empty($suggested_help_chapters)}
                        <div class="font-weight-bold font-9 mb-2">{:L('建议归档章节')}</div>
                        {volist name="suggested_help_chapters" id="v"}
                        <label class="d-block mb-2">
                            <input type="checkbox" name="help_chapter_ids[]" value="{$v.id}" {if !empty($v.selected)}checked{/if}> {$v.title}
                        </label>
                        {/volist}
                        {/if}
                        <div class="font-weight-bold font-9 mb-2">{:L('全部知识章节')}</div>
                        {volist name="help_chapter_options" id="v"}
                        {if empty($v.suggested)}
                        <label class="d-block mb-2">
                            <input type="checkbox" name="help_chapter_ids[]" value="{$v.id}" {if !empty($v.selected)}checked{/if}> {$v.title}
                        </label>
                        {/if}
                        {/volist}
                    </div>
                </div>
                {/if}

                <div class="form-group mb-3">
                    <label class="mb-3">{:L('FAQ 说明')}</label>
                    {:hook('editor',['name'=>'detail','cat'=>'question','value'=>isset($question_info['detail']) ? $question_info['detail'] : '','access_key'=>$access_key])}
                </div>

                <!--发布附加钩子-->
                {:hook('publish_extend',['info'=>$question_info,'page'=>'question'])}
                <!--发布附加钩子-->
                {if get_setting('auto_save_draft')=='Y'}
                <script>
                    //自动保存时间间隔
                    var AutoSaveTime = parseInt("{:get_setting('auto_save_draft_time')}")*360;
                    //设置自动保存
                    setInterval(function (item_type,itemId){
                        var formData = AWS_MOBILE.common.formToJSON('question_form');
                        $.ajax({
                            url:baseUrl + '/ajax/save_draft',
                            dataType: 'json',
                            type:'post',
                            data:{
                                data:formData,
                                item_id:'{$question_info.id|default=0}',
                                item_type:'question'
                            },
                            success: function (result)
                            {

                            },
                            error:  function (error) {
                                if ($.trim(error.responseText) !== '') {
                                    layer.closeAll();
                                    AWS_MOBILE.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                                }
                            }
                        });
                    }, AutoSaveTime);
                </script>
                {/if}
                <div class="mt-4 clearfix">
                    {if !isset($question_info['id']) && $setting.enable_anonymous=='Y'}
                    <label class="mb-0 text-muted mr-3">
                        <input value="1" name="is_anonymous" type="checkbox"  {$question_info.is_anonymous ? 'checked' : ''}> {:L('匿名发布')}
                    </label>
                    {/if}
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).on('click', '.js-fill-title', function () {
        let title = $(this).data('title');
        if (!title) {
            return;
        }
        $('#title').val(title).trigger('input').trigger('focus');
        window.scrollTo({top: 0, behavior: 'smooth'});
    });

    zxEditor.addFooterButton({
        name: 'save-draft',
        // 按钮外容器样式名称
        class: '',
        // 按钮内i元素样式名
        icon: 'iconfont icon-caogaoxiang',
        // 需要注册的监听事件名
        on: 'save-draft'
    })

    zxEditor.on('save-draft', function () {
        if (!parseInt(userId)) {
            AWS_MOBILE.User.login();
            return false;
        }

        var formData = AWS_MOBILE.common.formToJSON('question_form');

        $.ajax({
            url:baseUrl + '/ajax/save_draft',
            dataType: 'json',
            type:'post',
            data:{
                data:formData,
                item_id:'{$question_info.id|default=0}',
                item_type:'question'
            },
            success: function (result)
            {
                let msg = result.msg ? result.msg : '保存成功';
                if(result.code> 0)
                {
                    AWS_MOBILE.api.success(msg)
                }else{
                    AWS_MOBILE.api.error(msg)
                }
            },
            error:  function (error) {
                if ($.trim(error.responseText) !== '') {
                    AWS_MOBILE.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                }
            }
        });
    });
        $('.saveQuestion').click(function (){
        var that = this;
    {if $captcha_enable}
        $('#captcha').captcha({
        callback: function () {
        publishQuestion(that)
    }
    });
    {else/}
        publishQuestion(that)
    {/if}
    })

    function publishQuestion (){
        let form = $('#question_form');
        $('.saveQuestion').attr('disabled',true);
        $.ajax({
            url: form.attr('action'),
            dataType: 'json',
            type: 'post',
            data: form.serialize(),
            success: function (result) {
                if(result.code)
                {
                    AWS_MOBILE.api.success(result.msg, result.url);
                }else{
                    $('.saveQuestion').attr('disabled',false);
                    AWS_MOBILE.api.error(result.msg, result.url);
                }
            },
            error: function (error) {
                if ($.trim(error.responseText) !== '') {
                    AWS_MOBILE.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                }
            }
        })
    }
</script>
{/block}
{block name="footer"}{/block}
