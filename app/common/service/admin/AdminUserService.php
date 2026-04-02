<?php

namespace app\common\service\admin;

use app\common\library\helper\LogHelper;
use app\common\library\helper\RandomHelper;
use app\model\Score as ScoreModel;
use app\model\Users as UserModel;

class AdminUserService
{
    public function getOverview(int $status = 1, string $keyword = '', int $forbiddenIp = 0): array
    {
        $statusTabs = $this->getStatusTabs();
        if ($forbiddenIp !== 1 && !in_array($status, [0, 1, 2, 3, 4], true)) {
            $status = 1;
        }

        return [
            'status' => $status,
            'forbidden_ip' => $forbiddenIp,
            'tabs' => $statusTabs,
            'list' => $this->getUserList($status, $keyword, $forbiddenIp),
            'meta' => $this->getEditorMeta(),
        ];
    }

    public function getDetail(int $uid): array
    {
        if ($uid <= 0) {
            return [];
        }

        $info = db('users')->where('uid', $uid)->find();
        if (!$info) {
            return [];
        }

        return $this->formatUserDetail($info);
    }

    public function save(array $data): array
    {
        $uid = intval($data['uid'] ?? 0);
        if ($uid <= 0) {
            return ['code' => 0, 'msg' => '用户不存在'];
        }

        $user = db('users')->where('uid', $uid)->find();
        if (!$user) {
            return ['code' => 0, 'msg' => '用户不存在'];
        }

        $payload = [
            'nick_name' => trim((string) ($data['nick_name'] ?? '')),
            'email' => trim((string) ($data['email'] ?? '')),
            'mobile' => trim((string) ($data['mobile'] ?? '')),
            'avatar' => trim((string) ($data['avatar'] ?? '')) ?: null,
            'signature' => (string) ($data['signature'] ?? ''),
            'group_id' => intval($data['group_id'] ?? 0),
            'reputation_group_id' => intval($data['reputation_group_id'] ?? 0),
            'integral_group_id' => intval($data['integral_group_id'] ?? 0),
            'verified' => trim((string) ($data['verified'] ?? '')),
            'sex' => intval($data['sex'] ?? 0),
            'status' => intval($data['status'] ?? 1),
        ];

        if ($payload['nick_name'] === '') {
            return ['code' => 0, 'msg' => '用户昵称不能为空'];
        }
        if ($payload['verified'] === 'normal') {
            $payload['verified'] = '';
        }

        $birthday = trim((string) ($data['birthday'] ?? ''));
        if ($birthday !== '') {
            $payload['birthday'] = strtotime($birthday);
        }

        $password = trim((string) ($data['password'] ?? ''));
        if ($password !== '') {
            $salt = RandomHelper::alnum();
            $payload['salt'] = $salt;
            $payload['password'] = compile_password($password, $salt);
        }

        $dealPassword = trim((string) ($data['deal_password'] ?? ''));
        if ($dealPassword !== '') {
            $payload['deal_password'] = password_hash($dealPassword, 1);
        }

        $updated = db('users')->where('uid', $uid)->update($payload);
        if ($updated === false) {
            return ['code' => 0, 'msg' => '保存失败'];
        }

        return ['code' => 1, 'msg' => '保存成功', 'data' => ['uid' => $uid]];
    }

    public function create(array $data): array
    {
        $userName = trim((string) ($data['user_name'] ?? ''));
        $password = trim((string) ($data['password'] ?? ''));
        $payload = [
            'nick_name' => trim((string) ($data['nick_name'] ?? '')),
            'email' => trim((string) ($data['email'] ?? '')),
            'mobile' => trim((string) ($data['mobile'] ?? '')),
            'avatar' => trim((string) ($data['avatar'] ?? '')),
            'signature' => (string) ($data['signature'] ?? ''),
            'group_id' => intval($data['group_id'] ?? 4),
            'reputation_group_id' => intval($data['reputation_group_id'] ?? 1),
            'integral_group_id' => intval($data['integral_group_id'] ?? 1),
            'status' => intval($data['status'] ?? 1),
        ];

        $uid = UserModel::registerUser($userName, $password, $payload, false, true);
        if (!$uid) {
            return ['code' => 0, 'msg' => UserModel::getError() ?: '添加失败'];
        }

        return ['code' => 1, 'msg' => '添加成功', 'data' => ['uid' => intval($uid)]];
    }

