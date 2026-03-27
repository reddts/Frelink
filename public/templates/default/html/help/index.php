{extend name="$theme_block" /}
{block name="header"}
<div class="w-top-img" style="height:300px;background: url('{$static_url}images/help.png') center center;background-size:cover;">
    <div style="background: rgba(0,0,0,.25);height:300px;">
    {include file="global/nav"}
    </div>
</div>
{/block}
{block name="main"}
<div class="container" style="margin-top: -100px">
    <div class="bg-white p-3 aw-content-shell help-shell" style="border-radius: 10px">
        <div class="text-center py-3">
            <h2 class="col-12 font-weight-bolder aw-content-title">{:L('知识地图与公开知识文档')}</h2>
            <p class="mb-3 w-100 text-muted mt-3 help-summary-inline">{:L('把 FAQ、术语解释、研究资料和规则说明组织成长期可检索的知识地图。')}</p>
        </div>
        <div class="row text-center pb-3">
            <div class="col-md-3 col-6 mb-2">
                <div class="border rounded py-3">
                    <div class="font-weight-bold font-16">{$map_summary.chapter_count|default=0}</div>
                    <div class="text-muted font-12">{:L('知识章节')}</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="border rounded py-3">
                    <div class="font-weight-bold font-16">{$map_summary.relation_count|default=0}</div>
                    <div class="text-muted font-12">{:L('已归档内容')}</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="border rounded py-3">
                    <div class="font-weight-bold font-16">{$map_summary.question_count|default=0}</div>
                    <div class="text-muted font-12">{:L('FAQ 条目')}</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="border rounded py-3">
                    <div class="font-weight-bold font-16">{$map_summary.article_count|default=0}</div>
                    <div class="text-muted font-12">{:L('知识内容')}</div>
                </div>
            </div>
        </div>
        <div class="row pb-3">
            <div class="col-md-8 mb-2">
                <div class="border rounded h-100 p-3" style="background:linear-gradient(180deg,#f7fbff 0%,#f3f8fc 100%);border-color:#e4edf4 !important;">
                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                        <div class="mb-2 mr-3">
                            <div class="font-weight-bold text-dark mb-2">{:L('这张知识地图怎么用')}</div>
                            <div class="text-muted font-12 mb-2">{:L('先从章节进入，再沿着相关主题和归档内容继续追踪，这里承接的是长期可维护的知识结构，而不是一次性信息流。')}</div>
                        </div>
                        <div class="text-muted font-12 mb-2">{:L('已连接主题')} {$map_summary.topic_count|default=0}</div>
                    </div>
                    <div class="d-flex flex-wrap">
                        <span class="badge badge-light border mr-2 mb-2 px-3 py-2">{:L('先看章节结构')}</span>
                        <span class="badge badge-light border mr-2 mb-2 px-3 py-2">{:L('再找相关主题')}</span>
                        <span class="badge badge-light border mr-2 mb-2 px-3 py-2">{:L('继续顺着 FAQ 和知识内容往下读')}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="border rounded h-100 p-3">
                    <div class="font-weight-bold text-dark mb-2">{:L('长期主题连接')}</div>
                    <div class="text-muted font-12">{:L('这些主题已经和知识章节建立了真实连接，可直接作为长期追踪入口。')}</div>
                </div>
                <div class="border rounded h-100 p-3 mt-2">
                    <div class="font-weight-bold text-dark mb-2">{:L('API 接口文档')}</div>
                    <div class="text-muted font-12 mb-3">{:L('接口说明由代码自动生成，适合给 agent、前端调用和运维排查直接查看。')}</div>
                    <div class="d-flex flex-wrap">
                        <a href="{:url('help/api')}" class="btn btn-sm btn-primary mr-2 mb-2" target="_blank">{:L('查看 API 文档')}</a>
                        <a href="{$baseUrl}/docs/api-v1.openapi.json" class="btn btn-sm btn-outline-primary mb-2" target="_blank" download>{:L('下载 OpenAPI')}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 pb-3">
            <form action="{:url('search/index')}" method="get" id="homeSearch">
                <div class="w-100 px-3 py-1" style="background: #eee;border-radius: 50px">
                    <i class="iconfont">&#xe610;</i>
                    <input type="text" autocomplete="off" style="width: 96%;height: 32px;line-height: 32px;" value="{:input('get.q')}"  name="q" placeholder="{:L('请输入你遇到的问题进行搜索')}">
                </div>
            </form>
        </div>
    </div>
