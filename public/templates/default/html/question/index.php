{extend name="$theme_block" /}
{block name="style"}
<style>
    .aw-faq-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
        padding: 14px 24px 0;
        margin: 0 24px 18px;
        border: 0 !important;
        border-bottom: 0 !important;
        background: transparent;
        box-shadow: none;
    }

    .aw-faq-tabs .nav-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 18px;
        margin: 0;
        border-radius: 14px;
        border: 1px solid #d9e4ec;
        background: linear-gradient(180deg, #fff 0%, #f6fbff 100%);
        color: #475569;
        font-size: 13px;
        font-weight: 600;
        line-height: 1;
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.05);
    }

    .aw-faq-tabs .nav-link:hover {
        color: #0f172a;
        background: #eef5f7;
        border-color: #cddae5;
        text-decoration: none;
    }

    .aw-faq-tabs .nav-link.active {
        color: #fff;
        background: linear-gradient(135deg, #1d4ed8 0%, #0f766e 100%);
        border-color: transparent;
        box-shadow: 0 10px 18px rgba(29, 78, 216, 0.18);
    }

    .aw-question-feed {
        padding: 0 8px 6px;
    }

    .aw-faq-lanes {
        padding: 18px 16px 18px;
    }

    .aw-question-feed .aw-question-card {
        position: relative;
        margin: 0 0 18px;
        padding: 16px 18px 14px;
        border: 1px solid #d9e4ec;
        border-radius: 24px;
        background: linear-gradient(180deg, #fff 0%, #fbfdff 100%);
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .aw-question-feed .aw-question-card:hover {
        transform: translateY(-1px);
        border-color: #cbd9e6;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
    }

    .aw-question-feed .aw-question-card > dt {
        margin-bottom: 12px;
    }

    .aw-question-feed .aw-question-card > dd:last-child {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        padding-top: 16px;
        margin-top: 16px;
        border-top: 1px solid #e5edf4;
    }

    .aw-question-feed .n-title {
        margin-bottom: 10px;
        color: #0f172a;
    }

    .aw-question-feed .n-title a {
        color: #0f172a;
        font-size: 18px;
        font-weight: 700;
        line-height: 1.45;
    }

    .aw-question-feed .n-title a:hover {
        color: #1d4ed8;
        text-decoration: none;
    }

    .aw-question-feed .text-muted.mb-2 {
        color: #64748b !important;
        font-size: 13px;
        line-height: 1.7;
    }

    .aw-question-card .aw-ajax-agree,
    .aw-question-card .aw-ajax-against {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-height: 40px;
        padding: 0 16px;
        border: 1px solid #d9e4ec;
        border-radius: 999px;
        background: linear-gradient(180deg, #fff 0%, #f6fbff 100%);
        color: #0f172a;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.05);
        transition: all 0.2s ease;
    }

    .aw-question-card .aw-ajax-agree:hover,
    .aw-question-card .aw-ajax-against:hover {
        background: #eef5f7;
        border-color: #cddae5;
        color: #0f172a;
        text-decoration: none;
    }

    .aw-question-card .aw-ajax-agree i,
    .aw-question-card .aw-ajax-against i {
        color: inherit;
    }

    .aw-question-card .aw-ajax-agree.active {
        background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
        border-color: #1d4ed8;
        color: #fff;
    }

    .aw-question-card .aw-ajax-against.active {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        border-color: #dc2626;
        color: #fff;
    }

    .aw-question-card .aw-ajax-agree .badge,
    .aw-question-card .aw-ajax-against .badge {
        background: rgba(255, 255, 255, 0.16);
        color: inherit;
    }

    .aw-question-card .dz {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .aw-question-card .dz .btn {
        padding: 0 14px !important;
        line-height: 38px;
    }

    .aw-question-feed label.dz a {
        color: #ffffff !important;
    }

    .aw-question-feed label.dz a i {
        color: #ffffff !important;
    }

    .aw-question-feed label.dz a:hover,
    .aw-question-feed label.dz a.active,
    .aw-question-feed label.dz a:hover i,
    .aw-question-feed label.dz a.active i {
        color: #ffc107 !important;
    }

    .aw-side-entry-panel {
        padding: 14px;
        border: 1px solid #d9e4ec;
        border-radius: 20px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbfd 100%);
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
    }

    .aw-side-entry-grid {
        display: grid;
        gap: 12px;
    }

    .aw-side-entry-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 16px;
        border: 1px solid #d9e4ec;
        background: #fff;
        color: #0f172a;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .aw-side-entry-card:hover {
        text-decoration: none;
        color: #0f172a;
        transform: translateY(-1px);
        border-color: #c4d5e2;
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
    }

    .aw-side-entry-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 14px;
        font-size: 18px;
        flex: 0 0 42px;
    }

    .aw-side-entry-copy {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .aw-side-entry-copy strong {
        color: #0f172a;
        font-size: 14px;
        font-weight: 800;
        line-height: 1.3;
    }

    .aw-side-entry-copy em {
        margin-top: 3px;
        color: #64748b;
        font-style: normal;
        font-size: 12px;
        line-height: 1.5;
    }

    .aw-side-entry-card-faq .aw-side-entry-icon {
        color: #a16207;
        background: #fef3c7;
    }

    .aw-side-entry-card-create .aw-side-entry-icon {
        color: #b91c1c;
        background: #fee2e2;
    }

    @media (max-width: 767.98px) {
        .aw-faq-tabs {
            margin: 0 16px 16px;
            padding: 10px 16px 0;
            gap: 8px;
        }

        .aw-faq-tabs .nav-link {
            min-height: 38px;
            padding: 0 14px;
        }

        .aw-question-feed .aw-question-card {
            margin: 0 0 16px;
            padding: 14px 14px 12px;
            border-radius: 20px;
        }

        .aw-question-feed .aw-question-card > dd:last-child {
            gap: 8px;
        }
    }
</style>
{/block}
{block name="main"}
<div class="aw-wrap mt-2" id="wrapMain">
    {if $setting.enable_category=='Y'}
    {:widget('common/category',['type'=>'question','category'=>$category,'show_type'=>'list'])}
    {/if}
    <div class="container">
        <div class="row justify-content-between">
            <div class="aw-left radius col-md-9 bg-white mb-2">
                <div class="aw-faq-hero">
                    <div class="aw-page-kicker">FAQ Atlas</div>
                    <h1>{:L('问题索引')}</h1>
                    <p>{:L('这里承接高频 FAQ、明确答案和可复用解释。它不再是社区问答流，而是公开知识系统里的问题入口。')}</p>
                    <div class="aw-page-chips">
                        <span>{:L('高频问题优先')}</span>
                        <span>{:L('答案可复用')}</span>
                        <span>{:L('沿主题继续追踪')}</span>
                    </div>
                </div>
                <div class="aw-faq-lanes">
                    <a class="aw-faq-lane" href="{:url('question/index',['sort'=>'new','category_id'=>$category])}" data-pjax="wrapMain">
                        <strong>{:L('最新补充')}</strong>
                        <span>{:L('查看最近补入系统的 FAQ 条目，快速确认近期新增了哪些明确答案。')}</span>
                    </a>
                    <a class="aw-faq-lane" href="{:url('question/index',['sort'=>'unresponsive','category_id'=>$category])}" data-pjax="wrapMain">
                        <strong>{:L('待补充 FAQ')}</strong>
                        <span>{:L('优先发现仍缺答案或需要继续完善说明的 FAQ 入口。')}</span>
                    </a>
                    <a class="aw-faq-lane" href="{:url('topic/index')}">
                        <strong>{:L('转到主题')}</strong>
                        <span>{:L('如果单条 FAQ 不够，就沿主题继续追踪背景、资料和后续变化。')}</span>
                    </a>
                </div>
                <nav class="aw-pjax-a aw-faq-tabs" aria-label="{:L('FAQ 列表筛选')}">
                    <a class="nav-item nav-link {if $sort=='recommend'}active{/if}" data-pjax="wrapMain" href="{:url('question/index',['sort'=>'recommend','category_id'=>$category])}" {if $sort=='recommend'}aria-current="page"{/if}>{:L('精选')}</a>
                    <a class="nav-item nav-link {if $sort=='new'}active{/if}" data-pjax="wrapMain" href="{:url('question/index',['sort'=>'new','category_id'=>$category])}" {if $sort=='new'}aria-current="page"{/if}>{:L('更新')}</a>
                    <a class="nav-item nav-link {if $sort=='hot'}active{/if}" data-pjax="wrapMain" href="{:url('question/index',['sort'=>'hot','category_id'=>$category])}" {if $sort=='hot'}aria-current="page"{/if}>{:L('高关注')}</a>
                    <a class="nav-item nav-link {if $sort=='unresponsive'}active{/if}" data-pjax="wrapMain" href="{:url('question/index',['sort'=>'unresponsive','category_id'=>$category])}" {if $sort=='unresponsive'}aria-current="page"{/if}>{:L('待补充 FAQ')}</a>
                </nav>

                <div id="tabMain" class="tab-content" >
                    <div class="tab-pane fade show active">
                        <div class="aw-common-list aw-question-feed">
                            {we:question sort="$sort" category_id="$category"}
                            {:hook('postsListsExtend',['info'=>$v,'key'=>$key,'type'=>'question'])}
                            <dl class="js-analytics-impression aw-question-card" data-analytics-type="question" data-analytics-id="{$v['id']}" data-analytics-list="question_index" data-analytics-position="{$key + 1}" data-analytics-source="desktop_question_index">
                                <dt>
                                    {if (!$v['answer_info'])}
                                    {if $v.is_anonymous}
                                    <a href="javascript:;" class="aw-username">
                                        <img src="/static/common/image/default-avatar.svg" class="aw-user-img circle" alt="{:L('匿名用户')}">{:L('匿名用户')}
                                    </a>
                                    {else/}
                                    <a href="{$v['user_info']['url']}" class="aw-user-name" data-id="{$v['user_info']['uid']}" target="_blank">
                                        <img src="{$v['user_info']['avatar']}" onerror="this.src='/static/common/image/default-avatar.svg'" class="circle" alt="{$v['user_info']['name']}" loading="lazy" decoding="async">{$v['user_info']['name']}
                                    </a>
                                    {/if}
                                    <i>{:L('补充了 FAQ')}</i>
                                    <em class="time">{:date_friendly($v['update_time'])}</em>
                                    {else/}
                                    {if $v['answer_info']['is_anonymous']}
                                    <a href="javascript:;" class="aw-username" >
                                        <img src="/static/common/image/default-avatar.svg" class="aw-user-img circle" alt="{:L('匿名用户')}">{:L('匿名用户')}
                                    </a>
                                    {else/}
                                    <a href="{$v['answer_info']['user_info']['url']}" data-id="{$v['answer_info']['uid']}" class="aw-user-name" target="_blank">
                                        <img src="{$v['answer_info']['user_info']['avatar']}" onerror="this.src='/static/common/image/default-avatar.svg'" class="circle" alt="{$v['answer_info']['user_info']['name']}" loading="lazy" decoding="async">{$v['answer_info']['user_info']['name']}
                                    </a>
                                    {/if}
                                    <i>{:L('更新了 FAQ 答案')}</i>
                                    <em class="time">{:date_friendly($v['answer_info']['create_time'])}</em>
                                    {/if}
                                    {if isset($v['topics']) && !empty($v['topics'])}
                                    <div class="tag d-inline-block">
                                        {volist name="$v['topics']" id="topic"}
                                        <a href="{:url('topic/detail',['id'=>$topic['id']])}" class="aw-topic" data-id="{$topic.id}" target="_blank"><em class="tag">{$topic.title}</em></a>
                                        {/volist}
                                    </div>
                                    {/if}
                                </dt>
                                <dd>
                                    <div class="n-title">
                                        <span class="tip-s1 badge badge-secondary">{:L('FAQ')}</span>
                                        {if $v.set_top}
                                        <span class="tip-d badge badge-secondary">{:L('顶')}</span>
                                        {/if}
                                        <a href="{:url('question/detail',['id'=>$v['id']])}" target="_blank" class="js-analytics-click" data-analytics-type="question" data-analytics-id="{$v['id']}" data-analytics-list="question_index" data-analytics-position="{$key + 1}" data-analytics-source="desktop_question_index">{$v.title|raw}</a>
                                        {:hook('extend_title_label',['area'=>'question_list','info'=>$v])}
                                    </div>
                                    <div class="pcon {if $v['img_list'] && get_theme_setting('common.list_show_image')=='Y'}row{/if}">
                                        {if $v['img_list'] && get_theme_setting('common.list_show_image')=='Y'}
                                        <div class="col-md-12 t-imglist row">
                                            {volist name="$v['img_list']" id="img" key="k"}
                                            {if($k<4)}
                                            <div class="col-md-4 aw-list-img">
                                                <img src="{$img|default='/static/common/image/default-cover.svg'}" class="rounded w-100 aw-cut-img" style="margin-bottom: 5px;" loading="lazy" decoding="async">
                                            </div>
                                            {/if}
                                            {/volist}
                                        </div>
                                        <div class="ov-3 col-md-12">
                                            <div class="aw-two-line">
                                                {if !$v['answer_info']}
                                                    <a href="{:url('question/detail',['id'=>$v['id']])}" target="_blank">{$v.detail|raw}</a>:
                                                {else/}
                                                    {if $v['answer_info']['is_anonymous']}
                                                    <a href="javascript:;" class="aw-username" >匿名用户</a>:
                                                    {else/}
                                                    <a href="{$v['answer_info']['user_info']['url']}" class="aw-username" >{$v['answer_info']['user_info']['name']}</a>:
                                                    {/if}
                                                    <a href="{:url('question/detail',['id'=>$v['id'],'answer'=>$v['answer_info']['id']])}" target="_blank">{$v.detail|raw}</a>
                                                {/if}
                                            </div>
                                        </div>
                                        {else/}
                                        <div class="aw-two-line">
                                            {if !$v['answer_info']}
                                                <a href="{:url('question/detail',['id'=>$v['id']])}" target="_blank">{$v.detail|raw}</a>:
                                            {else/}
                                                {if $v['answer_info']['is_anonymous']}
                                                <a href="javascript:;" class="aw-username" >匿名用户</a>:
                                                {else/}
                                                <a href="{$v['answer_info']['user_info']['url']}" class="aw-username" >{$v['answer_info']['user_info']['name']}</a>:
                                                {/if}
                                                <a href="{:url('question/detail',['id'=>$v['id'],'answer'=>$v['answer_info']['id']])}" target="_blank">{$v.answer_info.content|raw}</a>
                                            {/if}
                                        </div>
                                        {/if}
                                    </div>
                                </dd>
                                <dd>
                                    {if (!$v['answer_info'])}
                                    <label>
                                        <a type="button" href="javascript:;" class="{$v['has_focus'] ? 'ygz' : 'gz'} btn btn-primary btn-sm" onclick="AWS.User.focus(this,'question','{$v.id}')">{$v['has_focus'] ? L('已关注') : L('关注 FAQ')} <span class="badge focus-count">{$v.focus_count}</span></a>
                                    </label>
                                    <label class="ml-3 mr-3"><i class="iconfont">&#xe870;</i> {$v.agree_count}</label>
                                    <label class="mr-3"><i class="iconfont">&#xe601;</i> {:L('%s 评论',$v['comment_count'])}</label>
                                    {else/}
                                    <label class="dz">
                                        <a type="button" href="javascript:;" class="btn btn-primary btn-sm aw-ajax-agree  {$v['answer_info']['vote_value']==1 ? 'active' : ''}" title="{:L('点赞回答')}" onclick="AWS.User.agree(this,'answer','{$v['answer_info']['id']}');"><i class="iconfont">&#xe870;</i>{:L('赞同')}<span class="badge">{$v['answer_info']['agree_count']}</span></a>
                                        <a type="button" href="javascript:;" class="btn btn-primary btn-sm aw-ajax-against  {$v['answer_info']['vote_value']==-1 ? 'active' : ''}" title="{:L('反对回答')}" onclick="AWS.User.against(this,'answer','{$v['answer_info']['id']}');"><i class="iconfont">&#xe68f;</i>{:L('反对')}<span class="badge"></span></a>
                                    </label>
                                    <label class="ml-3 mr-3"><i class="iconfont">&#xe625;</i>{:L('%s 人感谢',$v['answer_info']['thanks_count'])}</label>
                                    <label class="mr-3"><i class="iconfont">&#xe601;</i>{:L('%s 评论',$v['answer_info']['comment_count'])}</label>
                                    <label class="mr-3"><i class="fa fa-comment-alt"></i>{:L('%s 回复',$v['answer_count'])}</label>
                                    {/if}
                                </dd>
                            </dl>
                            {/we:question}

                            {$page|raw}
                        </div>
                    </div>
                </div>
            </div>
            <div class="aw-right radius col-md-3 px-xs-0">
                <!--侧边栏顶部钩子-->
                {:hook('sidebarTop')}

                {if $theme_config['question']['sidebar_show_items'] && in_array('write_nav',$theme_config['question']['sidebar_show_items'])}
                {:widget('sidebar/faqWriteNav')}
                {/if}

                {if $theme_config['question']['sidebar_show_items'] && in_array('focus_topic',$theme_config['question']['sidebar_show_items'])}
                {:widget('sidebar/focusTopic',['uid'=>$user_id])}
                {/if}

                {if $theme_config['question']['sidebar_show_items'] && in_array('hot_topic',$theme_config['question']['sidebar_show_items'])}
                {:widget('sidebar/hotTopic',['uid'=>$user_id])}
                {/if}

                {if $theme_config['question']['sidebar_show_items'] && in_array('column',$theme_config['question']['sidebar_show_items'])}
                {:widget('sidebar/hotColumn',['uid'=>$user_id,'sort'=>'hot'])}
                {/if}

                {if $theme_config['question']['sidebar_show_items'] && in_array('hot_users',$theme_config['question']['sidebar_show_items'])}
                {:widget('sidebar/hotUsers',['uid'=>$user_id])}
                {/if}

                <!--侧边栏底部钩子-->
                {:hook('sidebarBottom')}
            </div>
        </div>
    </div>
</div>
{/block}