    public function approve($ids): array
    {
        $ids = $this->normalizeIds($ids);
        if (!$ids) {
            return ['code' => 0, 'msg' => '请选择要操作的数据'];
        }

        db('users')->whereIn('uid', $ids)->update(['status' => 1]);
        return ['code' => 1, 'msg' => '操作成功'];
    }

    public function decline($ids): array
    {
        $ids = $this->normalizeIds($ids);
        if (!$ids) {
            return ['code' => 0, 'msg' => '请选择要操作的数据'];
        }

        db('users')->whereIn('uid', $ids)->update(['status' => 4]);
        return ['code' => 1, 'msg' => '操作成功'];
    }

    public function forbid($ids, string $forbiddenTime, string $reason): array
    {
        $ids = $this->normalizeIds($ids);
        if (!$ids) {
            return ['code' => 0, 'msg' => '请选择要操作的数据'];
        }
        if ($forbiddenTime === '') {
            return ['code' => 0, 'msg' => '请选择封禁时长'];
        }
        if ($reason === '') {
            return ['code' => 0, 'msg' => '请填写封禁原因'];
        }

        foreach ($ids as $uid) {
            if (!db('users_forbidden')->where(['uid' => $uid])->find()) {
                db('users_forbidden')->insert([
                    'uid' => $uid,
                    'forbidden_time' => strtotime($forbiddenTime),
                    'forbidden_reason' => $reason,
                    'create_time' => time(),
                    'status' => 1,
                ]);
            } else {
                db('users_forbidden')->where(['uid' => $uid])->update([
                    'forbidden_time' => strtotime($forbiddenTime),
                    'forbidden_reason' => $reason,
                    'status' => 1,
                ]);
            }
        }
        db('users')->whereIn('uid', $ids)->update(['status' => 3]);
        return ['code' => 1, 'msg' => '操作成功'];
    }

    public function unForbid($ids): array
    {
        $ids = $this->normalizeIds($ids);
        if (!$ids) {
            return ['code' => 0, 'msg' => '请选择要操作的数据'];
        }

        db('users_forbidden')->whereIn('uid', $ids)->update(['status' => 0]);
        db('users')->whereIn('uid', $ids)->update(['status' => 1]);
        return ['code' => 1, 'msg' => '操作成功'];
    }

    public function forbiddenIp($ids, bool $relieve = false): array
    {
        $ids = $this->normalizeIds($ids);
        if (!$ids) {
            return ['code' => 0, 'msg' => '请选择要操作的数据'];
        }

        if ($relieve) {
            $result = count($ids) === 1 ? UserModel::liftIp($ids[0]) : UserModel::batchLiftIp($ids);
            return ['code' => $result ? 1 : 0, 'msg' => $result ? '操作成功' : '操作失败'];
        }

        if (count($ids) === 1) {
            $uid = $ids[0];
            $user = UserModel::find($uid);
            $result = $user ? UserModel::forbiddenIp([
                'uid' => $uid,
                'ip' => $user->last_login_ip,
                'time' => time(),
            ]) : false;
            return ['code' => $result ? 1 : 0, 'msg' => $result ? '操作成功' : '操作失败'];
        }

        $result = UserModel::batchForbiddenIp($ids);
        return ['code' => $result ? 1 : 0, 'msg' => $result ? '操作成功' : '操作失败'];
    }

    public function recover($ids): array
    {
        $ids = $this->normalizeIds($ids);
        if (!$ids) {
            return ['code' => 0, 'msg' => '请选择要操作的数据'];
        }

        if (!UserModel::recoverUsers($ids)) {
            return ['code' => 0, 'msg' => '恢复失败:' . (UserModel::getError() ?: '未知错误')];
        }

        return ['code' => 1, 'msg' => '恢复成功'];
    }

    public function remove($ids, bool $real = false): array
    {
        $ids = $this->normalizeIds($ids);
        if (!$ids) {
            return ['code' => 0, 'msg' => '请选择要操作的数据'];
        }

        if (!UserModel::removeUser($ids, $real ? 1 : 0)) {
            return ['code' => 0, 'msg' => UserModel::getError() ?: '操作失败'];
        }

        return ['code' => 1, 'msg' => $real ? '删除成功' : '删除成功'];
    }

