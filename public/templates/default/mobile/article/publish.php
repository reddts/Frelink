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
                    <span class="mr-2 text-primary js-fill-title" data-title="{$v.keyword|htmlspecialchars}">{$v.keyword} · {$v.search_count}</span>
                    {/volist}
                </div>
                {/if}
                {if !empty($publish_insight.title_ideas)}
                <div class="font-9">
                    <div class="font-weight-bold mb-2">{:L('建议文章标题')}</div>
                    {volist name="publish_insight.title_ideas" id="v"}
                    <div class="mb-2 text-primary js-apply-idea" data-title="{$v.title|htmlspecialchars}" data-type="{$v.recommended_type|default=''}">
                        {$v.title}
                        {if !empty($v.recommended_type_label)}
                        <div class="text-muted font-8 mt-1">{:L('推荐形态')}：{$v.recommended_type_label}</div>
                        {/if}
                        <div class="text-muted font-8 mt-1">{$v.reason}</div>
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
                {if !empty($publish_insight.fragment_ideas)}
                <div class="font-9 mt-2">
                    <div class="font-weight-bold mb-2">{:L('观察整理')}</div>
                    <div class="text-muted font-8 mb-2">{:L('把高频阅读的观察整理成更稳定的综述、帮助或追踪。')}</div>
                    {volist name="publish_insight.fragment_ideas" id="v"}
                    <div class="mb-2 text-primary js-apply-idea" data-title="{$v.title|htmlspecialchars}" data-type="{$v.recommended_type|default='research'}">
                        {$v.title}
                        {if !empty($v.recommended_type_label)}
                        <div class="text-muted font-8 mt-1">{:L('推荐形态')}：{$v.recommended_type_label}</div>
                        {/if}
                    </div>
                    {/volist}
                </div>
                {/if}
                {if !empty($publish_insight.suggested_topics)}
                <div class="font-9 mt-2">
                    <div class="font-weight-bold mb-2">{:L('建议优先扩展的话题')}</div>
                    {volist name="publish_insight.suggested_topics" id="v"}
                    {if isset($v.topic_id) && $v.topic_id}
                    <button type="button" class="btn btn-link p-0 d-block text-left text-primary mb-2 js-apply-suggested-topic" data-topic-id="{$v.topic_id}" data-topic-title="{$v.title|htmlspecialchars}" data-topic-url="{$v.url|default=''}">
                        {$v.title|htmlspecialchars}
                    </button>
                    {/if}
                    {/volist}
                </div>
                {/if}
                {if !empty($publish_insight.topic_graph.nodes)}
                <div class="font-9 mt-2">
                    <div class="font-weight-bold mb-2">{:L('主题图谱')}</div>
                    <div class="text-muted font-8 mb-2">{:L('优先把经常一起出现的主题串成一条内容线，减少内容割裂。')}</div>
                    {volist name="publish_insight.topic_graph.nodes" id="v"}
                    <div class="border rounded px-2 py-2 mb-2" style="background:#fff;border-color:#e5edf6 !important;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="pr-2">
                                <button type="button" class="btn btn-link p-0 text-left text-primary font-weight-bold js-apply-suggested-topic" data-topic-id="{$v.topic_id}" data-topic-title="{$v.title|htmlspecialchars}" data-topic-url="{$v.url|default=''}">{$v.title}</button>
                                <div class="text-muted font-8 mt-1">{:L('内容关联')}：{$v.content_count} {:L('条')}</div>
                            </div>
                            <span class="badge badge-light border">{:L('图谱')}：{$v.weight}</span>
                        </div>
                        {if !empty($v.related_topics)}
                        <div class="mt-2">
                            {volist name="v.related_topics" id="related"}
                            <span class="insight-chip js-apply-suggested-topic" data-topic-id="{$related.topic_id}" data-topic-title="{$related.title|htmlspecialchars}" data-topic-url="{$related.url|default=''}">{$related.title} · {$related.weight}</span>
                            {/volist}
                        </div>
                        {/if}
                    </div>
                    {/volist}
                </div>
                {/if}
                <div class="font-9 mt-2">
                    <div class="font-weight-bold mb-2">{:L('写作模板')}</div>
                    <a href="javascript:;" class="btn btn-outline-primary btn-sm mr-2 mb-2 js-apply-template" data-type="research">{:L('插入研究综述模板')}</a>
                    <a href="javascript:;" class="btn btn-outline-primary btn-sm mr-2 mb-2 js-apply-template" data-type="fragment">{:L('插入观察记录模板')}</a>
                    <a href="javascript:;" class="btn btn-outline-primary btn-sm mb-2 js-apply-template" data-type="track">{:L('插入主题追踪模板')}</a>
                </div>
                <div class="font-9 mt-2">
                    <div class="font-weight-bold mb-2">{:L('运营规则')}</div>
                    <div class="text-muted font-8 mb-2">{:L('不同内容类型的写法侧重点不同，先按规则选型，再往下写。')}</div>
                    <ul class="text-muted pl-3 mb-0" id="mobileArticleTypeRulesList">
                        {volist name="$publish_type_rules_map[$article_info['article_type'] ?? 'research']" id="rule"}
                        <li class="mb-2">{$rule}</li>
                        {/volist}
                    </ul>
                </div>
                {if !empty($weekly_execution)}
                <div class="font-9 mt-2">
                    <div class="font-weight-bold mb-2">{:L('本周优先写作')}</div>
                    {volist name="weekly_execution" id="v"}
                    <div class="border rounded px-2 py-2 mb-2" style="background:#fff;border-color:#e5edf6 !important;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="pr-2">
                                <div class="text-primary font-weight-bold js-apply-idea" data-title="{$v.title|htmlspecialchars}" data-type="{$v.content_type|default='research'}">{$v.title}</div>
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
                        <option value="{$typeKey}" data-hint="{:frelink_publish_type_scene($typeKey)}" {if isset($article_info['article_type']) && $article_info['article_type']==$typeKey}selected{/if}>{$label}</option>
                        {/foreach}
                    </select>
                    <div class="text-muted font-8 mt-2" id="mobileArticleTypeHintText">{:frelink_publish_type_scene($article_info['article_type'] ?? 'research')}</div>
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
                            <li class="swiper-slide"><a href="{:url('topic/detail',['id'=>$v['id']])}"><em class="tag" data-topic-id="{$v.id}">{$v.title|htmlspecialchars}</em></a></li>
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
    let PUBLISH_TYPE_RULES = {:json_encode($publish_type_rules_map, JSON_UNESCAPED_UNICODE)};

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
        $('#articleTypeSelect').val(type === 'fragment' ? 'fragment' : (type === 'track' ? 'track' : 'research')).trigger('change');
        if (typeof editor !== 'undefined' && editor.txt) {
            editor.txt.html(nextHtml);
        }
        $('textarea[name="message"]').val(nextHtml);
        window.scrollTo({top: document.querySelector('.card-body').offsetTop, behavior: 'smooth'});
    });

    $(document).on('click', '.js-apply-suggested-topic', function () {
        let topicId = $(this).data('topic-id');
        let topicTitle = $(this).data('topic-title');
        let topicUrl = $(this).data('topic-url');
        if (!topicId || !topicTitle) {
            return;
        }
        addMobileTopic(topicId, topicTitle, topicUrl);
    });

    $('#articleTypeSelect').on('change', updateMobileArticleTypeHint);
    updateMobileArticleTypeHint();

    function updateMobileArticleTypeHint() {
        let option = $('#articleTypeSelect option:selected');
        $('#mobileArticleTypeHintText').text(option.data('hint') || '');
        let selectedType = $('#articleTypeSelect').val() || 'research';
        let rules = PUBLISH_TYPE_RULES[selectedType] || PUBLISH_TYPE_RULES.normal || [];
        let html = '';
        rules.forEach(function (item) {
            html += '<li class="mb-2">' + item + '</li>';
        });
        $('#mobileArticleTypeRulesList').html(html);
    }

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

    function addMobileTopic(topicId, topicTitle, topicUrl) {
        let topicValue = String(topicId);
        let hiddenInput = $('input[name="topics"]');
        let currentValues = hiddenInput.length && hiddenInput.val() ? hiddenInput.val().split(',').filter(Boolean) : [];
        if (!currentValues.includes(topicValue)) {
            currentValues.push(topicValue);
        }

        if (!hiddenInput.length) {
            hiddenInput = $('<input>', {type: 'hidden', name: 'topics'}).insertAfter('#awTopicList');
        }
        hiddenInput.val(currentValues.join(','));

        let topicList = $('#awTopicList');
        if (!topicList.find('[data-topic-id="' + topicValue + '"]').length) {
            let $slide = $('<li>', {class: 'swiper-slide'});
            let $link = $('<a>', {href: topicUrl ? topicUrl : 'javascript:;'});
            let $tag = $('<em>', {class: 'tag'}).attr('data-topic-id', topicValue).text(topicTitle);
            $link.append($tag);
            $slide.append($link);
            topicList.append($slide);
        }
    }

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
