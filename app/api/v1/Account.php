<?php

namespace app\api\v1;

use app\common\library\helper\ApiTokenHelper;
use app\common\library\helper\ImageHelper;
use think\facade\Log;
use app\model\Users;
use app\validate\User as UserValidate;
use think\exception\ValidateException;
use app\common\library\helper\MailHelper;
use app\common\library\helper\WeChatHelper;

use app\common\controller\Api;

class Account extends Api
{
    protected string $wxPlatform = 'wxMiniApp';
    protected $needLogin = ['my', 'create_user'];

    public function login()
    {
        if (!$this->request->isPost()) $this->apiError('错误的请求');

        $data = $this->request->post();

        if (!isset($data['account'])) $this->apiError('账号不能为空');

        $remember = intval($this->settings['remember_login_time'])*ONE_DAY;

        if (isset($data['code'])) {
            if (($this->settings['register_type']=='mobile' || $this->settings['register_type']=='all') && get_plugins_config('sms','enable') != 'N' && !$data['code']) {
                $this->apiError('请输入手机验证码');
            }

            if (cache('sms_'.$data['account']) != $data['code']) $this->apiError('手机验证码不正确');

            $user = Users::loginByMobile($data['account'], $data['code'], $data, $remember, true, 'h5');
        } else {
            if (!isset($data['password'])) $this->apiError('密码不能为空');
            $user = Users::getLogin($data['account'], $data['password'], $remember, 'h5');
        }

        if (!$user) $this->apiError(Users::getError());
        $uid = is_array($user) ? $user['uid'] : $user;
        $userInfo = Users::getUserInfo($uid);
        $res = [
            'uid' => $uid,
            'sex' => $userInfo['sex'],
            'url_token' => $userInfo['url_token'],
            'nick_name' => $userInfo['nick_name'],
            'signature' => $userInfo['signature'] ?: '这家伙没有什么简介',
            'group_id' => $userInfo['group_id'],
            'group_name' => $userInfo['group_name'],
            'integral' => $userInfo['integral'],
            'reputation' => $userInfo['reputation'],
            'agree_count' => $userInfo['agree_count'],
            'fans_count' => $userInfo['fans_count'],
            'friend_count' => $userInfo['friend_count'],
            'mobile' => $userInfo['mobile'],
            'email' => $userInfo['email'],
            'permission' => $userInfo['permission'],
            'token' => session('login_token'),
            'birthday' => $userInfo['birthday'] ? date('Y-m-d', $userInfo['birthday']) : '',
            'avatar' => $userInfo['avatar'] ? ImageHelper::replaceImageUrl($userInfo['avatar']) : $this->request->domain().'/static/common/image/default-avatar.svg'
        ];
        
        $this->apiSuccess('登录成功', $res);
    }

    // 注册 兼容注册和微信小程序注册+绑定
    public function register()
    {
        $data = $this->request->post();
        $data['register_type'] = $data['register_type'] ?? 'h5';
        $checkPassword = wcCheckPassword($data['password']);
        if ($checkPassword !== true) $this->apiError($checkPassword);
        if (isset($data['code']) || isset($data['mobile'])) {
            // 手机号绑定 兼容直接绑定和注册绑定
            if (!preg_match('/^1[3-9]\d{9}$/', $data['mobile'])) $this->apiError('错误的手机号');
            if (!(get_plugins_config('sms','base') && get_plugins_config('sms','base')!='N')) $this->apiError('网站尚未安装或配置短信插件');

            if(!$data['code'] || cache('sms_'.$data['mobile']) != $data['code']) $this->apiError('手机验证码不正确');
            $uid = Users::loginByMobile($data['mobile'], $data['code'], $data, 0, true, $data['register_type']);
        } else {
            $data['email'] =  trim($data['email']);
            $data['username'] = trim($data['username']);
            $data['password'] = trim($data['password']);
            // 微信输入内容安全检测
            $this->wxminiCheckText($data['username'], '用户名不符合微信小程序安全检测');
            if (Users::checkUserExist($data['username'])) {
                if ($data['register_type'] == $this->wxPlatform) {
                    $user = Users::getLogin($data['username'], $data['password'], 0, $this->wxPlatform);
                    $uid = $user ? $user['uid'] : 0;
                } else {
                    $uid = 0;
                    $this->apiError('账号已存在');
                }
            } else {
                try {
                    validate(UserValidate::class)->scene('register')->check($data);
                } catch (ValidateException $e) {
                    $this->apiError($e->getError());
                }

                if (!$data['email'] || !MailHelper::isEmail($data['email'])) $this->apiError('请填写正确的邮箱地址');
                if (Users::checkUserExist($data['email'])) $this->apiError('该邮箱已被使用请更换邮箱');

                $uid = Users::registerUser($data['username'], $data['password'], ['email' => $data['email']], true, true, $data['register_type']);
            }
        }

        if (!$uid) $this->apiError(Users::getError());
        // 微信小程序和用户绑定
        if ($uid && $data['register_type'] == $this->wxPlatform) {
            $this->bindWxPlatformAccount($uid, $data['openid'] ?? '');
        }

        $this->apiSuccess('注册成功', $this->getUserInfo($uid));
    }

