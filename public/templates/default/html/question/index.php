{extend name="$theme_block" /}
{block name="style"}
<style>
    .aw-faq-hero {
        padding: 20px 24px 10px;
        border-bottom: 1px solid #eef2f7;
        background: linear-gradient(180deg, #fbfdff 0%, #fff 100%);
    }
    .aw-faq-hero h1 {
        margin: 0 0 8px;
        font-size: 24px;
        font-weight: 700;
        color: #0f172a;
    }
    .aw-faq-hero p {
        margin: 0;
        color: #64748b;
        line-height: 1.7;
    }
    .aw-faq-lanes {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        padding: 14px 24px 18px;
        border-bottom: 1px solid #eef2f7;
        background: #fff;
    }
    .aw-faq-lane {
        display: block;
        padding: 14px;
        border: 1px solid #e5edf6;
        border-radius: 14px;
        background: #fbfdff;
        color: #0f172a;
    }
    .aw-faq-lane:hover {
        text-decoration: none;
        transform: translateY(-1px);
        transition: all .2s ease;
    }
    .aw-faq-lane strong {
        display: block;
        margin-bottom: 6px;
        font-size: 15px;
    }
    .aw-faq-lane span {
        display: block;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
    }
    @media (max-width: 991.98px) {
        .aw-faq-lanes {
            grid-template-columns: 1fr;
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
                    <h1>{:L('FAQ')}</h1>
                    <p>{:L('这里承接高频 FAQ、明确答案和可复用解释。它不再是社区问答流，而是公开知识系统里的 FAQ 入口。')}</p>
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
                <nav class="nav nav-tabs aw-pjax-a px-4" aria-label="{:L('FAQ 列表筛选')}">
                    <a class="nav-item nav-link {if $sort=='recommend'}active{/if}" data-pjax="wrapMain" href="{:url('question/index',['sort'=>'recommend','category_id'=>$category])}" {if $sort=='recommend'}aria-current="page"{/if}>{:L('精选')}</a>
                    <a class="nav-item nav-link {if $sort=='new'}active{/if}" data-pjax="wrapMain" href="{:url('question/index',['sort'=>'new','category_id'=>$category])}" {if $sort=='new'}aria-current="page"{/if}>{:L('更新')}</a>
                    <a class="nav-item nav-link {if $sort=='hot'}active{/if}" data-pjax="wrapMain" href="{:url('question/index',['sort'=>'hot','category_id'=>$category])}" {if $sort=='hot'}aria-current="page"{/if}>{:L('高关注')}</a>
                    <a class="nav-item nav-link {if $sort=='unresponsive'}active{/if}" data-pjax="wrapMain" href="{:url('question/index',['sort'=>'unresponsive','category_id'=>$category])}" {if $sort=='unresponsive'}aria-current="page"{/if}>{:L('待补充 FAQ')}</a>
                </nav>

                <div id="tabMain" class="tab-content" >
                    <div class="tab-pane fade show active">
                        <div class="aw-common-list">
                            {we:question sort="$sort" category_id="$category"}
                            {:hook('postsListsExtend',['info'=>$v,'key'=>$key,'type'=>'question'])}
                            <dl class="js-analytics-impression" data-analytics-type="question" data-analytics-id="{$v['id']}" data-analytics-list="question_index" data-analytics-position="{$key + 1}" data-analytics-source="desktop_question_index">
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
                                    <div class="text-muted mb-2" style="font-size: 13px;">{:L('优先沉淀高频 FAQ、明确答案和后续补充说明。')}</div>
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
                {:widget('sidebar/writeNav')}
                {/if}

                {if $theme_config['question']['sidebar_show_items'] && in_array('announce',$theme_config['question']['sidebar_show_items'])}
                {:widget('sidebar/announce')}
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

                {if $theme_config['question']['sidebar_show_items'] && in_array('diy_content',$theme_config['question']['sidebar_show_items'])}
                {$theme_config['question']['sidebar_diy_content']|raw|htmlspecialchars_decode}
                {/if}

                <!--侧边栏底部钩子-->
                {:hook('sidebarBottom')}
            </div>
        </div>
    </div>
</div>
{/block}
