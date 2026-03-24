{extend name="$theme_block" /}
{block name="style"}
<link rel="stylesheet" href="{$cdnUrl}/static/libs/select2/css/select2.css?v={$version|default='1.0.0'}">
<script src="{$cdnUrl}/static/libs/select2/js/select2.full.js?v={$version|default='1.0.0'}"></script>
<script src="{$cdnUrl}/static/libs/select2/js/i18n/zh-CN.js?v={$version|default='1.0.0'}"></script>
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__clear{margin: 0 !important;}
    .select2-container--default .select2-selection--multiple {
        width: 100%;
        box-sizing: border-box;
        display: flex;
        flex-direction: row;
        align-items: center;
        padding: 0 6px;
        gap: 10px;
        height: 40px;
        background: #FFFFFF;
        border: 1px solid #E0E0E0;
        border-radius: 2px;
        flex: none;
        align-self: stretch;
        flex-grow: 0;
    }
    .select2-selection__choice{
        display: flex;
        flex-direction: row;
        align-items: center;
        padding: 1px 8px;
        gap: 6px;
        height: 24px;
        background: #F5F5F5 !important;
        border: unset !important;
        border-radius: 2px;
        flex: none;
        flex-grow: 0;
        margin-top: 0 !important;
        font-style: normal;
        font-weight: 400;
        font-size: 14px;
        color: #1F1F1F;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove{
        margin-right: 0 !important;
    }
    .select2-selection__choice span {
        display: flex !important;
        flex-direction: row;
        align-items: flex-start;
        padding: 2px;
        gap: 10px;
        width: 16px;
        height: 16px;
        border-radius: 8px;
        flex: none;
        flex-grow: 0;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .select2-selection__choice span:hover{
        border-radius: 8px;
        background: #E0E0E0;
    }
    .select2-container .select2-search--inline .select2-search__field{margin-top: 0 !important;}
    .select2-container--default .select2-search--inline .select2-search__field::-webkit-input-placeholder{
        color:#A8ABAD
    }

    .select2-results__option {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 36px;
        border-radius: 2px;
        padding: 0 8px;
        background: #ffffff;
        font-size: 14px;
        font-family: Noto Sans SC, Noto Sans SC-400;
        font-weight: 400;
        color: #546eff;
        margin: 4px;
    }
    .select2-results__option--highlighted{
        background: #ffffff !important;
        color: #546eff !important;
    }
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #546eff !important;
        color: white !important;
    }
    #select2-topics-results{
        padding: 10px;
        display: flex;
        flex-wrap: wrap;
        background: #f2f2f5
    }
    .select2-dropdown{
        border: none !important;
    }
    .aw-publish-insight .insight-chip {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        background: #f4f7fb;
        color: #355070;
        margin: 0 8px 8px 0;
        font-size: 12px;
    }
    .aw-publish-insight .insight-title-idea {
        border-top: 1px solid #eef2f7;
        padding-top: 10px;
        margin-top: 10px;
    }
    .aw-publish-insight .insight-action {
        cursor: pointer;
    }
    .aw-publish-insight .insight-action:hover {
        color: #1d4ed8;
    }
    .aw-faq-guide {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }
    .aw-faq-guide-item {
        padding: 14px;
        border: 1px solid #e6edf5;
        border-radius: 14px;
        background: #fbfdff;
    }
    .aw-faq-guide-item strong {
        display: block;
        margin-bottom: 6px;
        color: #0f172a;
    }
    .aw-faq-guide-item span {
        display: block;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
    }
    .aw-faq-routing {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }
    .aw-faq-routing-item {
        padding: 14px;
        border: 1px solid #e6edf5;
        border-radius: 14px;
        background: #fbfdff;
    }
    .aw-faq-routing-item strong {
        display: block;
        margin-bottom: 6px;
        color: #0f172a;
    }
    .aw-faq-routing-item span {
        display: block;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
        margin-bottom: 10px;
    }
    .aw-faq-routing-item .btn {
        min-width: 112px;
    }
    .aw-archive-guide {
        padding: 14px;
        border: 1px solid #e6edf5;
        border-radius: 14px;
        background: #fbfdff;
        margin-bottom: 18px;
    }
    .aw-archive-option {
        display: inline-flex;
        align-items: center;
        margin: 0 10px 10px 0;
        padding: 8px 12px;
        border-radius: 999px;
        background: #f8fafc;
        border: 1px solid #dbe7f3;
        font-size: 13px;
        color: #334155;
        cursor: pointer;
    }
    .aw-archive-option input {
        margin-right: 6px;
    }