    // 受控创建普通用户并绑定 API 访问 token
    public function create_user()
    {
        if (!$this->request->isPost()) {
            $this->apiError('错误的请求');
        }

        if (!$this->canCreateManagedUser()) {
            $this->apiError('您没有新增用户权限');
        }

        $data = $this->request->post();
        $data['username'] = trim((string) ($data['username'] ?? ''));
        $data['password'] = trim((string) ($data['password'] ?? ''));
        $data['email'] = trim((string) ($data['email'] ?? ''));
        $data['mobile'] = trim((string) ($data['mobile'] ?? ''));
        $data['nick_name'] = trim((string) ($data['nick_name'] ?? ''));
        $data['signature'] = trim((string) ($data['signature'] ?? ''));
        $data['is_agent'] = intval($data['is_agent'] ?? 0) ? 1 : 0;
        $data['agent_level'] = max(0, intval($data['agent_level'] ?? 0));
        $data['agent_badge'] = trim((string) ($data['agent_badge'] ?? ''));
        $data['agent_display_name'] = trim((string) ($data['agent_display_name'] ?? ''));
        $data['agent_model_name'] = trim((string) ($data['agent_model_name'] ?? ''));
        $data['agent_verified_at'] = intval($data['agent_verified_at'] ?? 0);
        $data['agent_last_challenge_at'] = intval($data['agent_last_challenge_at'] ?? 0);
        $data['agent_challenge_total'] = max(0, intval($data['agent_challenge_total'] ?? 0));
        $data['agent_challenge_success'] = max(0, intval($data['agent_challenge_success'] ?? 0));
        $data['agent_challenge_failure'] = max(0, intval($data['agent_challenge_failure'] ?? 0));
        $data['agent_pass_rate'] = max(0, min(100, floatval($data['agent_pass_rate'] ?? 0)));
        $data['agent_avg_response_ms'] = max(0, intval($data['agent_avg_response_ms'] ?? 0));
        $data['agent_success_streak'] = max(0, intval($data['agent_success_streak'] ?? 0));
        $data['agent_best_response_ms'] = max(0, intval($data['agent_best_response_ms'] ?? 0));
        $data['agent_recent_response_ms'] = max(0, intval($data['agent_recent_response_ms'] ?? 0));

        try {
            validate(UserValidate::class)->scene('register')->check($data);
        } catch (ValidateException $e) {
            $this->apiError($e->getError());
        }

        $checkPassword = wcCheckPassword($data['password']);
        if ($checkPassword !== true) {
            $this->apiError($checkPassword);
        }

        if ($data['email'] !== '') {
            if (!MailHelper::isEmail($data['email'])) {
                $this->apiError('请填写正确的邮箱地址');
            }
            if (Users::checkUserExist($data['email'])) {
                $this->apiError('该邮箱已被使用请更换邮箱');
            }
        }

        if ($data['mobile'] !== '') {
            if (!preg_match('/^1[3-9]\d{9}$/', $data['mobile'])) {
                $this->apiError('错误的手机号');
            }
            if (Users::checkUserExist($data['mobile'])) {
                $this->apiError('该手机号已被使用请更换手机号');
            }
        }

        $tokenValue = trim((string) ($data['access_token'] ?? ''));
        if ($tokenValue === '') {
            $tokenValue = $this->buildUniqueApiToken();
        } elseif (db('app_token')->where('token', $tokenValue)->find()) {
            $this->apiError('访问 token 已存在，请更换后重试');
        }

        $tokenTitle = trim((string) ($data['token_title'] ?? ''));
        if ($tokenTitle === '') {
            $tokenTitle = $data['username'] . ' API Token';
        }

        $tokenRemark = trim((string) ($data['token_remark'] ?? ''));
        if ($tokenRemark === '') {
            $tokenRemark = '由 UID ' . $this->user_id . ' 通过 API 创建并绑定';
        }

        $expireTime = $this->normalizeTokenExpireTime($data['expire_time'] ?? '');
        if ($expireTime === null) {
            $this->apiError('访问 token 过期时间格式不正确');
        }

        $extend = array_filter([
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'nick_name' => $data['nick_name'],
            'signature' => $data['signature'],
            'status' => 1,
            'is_agent' => $data['is_agent'],
            'agent_level' => $data['agent_level'],
            'agent_badge' => $data['agent_badge'],
            'agent_display_name' => $data['agent_display_name'],
            'agent_model_name' => $data['agent_model_name'],
            'agent_verified_at' => $data['agent_verified_at'],
            'agent_last_challenge_at' => $data['agent_last_challenge_at'],
            'agent_challenge_total' => $data['agent_challenge_total'],
            'agent_challenge_success' => $data['agent_challenge_success'],
            'agent_challenge_failure' => $data['agent_challenge_failure'],
            'agent_pass_rate' => $data['agent_pass_rate'],
            'agent_avg_response_ms' => $data['agent_avg_response_ms'],
            'agent_success_streak' => $data['agent_success_streak'],
            'agent_best_response_ms' => $data['agent_best_response_ms'],
            'agent_recent_response_ms' => $data['agent_recent_response_ms'],
        ], static function ($value) {
            return $value !== '' && $value !== null;
        });

        db()->startTrans();
        try {
            $uid = Users::createManagedUser($data['username'], $data['password'], $extend, 'api');
            if (!$uid) {
                throw new \RuntimeException(Users::getError() ?: '新增用户失败');
            }

            $inserted = db('app_token')->insert([
                'title' => $tokenTitle,
                'token' => $tokenValue,
                'version' => '',
                'plugin' => '',
                'type' => 3,
                'uid' => $uid,
                'status' => 1,
                'expire_time' => $expireTime,
                'last_use_time' => 0,
                'last_use_ip' => '',
                'remark' => $tokenRemark,
                'create_time' => time(),
            ]);

            if (!$inserted) {
                throw new \RuntimeException('绑定访问 token 失败');
            }

            db()->commit();
        } catch (\Throwable $e) {
            db()->rollback();
            $this->apiError($e->getMessage());
        }

        $userData = $this->getUserInfo($uid);
        unset($userData['token']);

        $this->apiSuccess('新增用户成功', [
            'user' => $userData,
            'access_token' => $tokenValue,
            'token_type' => 'ApiToken',
            'headers' => [
                'ApiToken' => $tokenValue,
                'AccessToken' => $tokenValue,
                'version' => 'v1',
            ],
            'expire_time' => $expireTime,
        ]);
    }


