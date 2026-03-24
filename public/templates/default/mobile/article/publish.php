{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="{$baseUrl}" class="text-muted" data-pjax="pageMain"><i class="fa fa-home font-11"></i></a></div>
    <div class="aui-header-title font-10">{:L('新建知识内容')}</div>
    <a class="aui-header-right font-10 saveArticle">{:L('发布')}</a>
</header>
{/block}

{block name="main"}
<div class="main-container mt-1 bg-white" style="padding-bottom: 50px">
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
                    <div class="mb-2 text-primary js-apply-idea" data-title="{$v.title|htmlspecialchars}" data-type="{$v.recommended_type|default=''}">
                        {$v.title}
                        {if !empty($v.recommended_type_label)}
                        <div class="text-muted font-8 mt-1">{:L('推荐形态')}：{$v.recommended_type_label}</div>
                        {/if}
                    </div>
                    {/volist}
                </div>
                {/if}
                {if !empty($publish_insight.type_ideas)}
                <div class="font-9 mt-2">
                    <div class="font-weight-bold mb-2">{:L('建议内容形态')}</div>
                    {volist name="publish_insight.type_ideas" id="v"}
                    <div class="mb-2 text-primary js-apply-type" data-type="{$v.type}" data-title="{$v.title|htmlspecialchars}">{$v.label} · {$v.keyword}</div>
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
            <form id="question_form"  method="post" action="{:url('article/publish')}" onsubmit="return false">
                {:token_field()}
                <input type="hidden" name="id" value="{$article_info.id|default=0}">
                <input type="hidden" name="wait_time">
                <input type="hidden" name="access_key" value="{$access_key}">
                <div class="border rounded px-3 py-2 mb-3" style="background:#fbfdff;border-color:#e6edf5 !important;">
                    <div class="font-weight-bold mb-2">{:L('内容类型建议')}</div>
                    <div class="text-muted font-9">{:L('先判断这条内容更像综述、观察、帮助还是热点解释，再决定标题和结构。')}</div>
                </div>
                <div class="form-group mb-3">
                    <input id="title" name="title" value="{$article_info.title|default=''}" class="aw-form-control" type="text" placeholder="{:L('输入内容标题')}">
                </div>
                <div class="form-group">
                    <label class="mb-3">{:L('内容类型')}</label>
                    <select class="aw-form-control" id="articleTypeSelect" name="article_type">
                        {foreach $article_type_options as $typeKey => $label}
                        <option value="{$typeKey}" {if isset($article_info['article_type']) && $article_info['article_type']==$typeKey}selected{/if}>{$label}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="form-group">
                    <label class="mb-3">{:L('内容分类')}</label>
                    <select class="aw-form-control" name="category_id" title="{:L('请选择一项分类')}" required>
                        <option value="0">{:L('选择分类')}</option>
                        {volist name="article_category_list" id="v"}
                        <option value="{$v.id}" {if isset($article_info['category_id']) && $article_info['category_id']==$v['id']}selected {/if}>{$v.title}</option>
                        {if !empty($v.childs)}
                        {foreach $v.childs as $child}
                        <option value="{$child.id}" {if isset($article_info['category_id']) && $article_info['category_id']==$child['id']}selected {/if}>
                        &nbsp;&nbsp;&nbsp;&nbsp;|__{$child.title}
                        </option>
                        {/foreach}
                        {/if}
                        {/volist}
                    </select>
                </div>

                {if($column_list)}
                <div class="form-group">
                    <label class="mb-3">{:L('内容合集')}</label>
                    <select class="aw-form-control" name="column_id">
                        <option value="0">{:L('选择内容合集')}</option>
                        {volist name="column_list" id="v"}
                        <option value="{$v.id}" {if isset($article_info['column_id']) && $v['id']==$article_info['column_id']}selected{/if}>{$v.name}</option>
                        {/volist}
                    </select>
                </div>
                {/if}
                <div class="form-group mb-3">
                    <label>{:L('内容封面')}:<span class="font-8 text-danger">（* {:L('建议图片尺寸')}178*100{:L('的倍数')}）</span></label>
                    <div class="article-cover-box">
                        <div id="fileList_cover" class="uploader-list"></div>
                        <div id="filePicker_cover" style="margin: 0 auto">
                            <a href="{$article_info['cover']|default='static/common/image/default-cover.svg'}" target="_blank">
                                <img class="image_preview_info" src="{$article_info['cover']|default='static/common/image/default-cover.svg'}" id="cover_preview" width="100" height="100">
                            </a>
                        </div>
                        <input type="hidden" name="cover" value="{$article_info['cover']|default=''}" class="article-cover">
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label class="mb-3 w-100">
                        {:L('绑定主题')}
                        <a href="javascript:;" class="text-primary font-weight-bold aw-ajax-open float-right" data-url="{:url('topic/select',['item_type'=>'article','item_id'=>isset($article_info['id']) ? $article_info['id'] : 0])}">
                            <i class="icon-add"></i>{:L('添加话题')}
                        </a>
                        {if(get_setting('topic_enable')=='Y')} <span class="font-9 text-primary">({:L('至少添加一个')})</span>{/if}
                    </label>
                    <div class="page-detail-topic swiper-container py-1">
                        <ul class="swiper-wrapper" id="awTopicList">
                            {if !empty($article_info['topics'])}
                            {volist name="article_info['topics']" id="v"}
                            <li class="swiper-slide"><a href="{:url('topic/detail',['id'=>$v['id']])}"><em class="tag">{$v.title}</em></a></li>
                            {/volist}
                            <input type="hidden" name="topics" value="{:implode(',',array_column($article_info['topics'],'id'))}">
                            {/if}
                        </ul>
                    </div>
                </div>
                {if !empty($help_chapter_options)}
                <div class="form-group mb-3">
                    <label class="mb-2">{:L('知识归档')}</label>
                    <div class="border rounded px-3 py-2" style="background:#fbfdff;border-color:#e6edf5 !important;">
                        <div class="text-muted font-9 mb-2">{:L('发布后可直接归档到知识章节，方便后续整理、检索和维护。')}</div>
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
                    <label class="mb-3">{:L('内容正文')}</label>
                    {:hook('editor',['name'=>'message','cat'=>'article','value'=>isset($article_info['message']) ? $article_info['message']:'','access_key'=>$access_key])}
                </div>

                <!--发布附加钩子-->
                {:hook('publish_extend',['info'=>$article_info,'page'=>'article'])}
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
                                item_id:'{$article_info.id|default=0}',
                                item_type:'article'
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
                    {if !isset($article_info['id']) && $setting.enable_anonymous=='Y'}
                    <label class="mb-0 text-muted mr-3">
                        <input value="1" name="is_anonymous" type="checkbox"  {$article_info.is_anonymous ? 'checked' : ''}> {:L('匿名发布')}
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

    $(document).on('click', '.js-apply-type', function () {
        let type = $(this).data('type');
        let title = $(this).data('title');
        if (type) {
            $('#articleTypeSelect').val(type).trigger('change');
        }
        if (title) {
            $('#title').val(title).trigger('input').trigger('focus');
        }
    });

    $(document).on('click', '.js-apply-idea', function () {
        let title = $(this).data('title');
        let type = $(this).data('type');
        if (type) {
            $('#articleTypeSelect').val(type).trigger('change');
        }
        if (title) {
            $('#title').val(title).trigger('input').trigger('focus');
        }
    });

    //上传文章封面
    AWS_MOBILE.upload.webUpload('filePicker_cover','cover_preview','cover','article')

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
                item_id:'{$article_info.id|default=0}',
                item_type:'article'
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

    $('.saveArticle').click(function (){
        var that = this;
        {if $captcha_enable}
        $('#captcha').captcha({
            callback: function () {
                publishArticle(that)
            }
        });
        {else/}
        publishArticle(that)
        {/if}})

        function publishArticle (){
            let form = $('#question_form');
            $('.saveArticle').attr('disabled',true);
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
                        $('.saveArticle').attr('disabled',false);
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
{block name="sideMenu"}{/block}
{block name="footer"}

{/block}
