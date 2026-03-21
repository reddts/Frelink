{extend name="block" /}
{block name="main"}
{:hook('backendIndexPage')}
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="layui-tab layui-tab-card">
                <ul class="layui-tab-title">
                    <li class="layui-this">数据总览</li>
                    <li>财务概况</li>
                    <li>用户数据</li>
                    <li>运营洞察</li>
                </ul>
                <div class="layui-tab-content">
                    <div class="layui-tab-item layui-show">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="layui-inline echart-date">
                                    <div class="layui-input-inline">
                                        <input type="text" class="layui-input date-area" id="user-date-area" name="date_area" placeholder="日期区间" value="" readonly style="width: 260px">
                                    </div>
                                </div>
                                <div id="user-main" style="width: 100%;height:400px;"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="layui-inline echart-date">
                                    <div class="layui-input-inline">
                                        <input type="text" class="layui-input date-area" id="question-date-area" name="date_area" placeholder="日期区间" value="" readonly style="width: 260px">
                                    </div>
                                </div>
                                <div id="content-main" style="width: 100%;height:400px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-tab-item"><div class="text-warning pt-3 pb-3">各模块的收入({:get_setting("score_unit")}、金额)</div></div>
                    <div class="layui-tab-item">
                        <table class="col-md-6 table table-hover">
                            <tbody>
                            <tr>
                                <td>会员总数</td>
                                <td>{$usersInfo.users_count}</td>
                                <td>已激活会员</td>
                                <td>{$usersInfo.users_valid_email_count}</td>
                            </tr>
                            <tr>
                                <td>文章总数</td>
                                <td>{$usersInfo.article_count}</td>
                                <td >专栏总数</td>
                                <td>{$usersInfo.column_count}</td>
                            </tr>
                            <tr>
                                <td>问题总数</td>
                                <td>{$usersInfo.question_count}</td>
                                <td>待回答问题</td>
                                <td>{$usersInfo.no_answer_count}</td>
                            </tr>
                            <tr>
                                <td>回答总数</td>
                                <td>{$usersInfo.answer_count}</td>
                                <td>最佳答案数量</td>
                                <td>{$usersInfo.best_answer_count}</td>
                            </tr>
                            <tr>
                                <td>待审核问题</td>
                                <td>{$usersInfo.approval_question_count}</td>
                                <td>待审核回答</td>
                                <td>{$usersInfo.approval_answer_count}</td>
                            </tr>
                            <tr>
                                <td>话题总数</td>
                                <td>{$usersInfo.topic_count}</td>
                                <td>附件总数</td>
                                <td>{$usersInfo.attach_count}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="layui-tab-item">
                        <div class="d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center mb-3">
                            <div>
                                <h5 class="mb-1">最近 {$insight.window_days} 天运营洞察</h5>
                                <p class="text-muted mb-0">仅统计最近窗口，不采用全站累计点击率。</p>
                            </div>
                            <div class="d-flex flex-column flex-lg-row align-items-lg-center mt-3 mt-lg-0">
                                <div class="btn-group btn-group-sm mr-lg-2 mb-2 mb-lg-0" role="group" aria-label="时间窗口">
                                    <a href="{:url('index/index',['insight_days'=>1])}" class="btn {$insight.window_days==1 ? 'btn-primary' : 'btn-outline-primary'}">1天</a>
                                    <a href="{:url('index/index',['insight_days'=>3])}" class="btn {$insight.window_days==3 ? 'btn-primary' : 'btn-outline-primary'}">3天</a>
                                    <a href="{:url('index/index',['insight_days'=>7])}" class="btn {$insight.window_days==7 ? 'btn-primary' : 'btn-outline-primary'}">7天</a>
                                    <a href="{:url('index/index',['insight_days'=>30])}" class="btn {$insight.window_days==30 ? 'btn-primary' : 'btn-outline-primary'}">30天</a>
                                </div>
                                <div class="btn-group btn-group-sm" role="group" aria-label="导出简报">
                                    <a href="{:url('index/insightBrief',['days'=>$insight.window_days,'format'=>'markdown'])}" target="_blank" class="btn btn-outline-secondary">导出 Markdown</a>
                                    <a href="{:url('index/insightBrief',['days'=>$insight.window_days,'format'=>'json'])}" target="_blank" class="btn btn-outline-secondary">导出 JSON</a>
                                </div>
                            </div>
                        </div>

                        {if !$insight.enabled}
                        <div class="alert alert-warning mb-3">
                            运营洞察表尚未安装，请先执行 <code>docs/analytics-upgrade.sql</code>。
                        </div>
                        {elseif $insight.error}
                        <div class="alert alert-danger mb-3">
                            运营洞察加载失败：{$insight.error}
                        </div>
                        {else/}
                        <div class="row">
                            <div class="col-md-3 col-sm-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>{$insight.summary.search_count|default=0}</h3>
                                        <p>搜索次数</p>
                                    </div>
                                    <div class="icon"><i class="fas fa-search"></i></div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="small-box bg-primary">
                                    <div class="inner">
                                        <h3>{$insight.summary.impression_count|default=0}</h3>
                                        <p>曝光次数</p>
                                    </div>
                                    <div class="icon"><i class="fas fa-eye"></i></div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>{$insight.summary.click_count|default=0}</h3>
                                        <p>点击次数</p>
                                    </div>
                                    <div class="icon"><i class="fas fa-mouse-pointer"></i></div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>{$insight.summary.ctr|default=0}</h3>
                                        <p>窗口 CTR</p>
                                    </div>
                                    <div class="icon"><i class="fas fa-chart-line"></i></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">搜索缺口</h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                            <tr>
                                                <th>关键词</th>
                                                <th>搜索</th>
                                                <th>覆盖</th>
                                                <th>建议</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            {if $insight.opportunities}
                                            {volist name="insight.opportunities" id="v"}
                                            <tr>
                                                <td>{$v.keyword}</td>
                                                <td>{$v.search_count}</td>
                                                <td>{$v.matched_content_count}</td>
                                                <td class="text-muted">{$v.suggestion}</td>
                                            </tr>
                                            {/volist}
                                            {else/}
                                            <tr><td colspan="4" class="text-center text-muted">最近窗口暂无搜索缺口数据</td></tr>
                                            {/if}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Agent 执行简报</h3>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted">可直接复制给 agent，用于生成选题、补文档和专题建议。</p>
                                        <textarea class="form-control" rows="12" readonly onclick="this.select()">{$insight.agent_brief}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Agent 建议</h3>
                                    </div>
                                    <div class="card-body">
                                        {if $insight.recommendations}
                                        {volist name="insight.recommendations" id="v"}
                                        <div class="border rounded p-3 mb-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <strong>{$v.title}</strong>
                                                <span class="badge {$v.priority=='high' ? 'badge-danger' : 'badge-warning'}">{$v.priority}</span>
                                            </div>
                                            <div class="text-muted mt-2">{$v.reason}</div>
                                            <div class="mt-2">{$v.suggestion}</div>
                                        </div>
                                        {/volist}
                                        {else/}
                                        <p class="text-muted mb-0">最近窗口暂无明确运营动作建议。</p>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">内容冷启动进度</h3>
                                        <div class="card-tools">
                                            <div class="btn-group btn-group-sm" role="group" aria-label="导出执行清单">
                                                <a href="{:url('index/weeklyExecutionBrief',['days'=>$insight.window_days,'format'=>'markdown'])}" target="_blank" class="btn btn-outline-secondary">导出周报</a>
                                                <a href="{:url('index/weeklyExecutionBrief',['days'=>$insight.window_days,'format'=>'json'])}" target="_blank" class="btn btn-outline-secondary">导出 JSON</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="text-muted">以首批 `综述 / 观察 / FAQ / 帮助 / 知识章节` 的试运行目标为基线。</div>
                                            <div><strong>{$coldStart.overall_progress|default=0}%</strong>，已达标 {$coldStart.completed_targets|default=0}/{$coldStart.target_total|default=0}</div>
                                        </div>
                                        <div class="row">
                                            {foreach $coldStart.items as $v}
                                            <div class="col-md-4 col-lg-2 mb-3">
                                                <div class="border rounded p-3 h-100">
                                                    <div class="font-weight-bold">{$v.label}</div>
                                                    <div class="text-muted mt-1">{$v.current} / {$v.target}</div>
                                                    <div class="progress mt-2" style="height:8px;">
                                                        <div class="progress-bar {$v.progress>=100 ? 'bg-success' : ($v.progress>=60 ? 'bg-info' : 'bg-warning')}" role="progressbar" style="width: {$v.progress}%"></div>
                                                    </div>
                                                    <div class="mt-2 font-12 text-muted">{$v.status}，还差 {$v.gap}</div>
                                                </div>
                                            </div>
                                            {/foreach}
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-12 mb-3">
                                                <div class="font-weight-bold mb-2">本周执行清单</div>
                                                {if $weeklyExecution}
                                                <div class="row">
                                                    {volist name="weeklyExecution" id="v"}
                                                    <div class="col-md-4 mb-2">
                                                        <div class="border rounded p-3 h-100">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <strong>{$v.label}</strong>
                                                                <span class="text-muted">{$v.keyword}</span>
                                                            </div>
                                                            <div class="mt-2 font-weight-bold">{$v.title}</div>
                                                            <div class="text-muted mt-2">{$v.reason}</div>
                                                            <div class="mt-3">
                                                                <a href="{$v.primary_url}" target="_blank" class="btn btn-sm btn-primary mr-2">{$v.primary_label}</a>
                                                                <a href="{$v.secondary_url}" target="_blank" class="btn btn-sm btn-outline-secondary">{$v.secondary_label}</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {/volist}
                                                </div>
                                                {else/}
                                                <div class="text-muted">当前还没有形成明确的本周执行清单。</div>
                                                {/if}
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <div class="font-weight-bold mb-2">优先补齐建议</div>
                                                {if $coldStart.recommendations}
                                                <div class="row">
                                                    {volist name="coldStart.recommendations" id="v"}
                                                    <div class="col-md-4 mb-2">
                                                        <div class="border rounded p-3 h-100">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <strong>{$v.label}</strong>
                                                                <span class="text-muted">还差 {$v.gap}</span>
                                                            </div>
                                                            <div class="text-muted mt-2">{$v.status}，当前进度 {$v.progress}%</div>
                                                            <div class="mt-2">{$v.action}</div>
                                                            <div class="mt-3">
                                                                {if $v.primary_url}
                                                                <a href="{$v.primary_url}" target="_blank" class="btn btn-sm btn-primary mr-2">{$v.primary_label}</a>
                                                                {/if}
                                                                {if $v.secondary_url}
                                                                <a href="{$v.secondary_url}" target="_blank" class="btn btn-sm btn-outline-secondary">{$v.secondary_label}</a>
                                                                {/if}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {/volist}
                                                </div>
                                                {else/}
                                                <div class="text-muted">当前冷启动目标已全部达标。</div>
                                                {/if}
                                            </div>
                                            <div class="col-md-3">
                                                <div class="font-weight-bold mb-2">最新综述</div>
                                                {if $coldStart.recent.research}
                                                <ul class="mb-0 pl-3">
                                                    {volist name="coldStart.recent.research" id="v"}
                                                    <li><a href="{:get_url('article/detail',['id'=>$v['id']])}" target="_blank">{$v.title}</a></li>
                                                    {/volist}
                                                </ul>
                                                {else/}
                                                <div class="text-muted">暂无综述内容</div>
                                                {/if}
                                            </div>
                                            <div class="col-md-3">
                                                <div class="font-weight-bold mb-2">最新观察</div>
                                                {if $coldStart.recent.fragment}
                                                <ul class="mb-0 pl-3">
                                                    {volist name="coldStart.recent.fragment" id="v"}
                                                    <li><a href="{:get_url('article/detail',['id'=>$v['id']])}" target="_blank">{$v.title}</a></li>
                                                    {/volist}
                                                </ul>
                                                {else/}
                                                <div class="text-muted">暂无观察内容</div>
                                                {/if}
                                            </div>
                                            <div class="col-md-3">
                                                <div class="font-weight-bold mb-2">最新 FAQ</div>
                                                {if $coldStart.recent.faq}
                                                <ul class="mb-0 pl-3">
                                                    {volist name="coldStart.recent.faq" id="v"}
                                                    <li><a href="{:get_url('question/detail',['id'=>$v['id']])}" target="_blank">{$v.title}</a></li>
                                                    {/volist}
                                                </ul>
                                                {else/}
                                                <div class="text-muted">暂无 FAQ 内容</div>
                                                {/if}
                                            </div>
                                            <div class="col-md-3">
                                                <div class="font-weight-bold mb-2">最新帮助</div>
                                                {if $coldStart.recent.help}
                                                <ul class="mb-0 pl-3">
                                                    {volist name="coldStart.recent.help" id="v"}
                                                    <li><a href="{:get_url('article/detail',['id'=>$v['id']])}" target="_blank">{$v.title}</a> <span class="text-muted">({$v.article_type_label})</span></li>
                                                    {/volist}
                                                </ul>
                                                {else/}
                                                <div class="text-muted">暂无帮助内容</div>
                                                {/if}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="small-box bg-secondary">
                                    <div class="inner">
                                        <h3>{$archive.question_count|default=0}</h3>
                                        <p>未归档 FAQ</p>
                                    </div>
                                    <div class="icon"><i class="fas fa-question-circle"></i></div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="small-box bg-dark">
                                    <div class="inner">
                                        <h3>{$archive.article_count|default=0}</h3>
                                        <p>未归档内容</p>
                                    </div>
                                    <div class="icon"><i class="fas fa-archive"></i></div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>{$archiveBacklog.empty_count|default=0}</h3>
                                        <p>空知识章节</p>
                                    </div>
                                    <div class="icon"><i class="fas fa-folder-open"></i></div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>{$archiveBacklog.low_count|default=0}</h3>
                                        <p>待补充章节</p>
                                    </div>
                                    <div class="icon"><i class="fas fa-layer-group"></i></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">内容热点</h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                            <tr>
                                                <th>内容</th>
                                                <th>曝光</th>
                                                <th>点击</th>
                                                <th>阅读</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            {if $insight.content}
                                            {volist name="insight.content" id="v"}
                                            <tr>
                                                <td>
                                                    <div>{$v.title}</div>
                                                    <small class="text-muted">{$v.item_type} / CTR {$v.ctr}</small>
                                                </td>
                                                <td>{$v.impressions}</td>
                                                <td>{$v.clicks}</td>
                                                <td>{$v.detail_views}</td>
                                            </tr>
                                            {/volist}
                                            {else/}
                                            <tr><td colspan="4" class="text-center text-muted">最近窗口暂无内容热点数据</td></tr>
                                            {/if}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">主题热点</h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                            <tr>
                                                <th>主题</th>
                                                <th>阅读</th>
                                                <th>内容数</th>
                                                <th>CTR</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            {if $insight.topics}
                                            {volist name="insight.topics" id="v"}
                                            <tr>
                                                <td>
                                                    <div>{$v.title}</div>
                                                    <small class="text-muted">{$v.description|default='-'}</small>
                                                </td>
                                                <td>{$v.detail_views}</td>
                                                <td>{$v.content_count}</td>
                                                <td>{$v.ctr}</td>
                                            </tr>
                                            {/volist}
                                            {else/}
                                            <tr><td colspan="4" class="text-center text-muted">最近窗口暂无主题热点数据</td></tr>
                                            {/if}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">待归档 FAQ</h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                            <tr>
                                                <th>标题</th>
                                                <th>补充</th>
                                                <th>浏览</th>
                                                <th>更新</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            {if $archive.questions}
                                            {volist name="archive.questions" id="v"}
                                            <tr>
                                                <td>
                                                    <div><a href="{:get_url('question/detail',['id'=>$v['id']])}" target="_blank">{$v.title}</a></div>
                                                    <a href="javascript:;" class="aw-ajax-open text-primary font-12" data-title="归档 FAQ" data-url="{:url('index/archivePicker',['item_type'=>'question','item_id'=>$v['id']])}">去归档</a>
                                                </td>
                                                <td>{$v.answer_count}</td>
                                                <td>{$v.view_count}</td>
                                                <td>{:date('m-d H:i',$v.update_time)}</td>
                                            </tr>
                                            {/volist}
                                            {else/}
                                            <tr><td colspan="4" class="text-center text-muted">当前没有待归档 FAQ</td></tr>
                                            {/if}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">待归档内容</h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                            <tr>
                                                <th>标题</th>
                                                <th>类型</th>
                                                <th>浏览</th>
                                                <th>更新</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            {if $archive.articles}
                                            {volist name="archive.articles" id="v"}
                                            <tr>
                                                <td>
                                                    <div><a href="{:get_url('article/detail',['id'=>$v['id']])}" target="_blank">{$v.title}</a></div>
                                                    <a href="javascript:;" class="aw-ajax-open text-primary font-12" data-title="归档内容" data-url="{:url('index/archivePicker',['item_type'=>'article','item_id'=>$v['id']])}">去归档</a>
                                                </td>
                                                <td>{$v.article_type_label}</td>
                                                <td>{$v.view_count}</td>
                                                <td>{:date('m-d H:i',$v.update_time)}</td>
                                            </tr>
                                            {/volist}
                                            {else/}
                                            <tr><td colspan="4" class="text-center text-muted">当前没有待归档内容</td></tr>
                                            {/if}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">待整理章节</h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                            <tr>
                                                <th>章节</th>
                                                <th>内容数</th>
                                                <th>状态</th>
                                                <th>操作</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            {if $archiveBacklog.chapters}
                                            {volist name="archiveBacklog.chapters" id="v"}
                                            <tr>
                                                <td>
                                                    <div>{$v.title}</div>
                                                    <small class="text-muted">{$v.description|default='-'}</small>
                                                </td>
                                                <td>{$v.relation_count}</td>
                                                <td>{$v.backlog_label}</td>
                                                <td>
                                                    <a href="javascript:;" class="btn btn-xs btn-primary aw-ajax-open" data-title="章节候选内容" data-url="{:url('index/chapterCandidates',['chapter_id'=>$v['id']])}">看候选内容</a>
                                                    <a href="{:url('content.help/edit',['id'=>$v['id']])}" class="btn btn-xs btn-outline-primary">编辑章节</a>
                                                    <a href="{:get_url('help/detail',['token'=>$v['url_token']])}" target="_blank" class="btn btn-xs btn-outline-secondary">查看前台</a>
                                                </td>
                                            </tr>
                                            {/volist}
                                            {else/}
                                            <tr><td colspan="4" class="text-center text-muted">当前没有待整理章节</td></tr>
                                            {/if}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header">
                    <div class="card-title">系统信息</div>
                    <div class="card-tools"><span>系统环境相关配置</span></div>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <tbody>
                        <tr>
                            <td align="right">当前版本</td>
                            <td>FreCenter v{$sysInfo.version}</td>
                            <td align="right">服务器IP</td>
                            <td>{$sysInfo.ip}</td>
                        </tr>
                        <tr>
                            <td align="right">操作系统</td>
                            <td>{$sysInfo.os}</td>
                            <td align="right">服务器域名</td>
                            <td>{$sysInfo.domain}</td>
                        </tr>
                        <tr>
                            <td align="right">服务器环境</td>
                            <td>{$sysInfo.web_server}</td>
                            <td align="right">PHP 版本</td>
                            <td>{$sysInfo.php_version}</td>
                        </tr>
                        <tr>
                            <td align="right">服务器时间</td>
                            <td>{:date('Y-m-d H:i:s')}</td>
                            <td align="right">服务器时区</td>
                            <td>{$sysInfo.timezone}</td>
                        </tr>
                        <tr>
                            <td align="right">Mysql 版本</td>
                            <td>{$sysInfo.mysql_version}</td>
                            <td align="right">GD 版本</td>
                            <td>{$sysInfo.gd_info}</td>
                        </tr>
                        <tr>
                            <td align="right">上传限制</td>
                            <td>{$sysInfo.file_upload}</td>
                            <td align="right">最大内存:</td>
                            <td>{$sysInfo.memory_limit}</td>
                        </tr>
                        <tr>
                            <td align="right">执行时间</td>
                            <td>{$sysInfo.max_ex_time}</td>
                            <td align="right">安全模式</td>
                            <td>{$sysInfo.safe_mode}</td>
                        </tr>
                        <tr>
                            <td align="right">Zlib支持</td>
                            <td>{$sysInfo.zlib}</td>
                            <td align="right">Curl支持</td>
                            <td>{$sysInfo.curl}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/static/common/js/echarts.js"></script>
