{if !empty($list)}
{foreach $list as $key=>$v}

<!--自定义内容列表页拓展钩子,可自定义内容页插入内如，如每多少条内容显示一条广告-->
{:hook('postsListsExtend',['info'=>$v,'key'=>$key,'type'=>'index'])}

{if($v['item_type']=="question")}

<div class="aui-card js-analytics-impression" style="background: none" data-analytics-type="question" data-analytics-id="{$v['id']}" data-analytics-list="mobile_feed" data-analytics-position="{$key + 1}" data-analytics-source="mobile_feed">
    <div class="aui-card-main pb-1 px-0 pt-0">
        <div class="aui-card aui-card-image">
            <div class="aui-card-main">
                <div class="aui-card-title pt-3 bg-white px-2 clearfix position-relative pb-2">
                    <div class="float-left">
                        {if (!$v['answer_info'])}
                        {if $v.is_anonymous}
                        <a href="javascript:;" class="aw-username">
                            <img src="static/common/image/default-avatar.svg" width="40" height="40" class="aw-user-img circle" alt="{:L('匿名用户')}" loading="lazy" decoding="async">
                        </a>
                        {else/}
                        <a href="{$v['user_info']['url']}" class="aw-user-name" data-id="{$v['user_info']['uid']}">
                            <img src="{$v['user_info']['avatar']}" width="40" height="40" onerror="this.src='static/common/image/default-avatar.svg'" class="circle" alt="{$v['user_info']['name']}" loading="lazy" decoding="async">
                        </a>
                        {/if}
                        {else/}
                        {if $v['answer_info']['is_anonymous']}
                        <a href="javascript:;" class="aw-username" >
                            <img src="static/common/image/default-avatar.svg" width="40" height="40" class="aw-user-img circle" alt="{:L('匿名用户')}" loading="lazy" decoding="async">
                        </a>
                        {else/}
                        <a href="{$v['answer_info']['user_info']['url']}" data-id="{$v['answer_info']['uid']}" class="aw-user-name">
                            <img src="{$v['answer_info']['user_info']['avatar']}" width="40" height="40" onerror="this.src='static/common/image/default-avatar.svg'" class="circle" alt="{$v['answer_info']['user_info']['name']}" loading="lazy" decoding="async">
                        </a>
                        {/if}
                        {/if}
                    </div>
                    <div class="float-left ml-2">
                        {if (!$v['answer_info'])}
                        {if $v.is_anonymous}
                        <a href="javascript:;" class="aw-username">{:L('匿名用户')}</a>
                        {else/}
                        <a href="{$v['user_info']['url']}" class="aw-user-name" data-id="{$v['user_info']['uid']}">
                            {$v['user_info']['name']}
                        </a>
                        {/if}
                        <span class="d-block text-muted font-9">{:L('补充了 FAQ')}</span>
                        <em class="time position-absolute" style="right: 0.5rem;top: 1rem;">{:date_friendly($v['update_time'])}</em>
                        {else/}
                        {if $v['answer_info']['is_anonymous']}
                        <a href="javascript:;" class="aw-username" >
                            {:L('匿名用户')}
                        </a>
                        {else/}
                        <a href="{$v['answer_info']['user_info']['url']}" data-id="{$v['answer_info']['uid']}" class="aw-user-name">
                            {$v['answer_info']['user_info']['name']}
                        </a>
                        {/if}
                        <span class="d-block text-muted font-9">{:L('更新了 FAQ 答案')}</span>
                        <em class="time position-absolute" style="right: 0.5rem;top: 1rem;">{:date_friendly($v['answer_info']['create_time'])}</em>
                        {/if}
                    </div>
                </div>
                <div class="img" style="min-height: 24px;height: auto">
                    <div class="img-mask font-weight-bold aw-one-line" style="background: none;height: auto;line-height: unset">
                        <span class="tip-s1 badge badge-secondary">{:L('FAQ')}</span>
                        {if $v.set_top}
                        <span class="tip-d badge badge-secondary">{:L('顶')}</span>
                        {/if}
                        {if (!$v['answer_info'])}
                        <a href="{:url('question/detail',['id'=>$v['id']])}" class="js-analytics-click" data-analytics-type="question" data-analytics-id="{$v['id']}" data-analytics-list="mobile_feed" data-analytics-position="{$key + 1}" data-analytics-source="mobile_feed">{$v.title|raw}</a>
                        {else/}
                        <a href="{:url('question/detail',['id'=>$v['id'],'answer'=>$v['answer_info']['id']])}" class="js-analytics-click" data-analytics-type="question" data-analytics-id="{$v['id']}" data-analytics-list="mobile_feed" data-analytics-position="{$key + 1}" data-analytics-source="mobile_feed">{$v.title|raw}</a>
                        {/if}
                    </div>
                </div>
                <div class="desc">
                    <div class="text-muted font-9 mb-1">{:L('沉淀高频问题、明确答案和后续补充说明，作为知识系统的检索入口。')}</div>
                    {if $v['img_list'] && get_theme_setting('common.list_show_image')=='Y'}
                    {if count($v['img_list'])>1}
                    <div class="d-flex aw-list-img">
                        {volist name="$v['img_list']" id="img" key="k"}
                        {if($k<4)}
                        <img src="{$img|default='/static/common/image/default-cover.svg'}" class="flex-fill mx-1 rounded aw-cut-img" style="margin-bottom: 5px;max-width: 33.3%;border-radius: 5px" loading="lazy" decoding="async">
                        {/if}
                        {/volist}
                    </div>
                    {else/}
                    <div class="aw-list-img" style="height: auto;max-height: 200px;">
                        <img src="{$v['img_list'][0]|default='/static/common/image/default-cover.svg'}" class="rounded aw-cut-img w-100" style="margin-bottom: 5px;" loading="lazy" decoding="async">
                    </div>
                     {/if}
                    {/if}
                    <div class="aw-content aw-two-line text-muted font-9">
                        {$v.detail|raw}
                    </div>
                </div>
            </div>
            <div class="aui-card-down row-before">
                {if (!$v['answer_info'])}
                <div class="aui-btn dz">
                    <a href="javascript:;" class="text-muted aw-ajax-agree {$v['vote_value']==1 ? 'active' : ''}" onclick="AWS_MOBILE.User.agree(this,'question','{$v.id}');">
                        <i class="iconfont iconpraise"></i><span>{$v.agree_count}</span>
                    </a>
                </div>
                <div class="aui-btn"><i class="iconfont iconmessage"></i><span>{$v['answer_count']}</span></div>
                <div class="aui-btn" onclick="AWS_MOBILE.User.shareBox('{$v.title}','{:url('question/detail',['id'=>$v.id],true,true)}')"><i class="iconfont iconshare"></i>{:L('分享 FAQ')}</div>
                {else/}
                <div class="aui-btn dz">
                    <a href="javascript:;" class="text-muted aw-ajax-agree {$v['answer_info']['vote_value']==1 ? 'active' : ''}" title="{:L('点赞回答')}" onclick="AWS_MOBILE.User.agree(this,'answer','{$v['answer_info']['id']}');">
                        <i class="iconfont iconpraise"></i><span>{$v['answer_info']['agree_count']}</span>
                    </a>
                </div>
                <div class="aui-btn"><i class="iconfont iconmessage"></i><span>{$v['answer_info']['comment_count']}</span></div>
                <div class="aui-btn" onclick="AWS_MOBILE.User.shareBox('{$v.title}','{:url('question/detail',['id'=>$v.id],true,true)}')"><i class="iconfont iconshare"></i>{:L('分享补充')}</div>
                {/if}
            </div>
        </div>
    </div>