</style>
{/block}
{block name="main"}
<div class="container mt-2">
    <div class="row justify-content-between">
        <div class="aw-left radius col-md-9 mb-2">
            <div class="card border-0">
                <div class="card-body">
                    <form id="question_form"  method="post" action="{:url('question/publish')}">
                        {:token_field()}
                        <input type="hidden" id="captcha">
                        <input type="hidden" name="access_key" value="{$access_key}">
                        <input type="hidden" name="id" value="{$question_info['id']|default=0}">
                        <div class="mb-3">
                            <h4 class="mb-2">{:L('新建 FAQ 条目')}</h4>
                            <p class="text-muted mb-3">{:frelink_content_description('question')}</p>
                            <div class="aw-faq-guide">
                                <div class="aw-faq-guide-item">
                                    <strong>{:L('检索入口')}</strong>
                                    <span>{:L('优先补充用户会反复搜索的问题，而不是偶发讨论。')}</span>
                                </div>
                                <div class="aw-faq-guide-item">
                                    <strong>{:L('明确答案')}</strong>
                                    <span>{:L('尽量给出可执行结论，而不是只有模糊观点。')}</span>
                                </div>
                                <div class="aw-faq-guide-item">
                                    <strong>{:L('持续补齐')}</strong>
                                    <span>{:L('允许持续补充边界、例外情况和版本变化。')}</span>
                                </div>
                            </div>
                            <div class="aw-faq-routing">
                                <div class="aw-faq-routing-item">
                                    <strong>{:frelink_content_label('question')}</strong>
                                    <span>{:frelink_publish_type_scene('question')}</span>
                                    <button type="button" class="btn btn-outline-primary btn-sm" disabled>{:L('继续写 FAQ')}</button>
                                </div>
                                <div class="aw-faq-routing-item">
                                    <strong>{:frelink_content_label('research')}</strong>
                                    <span>{:frelink_publish_type_scene('research')}</span>
                                    <a class="btn btn-outline-primary btn-sm" href="{:frelink_publish_url('article',['article_type'=>'research'])}">{:L('改为写综述')}</a>
                                </div>
                                <div class="aw-faq-routing-item">
                                    <strong>{:frelink_content_label('fragment')}</strong>
                                    <span>{:frelink_publish_type_scene('fragment')}</span>
                                    <a class="btn btn-outline-primary btn-sm" href="{:frelink_publish_url('article',['article_type'=>'fragment'])}">{:L('改为写观察')}</a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group d-flex mb-3">
                            <div class="flex-fill">
                                <input id="title" name="title" value="{$question_info.title|default=''}" class="aw-form-control" type="text" placeholder="{:L('输入 FAQ 标题')}">
                                <div class="aw-dropdown mt-2 border" style="display: none ;border-radius: 5px" >
                                    <h6 class="px-3 pt-3 text-muted">{:L('这个 FAQ 可能已经有相关答案了')}</h6>
                                    <div class="aw-dropdown-list aw-common-list aw-overflow-auto text-left px-3 pb-3"></div>
                                </div>
                            </div>
                            {if !empty($category_list) && $setting.enable_category=='Y'}
                            <div class="flex-fill ml-2" style="max-width: 150px">
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
                            {/if}
                        </div>

                        <div class="form-group mb-3 aw-content">
                            <label class="mb-3">{:L('FAQ 说明')}</label>
                            {:hook('editor',['name'=>'detail','cat'=>'question','value'=>isset($question_info['detail']) ? $question_info['detail'] : '','access_key'=>$access_key])}
                        </div>

                        <div class="form-group mb-3">
                            <label class="mb-3">{:L('绑定主题')}</label>
                            <div class="topic">
                                <div class="tip-list">
                                    <label for="topics" class="d-block w-100">
                                        <select class="select2 form-control" value="{$question_info['topics']|default=''}" id="topics" name="topics[]" multiple>
                                            <option></option>
                                        </select>
                                    </label>
                                </div>
                            </div>
                        </div>
                        {if !empty($help_chapter_options)}
                        <div class="form-group mb-3">
                            <label class="mb-3">{:L('知识归档')}</label>
                            <div class="aw-archive-guide">
                                <div class="text-muted mb-2">{:L('把这条 FAQ 直接归到知识章节，后续更容易和综述、观察、帮助内容一起整理。')}</div>
                                {if !empty($suggested_help_chapters)}
                                <div class="font-weight-bold font-12 mb-2">{:L('建议归档章节')}</div>
                                <div class="mb-2">
                                    {volist name="suggested_help_chapters" id="v"}
                                    <label class="aw-archive-option">
                                        <input type="checkbox" name="help_chapter_ids[]" value="{$v.id}" {if !empty($v.selected)}checked{/if}>
                                        <span>{$v.title}</span>
                                    </label>
                                    {/volist}
                                </div>
                                {/if}
                                <div class="font-weight-bold font-12 mb-2">{:L('全部知识章节')}</div>
                                <div>
                                    {volist name="help_chapter_options" id="v"}
                                    {if empty($v.suggested)}
                                    <label class="aw-archive-option">
                                        <input type="checkbox" name="help_chapter_ids[]" value="{$v.id}" {if !empty($v.selected)}checked{/if}>
                                        <span>{$v.title}</span>
                                    </label>
                                    {/if}
                                    {/volist}
                                </div>
                            </div>
                        </div>
                        {/if}

                        {if get_plugins_config('paid_attach','enable')=='Y'}
                        {:hook('attachPublish',['info'=>$question_info,'page'=>'question','attach_list'=>$attach_list??[],'access_key'=>$access_key])}
                        {else/}
                        <div class="aw-attach-upload mb-3" data-path="question_attach">
                            <a  class="text-primary cursor-pointer font-weight-bold" id="testList" style="cursor: pointer"><i class="fas fa-cloud-upload-alt"></i>{:L('选择附件')}</a>
                            <input class="layui-upload-file" type="file" accept="" name="file" multiple="">
                            <span class="text-danger font-8">({:L('允许上传文件类型')}:{:get_setting('upload_file_ext')})</span>
                            <a class="cursor-pointer ml-3 font-weight-bold text-white float-right btn btn-primary px-3 btn-sm" id="uploadListAction" style="cursor: pointer">{:L('开始上传')}</a>
                            <div class="attach-upload-list mt-3">
                                <table class="layui-table">
                                    <thead>
                                    <tr>
                                        <th>{:L('文件名')}</th>
                                        <th>{:L('大小')}</th>
                                        <th>{:L('上传进度')}</th>
                                        <th>{:L('操作')}</th>
                                    </tr>
                                    </thead>
                                    <tbody id="attachList">
                                    {if $attach_list && isset($question_info['id'])}
                                    {volist name="$attach_list" id="v"}
                                    <tr>
                                        <td>{$v.name}</td>
                                        <td>{:formatBytes($v.size)}</td>
                                        <td>
                                            <span class="text-success">{:L('上传成功')}</span>
                                        </td>
                                        <td><button type="button" data-id="{$v.id}" data-key="{$v.access_key}" class="layui-btn layui-btn-xs layui-btn-danger aw-attach-delete">
                                            {:L('删除')}</button></td>
                                    </tr>
                                    {/volist}
                                    {/if}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {/if}
                        <!--发布附加钩子-->
                        {:hook('publish_extend',['info'=>$question_info,'page'=>'question'])}
                        <!--发布附加钩子-->

                        <div class="mt-4 clearfix">
                            {if !isset($question_info['id']) && $setting.enable_anonymous=='Y'}
                            <label class="mb-0 text-muted mr-3">
                                <input value="1" name="is_anonymous" type="checkbox"  {$question_info.is_anonymous ? 'checked' : ''}>
                                {:L('匿名发布')}
                            </label>
                            {/if}
                            <a href="{:url('page/score')}" target="_blank" ><i class="fa fa-database"></i> {:L(get_setting("score_unit"))}{:L('规则')}</a>
                            <button type="button" onclick="AWS.User.draft(this,'question','{$question_info.id|default=0}')" class="btn btn-outline-primary px-3 btn-sm aw-save-draft float-right">
                                {:L('存草稿')}</button>
                            {if get_setting('auto_save_draft')=='Y'}
                            <script>
                                //自动保存时间间隔
                                var AutoSaveTime = parseInt("{:get_setting('auto_save_draft_time')}")*360;
                                //设置自动保存
                                setInterval(function (item_type,itemId){
                                    var formData = AWS.common.formToJSON('question_form');
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
                                                AWS.api.error("{:L('发生错误, 返回的信息')}:" + ' ' + error.responseText);
                                            }
                                        }
                                    });
                                }, AutoSaveTime);
                            </script>
                            {/if}
                            <button type="button" class="btn btn-primary px-3 btn-sm aw-question-form mr-3 float-right">
                                {:L('发布 FAQ')}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="aw-right radius col-md-3 px-xs-0">
            {if !empty($publish_insight)}
            <div class="r-box mb-2 aw-publish-insight">
                <div class="r-title">
                    <h4>{:L('最近 %s 天可写方向',$publish_insight.window_days)}</h4>
                </div>
                <div class="pb-2">
                    {if !empty($publish_insight.top_keywords)}
                    <div class="mb-3">
                        <div class="font-weight-bold mb-2">{:L('最近有人在搜')}</div>
                        <div>
                            {volist name="publish_insight.top_keywords" id="v"}
                            <span class="insight-chip insight-action js-fill-title" data-title="{$v.keyword|htmlspecialchars}">{$v.keyword} · {$v.search_count}</span>
                            {/volist}
                        </div>
                    </div>
                    {/if}
                    {if !empty($publish_insight.title_ideas)}
                    <div class="mb-3">
                        <div class="font-weight-bold mb-2">{:L('建议问题标题')}</div>
                        {volist name="publish_insight.title_ideas" id="v"}
                        <div class="insight-title-idea insight-action js-fill-title" data-title="{$v.title|htmlspecialchars}">
                            <div>{$v.title}</div>
                            <div class="text-muted font-8 mt-1">{$v.reason}</div>
                        </div>
                        {/volist}
                    </div>
                    {/if}
                    {if !empty($publish_insight.suggested_topics)}
                    <div>
                        <div class="font-weight-bold mb-2">{:L('建议优先挂载的话题')}</div>
                        {volist name="publish_insight.suggested_topics" id="v"}
                        {if isset($v.url) && $v.url}
                        <a class="d-block text-primary mb-2" href="{$v.url}" target="_blank">{$v.title}</a>
                        {/if}
                        {/volist}
                    </div>
                    {/if}
                    <div class="r-box mb-2">
                        <div class="r-title">
                            <h4>{:L('发布前检查')}</h4>
                        </div>
                        <div class="pb-2">
                            <ul class="text-muted font-9 pl-3 mb-0">
                                <li class="mb-2">{:L('用户能否在 3 秒内知道这篇和自己有关')}</li>
                                <li class="mb-2">{:L('用户能否在 30 秒内获得一个新判断')}</li>
                                <li class="mb-2">{:L('用户看完后是否愿意继续点下一篇')}</li>
                                <li>{:L('标题是否真实反映正文，不靠夸张承诺骗点击')}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            {/if}
            <div class="r-box mb-2">
                <div class="r-title">
                    <h4>{:L('发布说明')}</h4>
                </div>
                <div class="pb-2">
                    <dl class="text-muted font-9">
                        <dt>{:L('问题标题')}：</dt>
                        <dd>{:L('请用准确的语言描述您发布的问题思想')}</dd>
                    </dl>
                    <dl class="text-muted font-9">
                        <dt>{:L('问题补充')}：</dt>
                        <dd>{:L('详细补充您的问题内容,并提供一些相关的素材以供参与者更多的了解您所要问题的主题思想')}</dd>
                    </dl>
                    <dl class="text-muted font-9">
                        <dt>{:L('选择话题')}：</dt>
                        <dd>{:L('选择一个或者多个合适的话题,让您发布的文章得到更多有相同兴趣的人参与,所有人可以在您发布文章之后添加和编辑该文章所属的话题')}</dd>
                    </dl>
                    <dl class="text-muted font-9">
                        <dt>{:L('关于')}{:L($setting.score_unit)}：</dt>
                        <dd>{:L('发起一个问题会消耗您')} {$integral_rule.NEW_QUESTION}{:L($setting.score_unit)},
                            {:L('每多一个回复你将获得')} {$integral_rule.QUESTION_ANSWER} {:L($setting.score_unit)}{:L('的奖励')} ,{:L('为了您的利益')},
                            {:L('在发起问题的时候希望能够更好的描述您的问题以及多使用站内搜索功能')}.</dd>
                    </dl>
                    {if !empty($publish_insight.guidance)}
                    <dl class="text-muted font-9">
                        <dt>{:L('运营建议')}：</dt>
                        {volist name="publish_insight.guidance" id="v"}
                        <dd>{$v}</dd>
                        {/volist}
                    </dl>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    let ACCESS_KEY = "{$access_key}";
    let ATTACH_LEN = parseInt('{:count($attach_list)}');
    let ITEM_ID = parseInt("{$question_info && isset($question_info['id']) ? $question_info['id'] : 0}");
    let SYS_ATTACH = "{:get_plugins_config('paid_attach','enable')=='Y' ? 1 : 0}";
    AWS.Dropdown.bind_dropdown_list('#title', 'publish');