    // 微信小程序登录
    public function wxminiapp_login()
    {
       try {
            $code = trim((string)$this->request->get('code', ''));
            if ($code === '') {
                $this->apiError('缺少登录凭证');
            }
            $app = WeChatHelper::instance()->getMiniProgram();
            $data = $app->auth->session($code);
            if (isset($data['errcode']) && !$data['errcode']) {
                $wxMinAppUser = db('third')->where('platform', $this->wxPlatform)->where('openid', $data['openid'])->find(); // 微信小程序用户
                $data['is_bind'] = $wxMinAppUser ? 1 : 0;
                // 已绑定-直接登录响应登录数据
                if ($data['is_bind']) {
                    // 服务器登录
                    $userInfo = Users::getUserInfo($wxMinAppUser['uid']);
                    $userInfo['client'] = $this->wxPlatform;
                    if (Users::extracted($userInfo, 0)) {
                        $this->apiError(Users::getError());
                    }
                    $data = [
                        'uid' => $userInfo['uid'],
                        'sex' => $userInfo['sex'],
                        'url_token' => $userInfo['url_token'],
                        'nick_name' => $userInfo['nick_name'],
                        'signature' => $userInfo['signature'] ?: '这家伙没有什么简介',
                        'group_id' => $userInfo['group_id'],
                        'group_name' => $userInfo['group_name'],
                        'integral' => $userInfo['integral'],
                        'reputation' => $userInfo['reputation'],
                        'agree_count' => $userInfo['agree_count'],
                        'fans_count' => $userInfo['fans_count'],
                        'friend_count' => $userInfo['friend_count'],
                        'mobile' => $userInfo['mobile'],
                        'email' => $userInfo['email'],
                        'permission' => $userInfo['permission'],
                        'token' => session('login_token'),
                        'birthday' => $userInfo['birthday'] ? date('Y-m-d', $userInfo['birthday']) : '',
                        'avatar' => $userInfo['avatar'] ? ImageHelper::replaceImageUrl($userInfo['avatar']) : $this->request->domain().'/static/common/image/default-avatar.svg'
                    ];
                }

                $this->apiSuccess('登录成功', $data);
            } else {
                $this->apiError("{$data['errmsg']}");
            }
       } catch (\Exception $e) {
            Log::error("微信小程序登录错误：{$e->getFile()} {$e->getLine()} {$e->getCode()} {$e->getMessage()}");
            $this->apiError($e->getMessage());
       }
    }
    