</div>

{elseif($v['item_type']=="answer")}
<div class="aui-card js-analytics-impression" style="background: none" data-analytics-type="question" data-analytics-id="{$v['id']}" data-analytics-list="mobile_feed" data-analytics-position="{$key + 1}" data-analytics-source="mobile_answer_feed">
    <div class="aui-card-main pb-1 px-0 pt-0">
        <div class="aui-card aui-card-image">
            <div class="aui-card-main">
                <div class="aui-card-title pt-3 bg-white px-2 clearfix position-relative pb-2">
                    <div class="float-left">
                        {if (!$v['answer_info'])}
                        {if $v.is_anonymous}
                        <a href="javascript:;" class="aw-username">
                            <img src="static/common/image/default-avatar.svg" width="40" height="40" class="aw-user-img circle" alt="{:L('匿名用户')}" loading="lazy" decoding="async">
                        </a>
                        {else/}
                        <a href="{$v['user_info']['url']}" class="aw-user-name" data-id="{$v['user_info']['uid']}">
                            <img src="{$v['user_info']['avatar']}" width="40" height="40" onerror="this.src='static/common/image/default-avatar.svg'" class="circle" alt="{$v['user_info']['name']}" loading="lazy" decoding="async">
                        </a>
                        {/if}
                        {else/}
                        {if $v['answer_info']['is_anonymous']}
                        <a href="javascript:;" class="aw-username" >
                            <img src="static/common/image/default-avatar.svg" width="40" height="40" class="aw-user-img circle" alt="{:L('匿名用户')}" loading="lazy" decoding="async">
                        </a>
                        {else/}
                        <a href="{$v['answer_info']['user_info']['url']}" data-id="{$v['answer_info']['uid']}" class="aw-user-name">
                            <img src="{$v['answer_info']['user_info']['avatar']}" width="40" height="40" onerror="this.src='static/common/image/default-avatar.svg'" class="circle" alt="{$v['answer_info']['user_info']['name']}" loading="lazy" decoding="async">
                        </a>
                        {/if}
                        {/if}
                    </div>
                    <div class="float-left ml-2">
                        {if (!$v['answer_info'])}
                        {if $v.is_anonymous}
                        <a href="javascript:;" class="aw-username">{:L('匿名用户')}</a>
                        {else/}
                        <a href="{$v['user_info']['url']}" class="aw-user-name" data-id="{$v['user_info']['uid']}">
                            {$v['user_info']['name']}
                        </a>
                        {/if}
                        <span class="d-block text-muted font-9">{:L('补充了 FAQ')}</span>
                        <em class="time position-absolute" style="right: 0.5rem;top: 1rem;">{:date_friendly($v['update_time'])}</em>
                        {else/}
                        {if $v['answer_info']['is_anonymous']}
                        <a href="javascript:;" class="aw-username" >
                            {:L('匿名用户')}
                        </a>
                        {else/}
                        <a href="{$v['answer_info']['user_info']['url']}" data-id="{$v['answer_info']['uid']}" class="aw-user-name">
                            {$v['answer_info']['user_info']['name']}
                        </a>
                        {/if}
                        <span class="d-block text-muted font-9">{:L('更新了 FAQ 答案')}</span>
                        <em class="time position-absolute" style="right: 0.5rem;top: 1rem;">{:date_friendly($v['answer_info']['create_time'])}</em>
                        {/if}
                    </div>
                </div>
                <div class="img" style="min-height: 24px;height: auto">
                    <div class="img-mask font-weight-bold aw-one-line" style="background: none;height: auto;line-height: unset">
                        <span class="tip-s1 badge badge-secondary">{:L('FAQ')}</span>
                        {if $v.set_top}
                        <span class="tip-d badge badge-secondary">{:L('顶')}</span>
                        {/if}
                        {if (!$v['answer_info'])}
                        <a href="{:url('question/detail',['id'=>$v['id']])}" class="js-analytics-click" data-analytics-type="question" data-analytics-id="{$v['id']}" data-analytics-list="mobile_feed" data-analytics-position="{$key + 1}" data-analytics-source="mobile_answer_feed">{$v.title|raw}</a>
                        {else/}
                        <a href="{:url('question/detail',['id'=>$v['id'],'answer'=>$v['answer_info']['id']])}" class="js-analytics-click" data-analytics-type="question" data-analytics-id="{$v['id']}" data-analytics-list="mobile_feed" data-analytics-position="{$key + 1}" data-analytics-source="mobile_answer_feed">{$v.title|raw}</a>
                        {/if}
                    </div>
                </div>
                <div class="desc">
                    <div class="text-muted font-9 mb-1">{:L('沉淀高频问题、明确答案和后续补充说明，作为知识系统的检索入口。')}</div>
                    {if $v['img_list'] && get_theme_setting('common.list_show_image')=='Y'}
                    {if count($v['img_list'])>1}
                    <div class="d-flex aw-list-img">
                        {volist name="$v['img_list']" id="img" key="k"}
                        {if($k<4)}
                        <img src="{$img|default='/static/common/image/default-cover.svg'}" class="flex-fill mx-1 rounded aw-cut-img" style="margin-bottom: 5px;max-width: 33.3%;border-radius: 5px" loading="lazy" decoding="async">
                        {/if}
                        {/volist}
                    </div>
                    {else/}
                    <div class="aw-list-img" style="height: auto;max-height: 200px;">
                        <img src="{$v['img_list'][0]|default='/static/common/image/default-cover.svg'}" class="rounded aw-cut-img w-100" style="margin-bottom: 5px;" loading="lazy" decoding="async">
                    </div>
                    {/if}
                    {/if}
                    <div class="aw-content aw-two-line text-muted font-9">
                        {$v.detail|raw}
                    </div>
                </div>
            </div>
            <div class="aui-card-down row-before">
                {if (!$v['answer_info'])}
                <div class="aui-btn dz">
                    <a href="javascript:;" class="text-muted aw-ajax-agree {$v['vote_value']==1 ? 'active' : ''}" onclick="AWS_MOBILE.User.agree(this,'question','{$v.id}');">
                        <i class="iconfont iconpraise"></i><span>{$v.agree_count}</span>
                    </a>
                </div>
                <div class="aui-btn"><i class="iconfont iconmessage"></i><span>{$v['answer_count']}</span></div>
                <div class="aui-btn" onclick="AWS_MOBILE.User.shareBox('{$v.title}','{:url('question/detail',['id'=>$v.id],true,true)}')"><i class="iconfont iconshare"></i>{:L('分享 FAQ')}</div>
                {else/}
                <div class="aui-btn dz">
                    <a href="javascript:;" class="text-muted aw-ajax-agree {$v['answer_info']['vote_value']==1 ? 'active' : ''}" title="{:L('点赞回答')}" onclick="AWS_MOBILE.User.agree(this,'answer','{$v['answer_info']['id']}');">
                        <i class="iconfont iconpraise"></i><span>{$v['answer_info']['agree_count']}</span>
                    </a>
                </div>
                <div class="aui-btn"><i class="iconfont iconmessage"></i><span>{$v['answer_info']['comment_count']}</span></div>
                <div class="aui-btn" onclick="AWS_MOBILE.User.shareBox('{$v.title}','{:url('question/detail',['id'=>$v.id],true,true)}')"><i class="iconfont iconshare"></i>{:L('分享补充')}</div>
                {/if}
            </div>
        </div>
    </div>