    public function getIntegralLogs(int $uid, int $page = 1, int $perPage = 10): array
    {
        if ($uid <= 0) {
            return ['uid' => 0, 'list' => [], 'pagination' => ['page' => 1, 'per_page' => $perPage], 'page_html' => ''];
        }

        $result = ScoreModel::getScoreList(['uid' => $uid], $page, $perPage);
        $list = $result['list'] ?? [];
        foreach ($list as &$item) {
            $item['integral'] = intval($item['integral'] ?? 0);
            $item['create_time_text'] = !empty($item['create_time']) ? date('Y-m-d H:i:s', intval($item['create_time'])) : '-';
        }
        unset($item);

        return [
            'uid' => $uid,
            'list' => $list,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
            ],
            'page_html' => (string) ($result['page'] ?? ''),
        ];
    }

    public function awardIntegral(int $uid, int $integral): array
    {
        if ($uid <= 0) {
            return ['code' => 0, 'msg' => '用户不存在'];
        }
        if ($integral === 0) {
            return ['code' => 0, 'msg' => '请输入操作积分'];
        }

        if (!LogHelper::addIntegralLog('AWARD', $uid, 'users', $uid, $integral)) {
            return ['code' => 0, 'msg' => '积分操作失败'];
        }

        return ['code' => 1, 'msg' => '积分操作成功'];
    }

    public function getEditorMeta(): array
    {
        return [
            'status_options' => $this->mapOptions([
                0 => '禁用',
                1 => '正常',
                2 => '待审核',
                3 => '已封禁',
                4 => '拒绝审核',
            ]),
            'sex_options' => $this->mapOptions([
                0 => '保密',
                1 => '男',
                2 => '女',
            ]),
            'verified_options' => $this->buildVerifiedOptions(),
            'group_options' => $this->buildAdminGroupOptions(),
            'reputation_group_options' => $this->buildReputationGroupOptions(),
            'integral_group_options' => $this->buildIntegralGroupOptions(),
        ];
    }

    protected function getUserList(int $status, string $keyword, int $forbiddenIp): array
    {
        $query = db('users')->order(['uid' => 'desc']);
        if ($forbiddenIp === 1) {
            $query->where('forbidden_ip', 1);
        } else {
            $query->where([
                ['status', '=', $status],
                ['forbidden_ip', '=', 0],
            ]);
        }
        if ($keyword !== '') {
            $query->where(function ($builder) use ($keyword) {
                $builder
                    ->whereLike('user_name', '%' . $keyword . '%')
                    ->whereOrLike('nick_name', '%' . $keyword . '%')
                    ->whereOrLike('email', '%' . $keyword . '%')
                    ->whereOrLike('mobile', '%' . $keyword . '%');
            });
        }

        $groupMap = db('admin_group')->column('title', 'id');
        $integralMap = db('users_integral_group')->column('title', 'id');
        $reputationMap = db('users_reputation_group')->column('title', 'id');

        $list = $query
            ->field('uid,user_name,nick_name,avatar,email,mobile,group_id,reputation_group_id,integral_group_id,status,forbidden_ip,last_login_time,last_login_ip,create_time')
            ->select()
            ->toArray();

        foreach ($list as &$item) {
            $item = $this->formatUserRow($item, $groupMap, $integralMap, $reputationMap);
            $item['actions'] = $this->resolveActions($item['status'], intval($item['forbidden_ip'] ?? 0));
        }
        unset($item);

        return $list;
    }

    protected function formatUserRow(array $item, array $groupMap, array $integralMap, array $reputationMap): array
    {
        $item['uid'] = intval($item['uid'] ?? 0);
        $item['group_id'] = intval($item['group_id'] ?? 0);
        $item['reputation_group_id'] = intval($item['reputation_group_id'] ?? 0);
        $item['integral_group_id'] = intval($item['integral_group_id'] ?? 0);
        $item['status'] = intval($item['status'] ?? 0);
        $item['forbidden_ip'] = intval($item['forbidden_ip'] ?? 0);
        $item['group_name'] = (string) ($groupMap[$item['group_id']] ?? '-');
        $item['reputation_group_name'] = (string) ($reputationMap[$item['reputation_group_id']] ?? '-');
        $item['integral_group_name'] = (string) ($integralMap[$item['integral_group_id']] ?? '-');
        $item['status_label'] = $this->formatStatusLabel($item['status'], $item['forbidden_ip']);
        $item['create_time_text'] = $this->formatTime($item['create_time'] ?? 0);
        $item['last_login_time_text'] = $this->formatTime($item['last_login_time'] ?? 0);
        return $item;
    }

    protected function formatUserDetail(array $item): array
    {
        $meta = $this->getEditorMeta();
        $groupMap = db('admin_group')->column('title', 'id');
        $integralMap = db('users_integral_group')->column('title', 'id');
        $reputationMap = db('users_reputation_group')->column('title', 'id');
        $item = $this->formatUserRow($item, $groupMap, $integralMap, $reputationMap);
        $item['signature'] = (string) ($item['signature'] ?? '');
        $item['verified'] = (string) ($item['verified'] ?? '') ?: 'normal';
        $item['sex'] = intval($item['sex'] ?? 0);
        $item['birthday_text'] = !empty($item['birthday']) ? date('Y-m-d', intval($item['birthday'])) : '';
        $item['meta'] = $meta;
        return $item;
    }

    protected function resolveActions(int $status, int $forbiddenIp): array
    {
        $actions = ['edit'];
        if ($status === 0) {
            $actions[] = 'recover';
            $actions[] = 'remove';
            return $actions;
        }

        $actions[] = 'delete';
        $actions[] = 'integral';
        if ($status === 2) {
            $actions[] = 'approve';
            $actions[] = 'decline';
        }
        if ($status === 3) {
            $actions[] = 'unforbid';
        } else {
            $actions[] = 'forbid';
        }
        if ($forbiddenIp === 1) {
            $actions[] = 'lift_ip';
        } else {
            $actions[] = 'forbid_ip';
        }
        return $actions;
    }

    protected function getStatusTabs(): array
    {
        return [
            ['label' => '用户列表', 'value' => 1, 'forbidden_ip' => 0],
            ['label' => '删除列表', 'value' => 0, 'forbidden_ip' => 0],
            ['label' => '待审核', 'value' => 2, 'forbidden_ip' => 0],
            ['label' => '拒绝审核', 'value' => 4, 'forbidden_ip' => 0],
            ['label' => '封禁用户', 'value' => 3, 'forbidden_ip' => 0],
            ['label' => '封禁IP用户', 'value' => 99, 'forbidden_ip' => 1],
        ];
    }

    protected function buildVerifiedOptions(): array
    {
        $options = db('users_verify_type')->where(['status' => 1])->column('title', 'name');
        $options['normal'] = '无';
        return $this->mapOptions($options);
    }

    protected function buildAdminGroupOptions(): array
    {
        return $this->mapOptions(db('admin_group')->column('title', 'id'));
    }

    protected function buildReputationGroupOptions(): array
    {
        return $this->mapOptions(db('users_reputation_group')->column('title', 'id'));
    }

    protected function buildIntegralGroupOptions(): array
    {
        return $this->mapOptions(db('users_integral_group')->column('title', 'id'));
    }

    protected function mapOptions(array $options): array
    {
        $result = [];
        foreach ($options as $value => $label) {
            $result[] = [
                'label' => (string) $label,
                'value' => is_numeric($value) ? intval($value) : (string) $value,
            ];
        }
        return $result;
    }

    protected function formatStatusLabel(int $status, int $forbiddenIp): string
    {
        if ($forbiddenIp === 1) {
            return '封禁IP';
        }

        $labels = [
            0 => '禁用',
            1 => '正常',
            2 => '待审核',
            3 => '已封禁',
            4 => '拒绝审核',
        ];

        return (string) ($labels[$status] ?? '未知');
    }

    protected function formatTime($timestamp): string
    {
        $timestamp = intval($timestamp);
        return $timestamp > 0 ? date('Y-m-d H:i:s', $timestamp) : '-';
    }

    protected function normalizeIds($ids): array
    {
        if (!is_array($ids)) {
            $ids = explode(',', (string) $ids);
        }

        return array_values(array_filter(array_map('intval', $ids)));
    }
}
