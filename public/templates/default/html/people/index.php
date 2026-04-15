{extend name="$theme_block" /}
{block name="main"}
<div class="container mt-2 people-page-wrap">
    <div class="row">
        <div class="col-md-9 aw-left mb-2 center-main">
            <section class="people-profile-card bg-white rounded mb-2">
                <div class="people-profile-main media p-3">
                    <div class="authorAvatar mr-3">
                        <img src="{$user.avatar}" onerror="this.src='/static/common/image/default-avatar.svg'" alt="{$user.name}">
                    </div>
                    <div class="media-body position-relative">
                        <h1 class="people-profile-title mb-1">
                            <a href="{$user.url}" class="text-dark">{$user.name}</a>
                            <span class="badge badge-success ml-1">{$user.group_name}</span>
                            {if $user.verified_icon}
                            <img src="{$user.verified_icon}" class="people-verified-icon" alt="verified">
                            {/if}
                            {if $user.status==3}
                            <span class="badge badge-danger ml-1">{:L('已封禁')}</span>
                            {/if}
                        </h1>
                        <p class="people-profile-signature aw-two-line mb-2">{$user.signature|default=L('这家伙还没有留下自我介绍')}</p>
                        <div class="people-profile-sex text-muted mb-2">
                            {$user.sex==1 ? L('男') : ($user.sex==2 ? L('女') : L('保密'))}
                        </div>
                        <div class="people-profile-stats">
                            {volist name="profile_stats" id="stat"}
                            <span class="people-stat-item">{$stat.label}: {$stat.value}</span>
                            {/volist}
                            {if $user_id==$user.uid}
                            <span class="people-stat-item">{:L('余额')}: {$user.money}</span>
                            {/if}
                        </div>
                        {if $user_id}
                        <div class="aw-home-user-btn people-action-bar">
                            {if $user_id==$user.uid}
                            <a class="btn btn-primary btn-sm px-4" href="{:url('member/setting/profile')}">{:L('编辑资料')}</a>
                            {else/}
                            <a class="btn btn-primary btn-sm px-4 {if $user['has_focus']}active{/if} {$user['has_focus'] ? 'ygz' : 'gz'}" href="javascript:;" onclick="AWS.User.focus(this,'user','{$user.uid}')">{$user['has_focus'] ? L('已关注') : L('关注')}</a>
                            <a class="btn btn-outline-primary btn-sm px-4 ml-2" href="javascript:;" onclick="AWS.User.inbox('{$user.nick_name}')">{:L('私信')}</a>
                            {/if}
                            {:hook('peopleDetailExtendBtn',$user)}
                        </div>
                        {/if}
                    </div>
                </div>
            </section>

            <div class="swiper-container people-tabs-card bg-white rounded">
                <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block px-3 bg-white swiper-wrapper people-nav-tabs">
                    {volist name="post_tabs" id="tab"}
                    <li class="nav-item swiper-slide {if $type==$tab.type}i-active{/if}">
                        <a class="nav-link {if $type==$tab.type}active{/if}" data-pjax="tabMain" href="{:url('people/index',['name'=>$user['url_token'],'type'=>$tab.type])}">
                            {$tab.label}{if isset($tab['count'])} {$tab['count']}{/if}
                        </a>
                    </li>
                    {/volist}
                    {:hook('people_nav',['user'=>$user])}
                </ul>
            </div>

            <div class="tab-content bg-white rounded people-content-card" id="tabMain">
                <div class="tab-pane fade show active">
                    <div class="aw-common-list">
                        {:widget('member/getUserPost', ['uid' => $user['uid'], 'type' => $type])}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 aw-right px-xs-0">
            {:hook('sidebarTop')}

            <section class="people-achievement-card media p-3 bg-white rounded">
                <div class="media-body">
                    <h6 class="font-weight-bold mb-2">{:L('个人成就')}</h6>
                    <ul class="people-achievement-list mb-0">
                        <li><i class="iconfont">&#xe870;</i>{:L('获得 %s 次赞同',$user.agree_count)}</li>
                        <li>{:L('被 %s 人关注了',$user.fans_count)}</li>
                        <li>{:L('关注了 %s 人',$user.friend_count)}</li>
                    </ul>
                </div>
            </section>

            {:hook('sidebarBottom')}
        </div>
    </div>
</div>
<script type="text/javascript">
    window.__whenSwiperReady(function () {
        let k = parseInt($('.i-active').index(), 10);

        if (isNaN(k)) {
            k = 0;
        }

        new Swiper('.swiper-container', {
            speed: 600,
            grabCursor: true,
            slidesPerView: 'auto',
            initialSlide: k,
            slidesPerGroup: 3
        });
    });
</script>
{/block}