    // 微信小程序用户直接绑定/注册+绑定
    public function wxminiapp_bind()
    {
        $data = $this->request->post();

        if (isset($data['code']) || isset($data['mobile'])) {
            // 手机号绑定 兼容直接绑定和注册绑定
            if (!preg_match('/^1[3-9]\d{9}$/', $data['mobile'])) $this->apiError('错误的手机号');
            if (!(get_plugins_config('sms','base') && get_plugins_config('sms','base')!='N')) $this->apiError('网站尚未安装或配置短信插件');

            if(!$data['code'] || cache('sms_'.$data['mobile']) != $data['code']) $this->apiError('手机验证码不正确');

            $uid = Users::loginByMobile($data['mobile'], $data['code'], $data, 0, true, $this->wxPlatform);
        } else {
            $data['email'] =  trim($data['email'] ?? '');
            $data['username'] = trim($data['username']);
            $data['password'] = trim($data['password']);
            // 微信输入内容安全检测
            $this->wxminiCheckText($data['username'], '用户名不符合微信小程序安全检测');
            if (Users::checkUserExist($data['username'])) {
                $user = Users::getLogin($data['username'], $data['password'], 0, $this->wxPlatform);
                $uid = $user ? $user['uid'] : 0;
            } else {
                try {
                    validate(UserValidate::class)->scene('register')->check($data);
                } catch (ValidateException $e) {
                    $this->apiError($e->getError());
                }

                if (!$data['email'] || !MailHelper::isEmail($data['email'])) $this->apiError('请填写正确的邮箱地址');
                if (Users::checkUserExist($data['email'])) $this->apiError('该邮箱已被使用请更换邮箱');

                $uid = Users::registerUser($data['username'], $data['password'], ['email' => $data['email']], true, true, $this->wxPlatform);
            }
        }

        if (!$uid) $this->apiError(Users::getError());
        // 微信小程序和用户绑定
        if ($uid) {
            $this->bindWxPlatformAccount($uid, $data['openid'] ?? '');
        }

        $userInfo = Users::getUserInfo($uid);

        $this->apiSuccess('绑定成功', [
            'uid' => $uid,
            'sex' => $userInfo['sex'],
            'url_token' => $userInfo['url_token'],
            'nick_name' => $userInfo['nick_name'],
            'signature' => $userInfo['signature'] ?: '这家伙没有什么简介',
            'group_id' => $userInfo['group_id'],
            'group_name' => $userInfo['group_name'],
            'integral' => $userInfo['integral'],
            'reputation' => $userInfo['reputation'],
            'agree_count' => $userInfo['agree_count'],
            'fans_count' => $userInfo['fans_count'],
            'friend_count' => $userInfo['friend_count'],
            'mobile' => $userInfo['mobile'],
            'email' => $userInfo['email'],
            'permission' => $userInfo['permission'],
            'token' => session('login_token'),
            'birthday' => $userInfo['birthday'] ? date('Y-m-d', $userInfo['birthday']) : '',
            'avatar' => $userInfo['avatar'] ? ImageHelper::replaceImageUrl($userInfo['avatar']) : $this->request->domain().'/static/common/image/default-avatar.svg'
        ]);
    }


    // 我的信息
    public function my()
    {
        $this->apiResult($this->getUserInfo());
    }

    // 重设密码
    public function reset_password()
    {
        $data = $this->request->post();
        if (!preg_match('/^1[3-9]\d{9}$/', $data['mobile'])) $this->apiError('请输入正确的手机号');
        if (!(get_plugins_config('sms','base') && get_plugins_config('sms','base')!='N')) $this->apiError('网站尚未安装或配置短信插件');
        if(!$data['code'] || cache('sms_'.$data['mobile']) != $data['code']) $this->apiError('短信验证码不正确');
        $checkPassword = wcCheckPassword($data['password']);
        if ($checkPassword !== true) $this->apiError($checkPassword);
        $uid = Users::loginByMobile($data['mobile'], $data['code'], [], 0, true);
        if (!$uid) $this->apiError(Users::getError());
        if (Users::updateUserFiled($uid, ['password' => $data['password']])) {
            $this->apiSuccess('操作成功', $this->getUserInfo($uid));
        } else {
            $this->apiError('操作失败');
        }
    }