</div>
<div class="container mt-2 aw-wrap" id="tabMain">
    {if !empty($topic_connections)}
    <div class="bg-white p-3 aw-content-shell help-list-shell mb-2" style="border-radius: 10px">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
            <div>
                <h4 class="mb-1 font-weight-bold">{:L('长期主题连接')}</h4>
                <p class="text-muted font-12 mb-0">{:L('优先展示已经和知识地图形成真实关系的主题，方便从主题进入章节，再继续找到归档内容。')}</p>
            </div>
            <a href="{:url('topic/index')}" class="text-primary">{:L('查看全部')}</a>
        </div>
        <div class="row">
            {foreach $topic_connections as $topic}
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="border rounded h-100 p-3">
                    <a href="{:url('topic/detail',['id'=>$topic['id']])}" target="_blank" class="text-dark font-weight-bold d-block mb-2">{$topic.title}</a>
                    <div class="text-muted font-12 mb-2">{:L('已连接章节')} {$topic.chapter_count|default=0} · {:L('已归档内容')} {$topic.matched_count|default=0}</div>
                    {if !empty($topic['chapters'])}
                    <div class="d-flex flex-wrap">
                        {foreach $topic['chapters'] as $chapter}
                        <a href="{:url('help/detail',['token'=>$chapter['url_token']])}" class="badge badge-light border mr-2 mb-2 px-2 py-2 text-dark">{$chapter.title}</a>
                        {/foreach}
                    </div>
                    {/if}
                </div>
            </div>
            {/foreach}
        </div>
    </div>
    {/if}
    {if $list}
    <div class="row">
        {foreach $list as $k=>$v}
        <div class="col-md-3">
            <div class="bg-white p-3 aw-content-shell help-card" style="border-radius: 10px">
                <div class="d-flex pb-2">
                    {if $v.image}
                    <div class="flex-fill mr-2" style="background-image:url({$v.image});background-size:cover;width: 1.5rem;max-width:1.5rem;height: 1.5rem;border-radius: 5px;"></div>
                    {/if}
                    <h4 class="aw-one-line font-weight-bolder font-13"><a href="{:url('help/detail',['token'=>$v.url_token])}" data-pjax="wrapMain">{$v.title}</a></h4>
                </div>
                {if !empty($v.description)}
                <p class="text-muted font-12 mb-2">{:str_cut(strip_tags((string)$v['description']),0,52)}</p>
                {/if}
                <div class="d-flex flex-wrap mb-2">
                    <span class="badge badge-light border mr-2 mb-2 px-2 py-2">{:L('FAQ 条目')} {$v.question_count|default=0}</span>
                    <span class="badge badge-light border mr-2 mb-2 px-2 py-2">{:L('知识内容')} {$v.article_count|default=0}</span>
                    <span class="badge badge-light border mr-2 mb-2 px-2 py-2">{:L('相关主题')} {$v.topic_count|default=0}</span>
                </div>
                {if !empty($v.related_topics)}
                <div class="mb-2">
                    {foreach $v.related_topics as $topic}
                    <a href="{:url('topic/detail',['id'=>$topic['id']])}" target="_blank" class="badge badge-light border mr-2 mb-2 px-2 py-2 text-dark">{$topic.title}</a>
                    {/foreach}
                </div>
                {/if}
                {if isset($v.chapters) && $v.chapters}
                <ul class="pb-2">
                    {foreach $v['chapters'] as $k1=>$v1}
                    <li class="py-1 aw-one-line"><a href="{:url($v1['item_type'].'/detail',['id'=>$v1['item_id']])}" target="_blank">{$v1.info.title}</a></li>
                    {/foreach}
                </ul>
                {/if}
                <div class="text-muted font-12 mb-2">
                    {if $v.question_count>0 && $v.article_count>0}
                    {:L('这个章节同时覆盖 FAQ 和知识内容，更适合做长期主题容器。')}
                    {elseif $v.question_count>0/}
                    {:L('这个章节当前以 FAQ 为主，适合作为答案入口继续沉淀。')}
                    {elseif $v.article_count>0/}
                    {:L('这个章节当前以知识内容为主，适合作为综述和观察的长期归档位。')}
                    {else/}
                    {:L('这个章节还在起步阶段，后续可以继续补 FAQ、综述或观察内容。')}
                    {/if}
                </div>
                <div class="text-center border-top pt-3">
                    <a href="{:url('help/detail',['token'=>$v.url_token])}" data-pjax="wrapMain" class="text-primary">{:L('查看全部')}</a>
                </div>
            </div>
        </div>
        {/foreach}
    </div>
    {$page|raw}
    {else/}
    <p class="text-center py-3 text-muted">
        <img src="{$cdnUrl}/static/common/image/empty.svg">
        <span class="d-block">{:L('暂无内容')}</span>
    </p>

    {/if}
</div>
{/block}