</script>
{/block}

{block name="script"}
<script>
    let createEnable = parseInt("{if $user_info['permission']['create_topic_enable']=='Y' || isNormalAdmin() || isSuperAdmin()}1{else/}0{/if}");
    let topicBox = $('#topics');
    $(function () {
        $(document).on('click', '.js-fill-title', function () {
            let title = $(this).data('title');
            if (!title) {
                return;
            }
            $('#title').val(title).trigger('input').trigger('focus');
            $('html, body').animate({scrollTop: $('#title').offset().top - 120}, 150);
        });

        // 启用ajax分页查询
        var  option = {
            placeholder: "为你的作品贴“关键词”标签，最多不超过{:get_setting('max_topic_select')}个，单个标签不超过6个字符（{$setting.topic_enable=='Y'?'必填':'选填'}）",
            language: "zh-CN",
            allowClear: false,
            tags: createEnable === 1,
            createTag: function(params) {
                var term = $.trim(params.term);
                if (term === '') {
                    return null;
                }
                var match = false;
                // 判断输入的内容是否已经存在选项中
                $.each(this.$element.find('option'), function() {
                    if ($.trim($(this).text()) === term) {
                        match = true;
                        return false;
                    }
                });
                if (match) {
                    return null;
                }
                // 创建新的选项
                return {
                    id: term,
                    text: term,
                    newTag: true
                };
            },
            maximumSelectionLength: Number("{:get_setting('max_topic_select')}"),
            ajax: {
                type:'POST',
                delay: 250, // 限速请求
                url: "{:url('ajax.Topic/get_topics')}",   //  请求地址
                dataType: 'json',
                data: function (params) {
                    return {
                        keyWord: params.term || '',    //搜索参数
                        page: params.page || 1,        //分页参数
                        rows: 10,   //每次查询10条记录,
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data.list,
                        pagination: {
                            more: (params.page) < data.data.total
                        }
                    };
                },
                cache: true
            }
        };
        // 默认值设置
        var defaultValue = topicBox.attr("value");
        if (defaultValue) {
            $.ajax({
                type: "POST",
                url: "{:url('ajax.Topic/get_topics')}",
                data: {value:defaultValue},
                dataType: "json",
                success: function(data){
                    var html = '';
                    $.each(data.data.list,function(index,value){
                        html +="<option selected value='" + value.id + "'>" +value.text + "</option>"
                    })
                    topicBox.append(html);
                }
            });
        }

        $('#topics').select2(option).on('select2:select', function(e) {
            // 如果选择了新创建的选项，则需要把它添加到select2选项中
            if (e.params.data.newTag) {
                var $option = $('', {
                    value: e.params.data.id,
                    text: e.params.data.text,
                    selected: true
                });
                $(this).append($option).trigger('change');
            }
        });
    })
    $('.aw-question-form').click(function (){
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
</script>
{/block}