<script src="/static/admin/js/echat.js"></script>
<script>
    $(function () {
        var dateArr = getDate();
        //图表数据接入
        var echart = new Echarts('#user-main', 'line', G_BASE_URL+'/Index/statistic/?tag=new_user,user_valid&start_date=' + dateArr[1] + '&end_date=' + dateArr[0]);
        var echart2 = new Echarts('#content-main', 'line', G_BASE_URL+'/Index/statistic/?tag=new_question,new_answer,new_topic,new_article&start_date=' + dateArr[1] + '&end_date=' + dateArr[0]);

        layui.use(['element','laydate'],function(){
            var element = layui.element//元素操作
            var laydate = layui.laydate;
            //日期时间选择器
            laydate.render({
                elem: '#user-date-area'
                ,type: 'date',
                range: '~',
                format: 'yyyy-mm-dd',
                value: dateArr[1]+' ~ '+dateArr[0],
                done: function (value) {
                    let date_arr = value.split('~'),
                        start_date = $.trim(date_arr[0]),
                        end_date = $.trim(date_arr[1])
                    echart.initChart(echart.url.substring(0, echart.url.search(/&/)) + '&start_date=' + start_date + '&end_date=' + end_date)
                }
            });
            //日期时间选择器
            laydate.render({
                elem: '#question-date-area'
                ,type: 'date',
                range: '~',
                format: 'yyyy-mm-dd',
                value: dateArr[1]+' ~ '+dateArr[0],
                done: function (value) {
                    let date_arr = value.split('~'),
                        start_date = $.trim(date_arr[0]),
                        end_date = $.trim(date_arr[1])
                    echart2.initChart(echart2.url.substring(0, echart2.url.search(/&/)) + '&start_date=' + start_date + '&end_date=' + end_date)
                }
            });
        });
        window.addEventListener("orientationchange", function ()
        {
            echart.render();
            echart2.render();
        }, false);
    });
</script>
{/block}