    protected function getUserInfo ($uid=0)
    {
        $uid = $uid ?:$this->user_id;
        $userInfo = Users::getUserInfo($uid);
        return [
            'uid' => $userInfo['uid'],
            'sex' => $userInfo['sex'],
            'url_token' => $userInfo['url_token'],
            'nick_name' => $userInfo['nick_name'],
            'signature' => $userInfo['signature'] ?: '这家伙没有什么简介',
            'group_id' => $userInfo['group_id'],
            'group_name' => $userInfo['group_name'],
            'integral' => $userInfo['integral'],
            'reputation' => $userInfo['reputation'],
            'agree_count' => $userInfo['agree_count'],
            'fans_count' => $userInfo['fans_count'],
            'friend_count' => $userInfo['friend_count'],
            'mobile' => $userInfo['mobile'],
            'email' => $userInfo['email'],
            'permission' => $userInfo['permission'],
            'token' => session('login_token'),
            'inbox_unread' => $userInfo['inbox_unread'],
            'notify_unread' => $userInfo['notify_unread'],
            'is_valid_email' => $userInfo['is_valid_email'],
            'is_agent' => intval($userInfo['is_agent'] ?? 0),
            'agent_level' => intval($userInfo['agent_level'] ?? 0),
            'agent_badge' => $userInfo['agent_badge'] ?? '',
            'agent_display_name' => $userInfo['agent_display_name'] ?? '',
            'agent_model_name' => $userInfo['agent_model_name'] ?? '',
            'agent_verified_at' => intval($userInfo['agent_verified_at'] ?? 0),
            'agent_last_challenge_at' => intval($userInfo['agent_last_challenge_at'] ?? 0),
            'agent_challenge_total' => intval($userInfo['agent_challenge_total'] ?? 0),
            'agent_challenge_success' => intval($userInfo['agent_challenge_success'] ?? 0),
            'agent_challenge_failure' => intval($userInfo['agent_challenge_failure'] ?? 0),
            'agent_pass_rate' => floatval($userInfo['agent_pass_rate'] ?? 0),
            'agent_avg_response_ms' => intval($userInfo['agent_avg_response_ms'] ?? 0),
            'agent_success_streak' => intval($userInfo['agent_success_streak'] ?? 0),
            'agent_best_response_ms' => intval($userInfo['agent_best_response_ms'] ?? 0),
            'agent_recent_response_ms' => intval($userInfo['agent_recent_response_ms'] ?? 0),
            'birthday' => $userInfo['birthday'] ? date('Y-m-d', $userInfo['birthday']) : '',
            'avatar' => $userInfo['avatar'] ? ImageHelper::replaceImageUrl($userInfo['avatar']) : $this->request->domain().'/static/common/image/default-avatar.svg'
        ];
    }

    protected function bindWxPlatformAccount(int $uid, string $openid): void
    {
        $openid = trim($openid);
        if ($uid <= 0 || $openid === '') {
            $this->apiError('缺少绑定参数');
        }
        $bindInfo = db('third')->where(['platform' => $this->wxPlatform, 'openid' => $openid])->find();
        if ($bindInfo && intval($bindInfo['uid']) !== $uid) {
            $this->apiError('该微信账号已绑定其他用户');
        }
        if (!$bindInfo) {
            db('third')->insert([
                'uid' => $uid,
                'openid' => $openid,
                'platform' => $this->wxPlatform
            ]);
        }
    }

    protected function canCreateManagedUser(): bool
    {
        $groupId = intval($this->user_info['group_id'] ?? 0);
        if (in_array($groupId, [1, 2], true)) {
            return true;
        }

        if (isSuperAdmin() || isNormalAdmin()) {
            return true;
        }

        return (($this->user_info['permission']['create_api_user'] ?? 'N') === 'Y');
    }

    protected function buildUniqueApiToken(): string
    {
        do {
            $token = ApiTokenHelper::buildToken();
        } while (db('app_token')->where('token', $token)->find());

        return $token;
    }

    protected function normalizeTokenExpireTime($value): ?int
    {
        if ($value === null) {
            return 0;
        }

        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') {
                return 0;
            }
        }

        if (is_numeric($value)) {
            $timestamp = intval($value);
            return $timestamp > 0 ? $timestamp : 0;
        }

        $timestamp = strtotime((string) $value);
        if ($timestamp === false) {
            return null;
        }

        return $timestamp > 0 ? $timestamp : 0;
    }
}
