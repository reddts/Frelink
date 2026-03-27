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
    .aw-type-guide {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }
    @media screen and (max-width: 1200px) {
        .aw-type-guide {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media screen and (max-width: 768px) {
        .aw-type-guide {
            grid-template-columns: 1fr;
        }
    }
    .aw-type-guide-item {
        padding: 14px;
        border: 1px solid #e6edf5;
        border-radius: 14px;
        background: #fbfdff;
    }
    .aw-type-guide-item strong {
        display: block;
        margin-bottom: 6px;
        color: #0f172a;
    }
    .aw-type-guide-item span {
        display: block;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
    }
    .aw-type-hint {
        margin-top: 10px;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
    }
    .aw-template-guide {
        margin-bottom: 18px;
        padding: 14px;
        border: 1px solid #e6edf5;
        border-radius: 14px;
        background: #fbfdff;
    }
    .aw-template-guide-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }
    .aw-template-guide-head strong {
        color: #0f172a;
    }
    .aw-template-guide-head span {
        color: #64748b;
        font-size: 13px;
    }
    .aw-template-guide-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
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
                    <form class="aw-form" method="post" action="{:url('article/publish')}" id="ajaxForm">
                        <input type="hidden" name="id" value="{$article_info.id|default=0}">
                        <!--<input type="hidden" name="wait_time">-->
                        <input type="hidden" name="access_key" value="{$access_key}">
                        <input type="hidden" id="captcha">
                        {:token_field()}
                        <div class="mb-3">
                            <h4 class="mb-2">{:L('新建知识内容')}</h4>
                            <p class="text-muted mb-3">{:L('先确定这条内容更像综述、观察、帮助还是热点解释，再决定怎么写。')}</p>
                            <div class="aw-type-guide">
                                <div class="aw-type-guide-item">
                                    <strong>{:frelink_content_label('research')}</strong>
                                    <span>{:frelink_content_description('research')}</span>
                                </div>
                                <div class="aw-type-guide-item">
                                    <strong>{:frelink_content_label('fragment')}</strong>
                                    <span>{:frelink_content_description('fragment')}</span>
                                </div>
                                <div class="aw-type-guide-item">
                                    <strong>{:frelink_content_label('track')}</strong>
                                    <span>{:frelink_content_description('track')}</span>
                                </div>
                                <div class="aw-type-guide-item">
                                    <strong>{:frelink_content_label('faq')}</strong>
                                    <span>{:frelink_content_description('faq')}</span>
                                </div>
                                <div class="aw-type-guide-item">
                                    <strong>{:frelink_content_label('normal')}</strong>
                                    <span>{:frelink_content_description('normal')}</span>
                                </div>
                            </div>
                            <div class="aw-template-guide">
                                <div class="aw-template-guide-head">
                                    <div>
                                        <strong>{:L('写作模板')}</strong>
                                        <span>{:L('先插入结构，再按你的判断补资料、分歧和结论。')}</span>
                                    </div>
                                </div>
                                <div class="aw-template-guide-actions">
                                    <button type="button" class="btn btn-outline-primary btn-sm js-apply-template" data-type="research">{:L('插入研究综述模板')}</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm js-apply-template" data-type="fragment">{:L('插入观察记录模板')}</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm js-apply-template" data-type="track">{:L('插入主题追踪模板')}</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group d-flex mb-3">
                            <div class="flex-fill">
                                <input class="aw-form-control" type="text" name="title" value="{$article_info['title']|default=''}" placeholder="{:L('输入内容标题')}">
                            </div>
                            <div class="flex-fill ml-2" style="max-width: 160px">
                                <select class="aw-form-control" id="articleTypeSelect" name="article_type">
                                    {foreach $article_type_options as $typeKey => $label}
                                    <option value="{$typeKey}" data-hint="{:frelink_publish_type_scene($typeKey)}" {if isset($article_info['article_type']) && $article_info['article_type']==$typeKey}selected{/if}>{$label}</option>
                                    {/foreach}
                                </select>
                                <div class="aw-type-hint" id="articleTypeHint">
                                    {:L('你现在选择的是')}<strong class="text-dark ml-1">{:frelink_article_type_label($article_info['article_type'] ?? 'research')}</strong>，
                                    <span id="articleTypeHintText">{:frelink_publish_type_scene($article_info['article_type'] ?? 'research')}</span>
                                </div>
                            </div>
                            {if($column_list)}
                            <div class="flex-fill ml-2">
                                <select class="aw-form-control" name="column_id">
                                    <option value="0">{:L('选择内容合集')}</option>
                                    {volist name="column_list" id="v"}
                                    <option value="{$v.id}" {if isset($article_info['column_id']) && $v['id']==$article_info['column_id']}selected{/if}>{$v.name}</option>
                                    {/volist}
                                </select>
                            </div>
                            {/if}
                            {if $article_category_list && $setting.enable_category=='Y'}
                            <div class="flex-fill ml-2" style="max-width: 150px">
                                <select class="aw-form-control" name="category_id">
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
                            {/if}
                        </div>
                        <div class="form-group mb-3">
                            <label>{:L('内容封面')}:<span class="font-8 text-danger">（* {:L('建议图片尺寸')}178*100{:L('的倍数')}）</span></label>
                            <div class="article-cover-box">
                                <div id="fileList_cover" class="uploader-list"></div>
                                <div id="filePicker_cover" style="margin: 0 auto">
                                    <a href="{$article_info['cover']|default='/static/common/image/default-cover.svg'}" target="_blank">
                                        <img class="image_preview_info" src="{$article_info['cover']|default='/static/common/image/default-cover.svg'}" id="cover_preview" width="100" height="100">
                                    </a>
                                </div>
                                <input type="hidden" name="cover" value="{$article_info['cover']|default=''}" class="article-cover">
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="mb-3">{:L('绑定主题')}</label>
                            <div class="topic">
                                <div class="tip-list">
                                    <label for="topics" class="d-block w-100">
                                        <select class="select2 form-control" value="{$article_info['topics']|default=''}" id="topics" name="topics[]" multiple>
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
                                <div class="text-muted mb-2">{:L('发布后可直接归档到知识章节，方便后续检索、聚合和长期维护。')}</div>
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
                        <div class="form-group mb-3 aw-content">
                            <label class="mb-3">{:L('内容正文')}</label>
                            {:hook('editor',['name'=>'message','cat'=>'article','value'=>isset($article_info['message']) ? $article_info['message']:'','access_key'=>$access_key])}
                        </div>
                        {if get_plugins_config('paid_attach','enable')=='Y'}
                        {:hook('attachPublish',['info'=>$article_info,'page'=>'article','attach_list'=>$attach_list??[],'access_key'=>$access_key])}
                        {else/}
                        <div class="aw-attach-upload mb-3" data-path="article_attach">
                            <a  class="text-primary cursor-pointer font-weight-bold" id="testList" style="cursor: pointer"><i class="fas fa-cloud-upload-alt"></i> {:L('选择附件')}</a>
                            <input class="layui-upload-file" type="file" accept="" name="file" multiple="">
                            <span class="text-danger font-8">({:L('允许上传文件类型')}:{:get_setting('upload_file_ext')})</span>
                            <a class="cursor-pointer btn btn-primary text-white px-3 btn-sm ml-3 font-weight-bold float-right" id="uploadListAction" style="cursor: pointer">{:L('开始上传')}</a>
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
                                    {if $attach_list && isset($article_info['id'])}
                                    {volist name="$attach_list" id="v"}
                                    <tr>
                                        <td>{$v.name}</td>
                                        <td>{:formatBytes($v.size)}</td>
                                        <td>
                                            <span class="text-success">{:L('上传成功')}</span>
                                        </td>
                                        <td><button type="button" data-id="{$v.id}" data-key="{$v.access_key}" class="layui-btn layui-btn-xs layui-btn-danger aw-attach-delete">{:L('删除')}</button></td>
                                    </tr>
                                    {/volist}
                                    {/if}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {/if}

                        <!--发布附加钩子-->
                        {:hook('publish_extend',['info'=>$article_info,'page'=>'article'])}
                        <!--发布附加钩子-->

                        <div class="mt-4 clearfix">
                            <a href="{:url('page/score')}" target="_blank" ><i class="fa fa-database"></i> {:get_setting("score_unit")}{:L('规则')}</a>
                            <button type="button" class="btn btn-primary btn-sm px-4 aw-article-publish ml-3 float-right">{:L('发布内容')}</button>
                            {if get_setting('auto_save_draft')=='Y'}
                            <script>
                                //自动保存时间间隔
                                let AutoSaveTime = parseInt("{:get_setting('auto_save_draft_time')}")*360;
                                //设置自动保存
                                setInterval(function (item_type,itemId){
                                    var formData = AWS.common.formToJSON('ajaxForm');
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
                                                AWS.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                                            }
                                        }
                                    });
                                }, AutoSaveTime);
                            </script>
                            {/if}
                            <button type="button" onclick="AWS.User.draft(this,'article','{$article_info.id|default=0}')" class="btn btn-outline-primary px-4 btn-sm ml-3 float-right">{:L('存草稿')}</button>
                            <button type="button" data-url="{:url('article/preview')}" class="btn btn-outline-primary btn-sm aw-preview px-4 float-right">{:L('预览')}</button>
                        </div>

                        {if !isset($article_info['id'])}
                        <script id="timing-publish-modal" type="text/html">
                            <div class="rounded p-3">
                                <div class="form-group">
                                    <label for="timing"><input type="text" id="timing" placeholder="{:L('选择定时发布时间')}" class="form-control"></label>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm px-4 mr-3 select-choose">{:L('确定选择')}</button>
                            </div>
                        </script>
                        {/if}
                    </form>
                </div>
            </div>
        </div>
        <div class="aw-right radius col-md-3 px-xs-0">
            {:hook('content_ocr',['type'=>'article','element'=>''])}
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
                        <div class="font-weight-bold mb-2">{:L('建议文章标题')}</div>
                        {volist name="publish_insight.title_ideas" id="v"}
                        <div class="insight-title-idea insight-action js-apply-idea" data-title="{$v.title|htmlspecialchars}" data-type="{$v.recommended_type|default=''}">
                            <div>{$v.title}</div>
                            {if !empty($v.recommended_type_label)}
                            <div class="text-primary font-8 mt-1">{:L('推荐形态')}：{$v.recommended_type_label}</div>
                            {/if}
                            <div class="text-muted font-8 mt-1">{$v.reason}</div>
                        </div>
                        {/volist}
                    </div>
                    {/if}
                    {if !empty($publish_insight.type_ideas)}
                    <div class="mb-3">
                        <div class="font-weight-bold mb-2">{:L('建议内容形态')}</div>
                        <div>
                            {volist name="publish_insight.type_ideas" id="v"}
                            <span class="insight-chip insight-action js-apply-type" data-type="{$v.type}" data-title="{$v.title|htmlspecialchars}">
                                {$v.label} · {$v.keyword}
                            </span>
                            {/volist}
                        </div>
                    </div>
                    {/if}
                    {if !empty($weekly_execution)}
                    <div class="mb-3">
                        <div class="font-weight-bold mb-2">{:L('本周优先写作')}</div>
                        {volist name="weekly_execution" id="v"}
                        <div class="border rounded p-2 mb-2 bg-white">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="pr-2">
                                    <div class="font-weight-bold">{$v.title}</div>
                                    <div class="text-muted font-8 mt-1">{$v.label}{if !empty($v.keyword)} · {$v.keyword}{/if}</div>
                                </div>
                                <span class="badge badge-light border">{$v.content_type}</span>
                            </div>
                            <div class="text-muted font-8 mt-2">{$v.reason}</div>
                            <div class="mt-2">
                                <a href="{$v.primary_url}" target="_blank" class="btn btn-primary btn-sm mr-2">{$v.primary_label}</a>
                                <a href="{$v.secondary_url}" target="_blank" class="btn btn-outline-secondary btn-sm">{$v.secondary_label}</a>
                            </div>
                        </div>
                        {/volist}
                    </div>
                    {/if}
                    {if !empty($publish_insight.suggested_topics)}
                    <div>
                        <div class="font-weight-bold mb-2">建议优先扩展的话题</div>
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
                <div class="pb-2 fbsm">
                    <dl class="text-muted font-9">
                        <dt>{:L('文章标题')}：</dt>
                        <dd>{:L('请用准确的语言描述您发布的文章思想')}</dd>
                    </dl>

                    <dl class="text-muted font-9">
                        <dt>{:L('文章补充')}：</dt>
                        <dd>{:L('详细补充您的文章内容')}, {:L('并提供一些相关的素材以供参与者更多的了解您所要文章的主题思想')}</dd>
                    </dl>

                    <dl class="text-muted font-9">
                        <dt>{:L('选择话题')}：</dt>
                        <dd>{:L('选择一个或者多个合适的话题,让您发布的文章得到更多有相同兴趣的人参与,所有人可以在您发布文章之后添加和编辑该文章所属的话题')}</dd>
                    </dl>
                    {if !empty($publish_insight.guidance)}
                    <dl class="text-muted font-9">
                        <dt>运营建议：</dt>
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
    let ITEM_ID = parseInt("{$article_info['id']??0}");
    let SYS_ATTACH = "{:get_plugins_config('paid_attach','enable')=='Y' ? 1 : 0}";
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
            $('input[name="title"]').val(title).trigger('input').trigger('focus');
            $('html, body').animate({scrollTop: $('input[name="title"]').offset().top - 120}, 150);
        });

        $(document).on('click', '.js-apply-type', function () {
            let type = $(this).data('type');
            let title = $(this).data('title');
            if (type) {
                $('#articleTypeSelect').val(type).trigger('change');
            }
            if (title) {
                $('input[name="title"]').val(title).trigger('input').trigger('focus');
            }
        });

        $(document).on('click', '.js-apply-idea', function () {
            let type = $(this).data('type');
            if (type) {
                $('#articleTypeSelect').val(type).trigger('change');
            }
        });

        function buildEditorTemplate(type) {
            if (type === 'fragment') {
                return [
                    '<h3>观察</h3><p>这次我重点看到的变化、现象或信号是什么？</p>',
                    '<h3>触发原因</h3><p>是什么事件、资料或体验触发了这条记录？</p>',
                    '<h3>暂时判断</h3><p>我当前的判断是什么？它成立的边界在哪里？</p>',
                    '<h3>后续待补资料</h3><p>下一步还需要补哪些数据、案例或对照材料？</p>'
                ].join('');
            }

            if (type === 'track') {
                return [
                    '<h3>阶段更新</h3><p>这一次追踪最重要的变化是什么？</p>',
                    '<h3>本期变化</h3><p>相比上次判断，哪些地方发生了变化？</p>',
                    '<h3>旧判断是否要修正</h3><p>哪些旧结论已经过时，为什么？</p>',
                    '<h3>下一步观察点</h3><p>接下来最值得继续看的 2-3 个信号是什么？</p>'
                ].join('');
            }

            return [
                '<h3>背景</h3><p>这个主题为什么值得现在重新看一遍？它处在什么上下文里？</p>',
                '<h3>核心问题</h3><p>这篇综述真正要回答的 2-3 个关键问题是什么？</p>',
                '<h3>资料来源</h3><p>我主要参考了哪些资料？哪些是一手资料，哪些是二手整理？</p>',
                '<h3>分歧点</h3><p>当前最重要的分歧在哪里？不同观点分别基于什么前提？</p>',
                '<h3>当前判断</h3><p>基于现有资料，我当前更倾向什么判断？为什么？</p>',
                '<h3>待验证</h3><p>还有哪些关键问题没有证据，后续要继续跟踪？</p>'
            ].join('');
        }

        function setEditorHtml(html) {
            if (typeof editor !== 'undefined' && editor.txt) {
                editor.txt.html(html);
            }
            $('textarea[name="message"]').val(html);
        }

        $(document).on('click', '.js-apply-template', function () {
            let type = $(this).data('type') || 'research';
            let templateHtml = buildEditorTemplate(type);
            let currentHtml = '';
            if (typeof editor !== 'undefined' && editor.txt) {
                currentHtml = $.trim(editor.txt.html());
            } else {
                currentHtml = $.trim($('textarea[name="message"]').val());
            }
            let nextHtml = currentHtml ? currentHtml + '<hr>' + templateHtml : templateHtml;
            $('#articleTypeSelect').val(type === 'fragment' ? 'fragment' : 'research').trigger('change');
            setEditorHtml(nextHtml);
            $('html, body').animate({scrollTop: $('.aw-content').offset().top - 120}, 150);
        });

        function updateArticleTypeHint() {
            let option = $('#articleTypeSelect option:selected');
            $('#articleTypeHint strong').text(option.text() || '');
            $('#articleTypeHintText').text(option.data('hint') || '');
        }

        $('#articleTypeSelect').on('change', updateArticleTypeHint);
        updateArticleTypeHint();

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
    $('.aw-article-publish').click(function (){
        var that = this;
        {if $captcha_enable}
        $('#captcha').captcha({
            callback: function () {
                publishArticle(that)
            }
        });
        {else/}
            publishArticle(that)
            {/if}
            })
</script>
{/block}