</div>

{elseif($v['item_type']=="article")}
<div class="aui-card js-analytics-impression" style="background: none" data-analytics-type="article" data-analytics-id="{$v['id']}" data-analytics-list="mobile_feed" data-analytics-position="{$key + 1}" data-analytics-source="mobile_feed">
    <div class="aui-card-main pb-1 px-0 pt-0">
        <div class="aui-card aui-card-image">
            <div class="aui-card-main">
                <div class="aui-card-title pt-3 bg-white px-2 clearfix position-relative pb-2">
                    <div class="float-left">
                        <a href="{$v['user_info']['url']}"  class="aw-user-name" data-id="{$v['user_info']['uid']}" >
                            <img src="{$v['user_info']['avatar']}" width="40" height="40" onerror="this.src='/static/common/image/default-avatar.svg'" class="circle" alt="{$v['user_info']['name']}" loading="lazy" decoding="async">
                        </a>
                    </div>
                    <div class="float-left ml-2">
                        <a href="{$v['user_info']['url']}" class="aw-user-name" data-id="{$v['user_info']['uid']}" >
                            {$v['user_info']['name']}
                        </a>
                        <span class="d-block text-muted font-9">发布了{:frelink_article_type_label(isset($v['article_type']) ? $v['article_type'] : 'normal')}</span>
                        <em class="time position-absolute" style="right: 0.5rem;top: 1rem;">{:date_friendly($v['create_time'])}</em>
                    </div>
                </div>
                <div class="img" style="min-height: 24px;height: auto">
                    <div class="img-mask font-weight-bold aw-one-line" style="background: none;height: auto;line-height: unset">
                        <span class="tip-s2 badge badge-secondary">{:frelink_article_type_label(isset($v['article_type']) ? $v['article_type'] : 'normal')}</span>
                        {:hook('article_badge',$v)}
                        {if $v.set_top}
                        <span class="tip-d badge badge-secondary">{:L('顶')}</span>
                        {/if}
                        <a href="{:url('article/detail',['id'=>$v['id']])}" class="js-analytics-click" data-analytics-type="article" data-analytics-id="{$v['id']}" data-analytics-list="mobile_feed" data-analytics-position="{$key + 1}" data-analytics-source="mobile_feed">{$v['title']|raw}</a>
                    </div>
                </div>
                <div class="desc">
                    <div class="text-muted font-9 mb-1">
                        {if isset($v['article_type']) && $v['article_type']=='research'}
                        {:L('系统整理资料脉络、核心分歧和阶段性结论。')}
                        {elseif isset($v['article_type']) && $v['article_type']=='fragment'/}
                        {:L('保留观察、判断和仍在形成中的思考线索。')}
                        {elseif isset($v['article_type']) && $v['article_type']=='faq'/}
                        {:L('适合快速检索和复用的高频问题答案。')}
                        {else/}
                        {:L('用完整展开的方式解释热点、案例和方法。')}
                        {/if}
                    </div>
                    {if ($v['img_list'] || $v.cover) && get_theme_setting('common.list_show_image')=='Y'}
                    <div class="aw-list-img mb-1" style="max-height: 180px;height: auto">
                        <img src="{$v.cover|default=$v['img_list'][0]}" class="rounded aw-cut-img" alt="{$v['title']}" width="100%" loading="lazy" decoding="async">
                    </div>
                    {/if}
                    <div class="aw-content aw-two-line text-muted font-9">
                        {$v.message|raw}
                    </div>
                </div>
            </div>
            <div class="aui-card-down row-before">
                <div class="aui-btn dz">
                    <a href="javascript:;" class="text-muted aw-ajax-agree {$v['vote_value']==1 ? 'active' : ''}" onclick="AWS_MOBILE.User.agree(this,'article','{$v['id']}');">
                        <i class="iconfont iconpraise"></i><span>{$v.agree_count}</span>
                    </a>
                </div>
                <div class="aui-btn"><i class="iconfont iconmessage"></i><span>{$v['comment_count']}</span></div>
                <div class="aui-btn"><i class="si si-eye"></i> {$v['view_count']?:''}</div>
            </div>
        </div>
    </div>
</div>
{else/}
<!--自定义内容类型钩子-->
{:hook('widget_posts_'.$v['item_type'],['item_id'=>$v['item_id']])}
{/if}

{/foreach}
{/if}
