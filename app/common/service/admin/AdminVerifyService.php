<?php

namespace app\common\service\admin;

class AdminVerifyService
{
    public function getOverview(int $status = 1, string $type = ''): array
    {
        $allowedStatus = [1, 2, 3];
        if (!in_array($status, $allowedStatus, true)) {
            $status = 1;
        }

        return [
            'status' => $status,
            'type' => $type,
            'status_tabs' => [
                ['label' => '待审核', 'value' => 1],
                ['label' => '已审核', 'value' => 2],
                ['label' => '已拒绝', 'value' => 3],
            ],
            'type_tabs' => $this->getTypeTabs(),
            'list' => $this->getList($status, $type),
        ];
    }

    public function getDetail(int $id): array
    {
        if ($id <= 0) {
            return [];
        }

        $info = db('users_verify')
            ->alias('v')
            ->leftJoin('users u', 'v.uid = u.uid')
            ->where('v.id', $id)
            ->field('v.*,u.user_name,u.nick_name,u.url_token')
            ->find();

        if (!$info) {
            return [];
        }

        $payload = json_decode((string) ($info['data'] ?? '{}'), true);
        if (!is_array($payload)) {
            $payload = [];
        }

        return [
            'id' => intval($info['id'] ?? 0),
            'uid' => intval($info['uid'] ?? 0),
            'user_name' => (string) ($info['user_name'] ?? ''),
            'nick_name' => (string) ($info['nick_name'] ?? ''),
            'url_token' => (string) ($info['url_token'] ?? ''),
            'type' => (string) ($info['type'] ?? ''),
            'type_label' => $this->getTypeLabel((string) ($info['type'] ?? '')),
            'status' => intval($info['status'] ?? 0),
            'reason' => (string) ($info['reason'] ?? ''),
            'create_time_text' => $this->formatTime($info['create_time'] ?? 0),
            'preview_fields' => $this->buildPreviewFields((string) ($info['type'] ?? ''), $payload),
            'payload' => $payload,
            'payload_json' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
        ];
    }

    public function approve($ids): array
    {
        $ids = $this->normalizeIds($ids);
        if (!$ids) {
            return ['code' => 0, 'msg' => '请选择要操作的数据'];
        }

        $records = db('users_verify')->whereIn('id', $ids)->select()->toArray();
        if (!$records) {
            return ['code' => 0, 'msg' => '审核数据不存在'];
        }

        foreach ($records as $record) {
            $uid = intval($record['uid'] ?? 0);
            $verifyType = (string) ($record['type'] ?? '');
            db('users_verify')->where('id', intval($record['id']))->update(['status' => 2, 'reason' => '']);
            if ($uid > 0) {
                db('users')->where('uid', $uid)->update(['verified' => $verifyType]);
            }
            hook('userVerifyApproval', $record);
        }

        return ['code' => 1, 'msg' => '操作成功'];
    }

    public function decline($ids, string $reason): array
    {
        $ids = $this->normalizeIds($ids);
        if (!$ids) {
            return ['code' => 0, 'msg' => '请选择要操作的数据'];
        }

        $records = db('users_verify')->whereIn('id', $ids)->select()->toArray();
        if (!$records) {
            return ['code' => 0, 'msg' => '审核数据不存在'];
        }

        $declineReason = trim($reason);
        foreach ($records as $record) {
            db('users_verify')
                ->where('id', intval($record['id']))
                ->update(['status' => 3, 'reason' => $declineReason]);

            hook('userVerifyRefuse', [
                'post_data' => [
                    'id' => intval($record['id']),
                    'reason' => $declineReason,
                ],
                'info' => $record,
            ]);
        }

        return ['code' => 1, 'msg' => '操作成功'];
    }

    protected function getList(int $status, string $type): array
    {
        $query = db('users_verify')
            ->alias('v')
            ->leftJoin('users u', 'v.uid = u.uid')
            ->where('v.status', $status)
            ->field('v.id,v.uid,v.type,v.status,v.reason,v.data,v.create_time,u.user_name,u.nick_name,u.url_token');

        if ($type !== '') {
            $query->where('v.type', $type);
        }

        $list = $query->order(['v.id' => 'desc'])->select()->toArray();

        foreach ($list as &$item) {
            $payload = json_decode((string) ($item['data'] ?? '{}'), true);
            if (!is_array($payload)) {
                $payload = [];
            }

            $item['id'] = intval($item['id'] ?? 0);
            $item['uid'] = intval($item['uid'] ?? 0);
            $item['status'] = intval($item['status'] ?? 0);
            $item['type'] = (string) ($item['type'] ?? '');
            $item['type_label'] = $this->getTypeLabel($item['type']);
            $item['summary'] = $this->buildSummary($payload);
            $item['create_time_text'] = $this->formatTime($item['create_time'] ?? 0);
            unset($item['data']);
        }
        unset($item);

        return $list;
    }

    protected function buildSummary(array $payload): string
    {
        if (!$payload) {
            return '认证资料';
        }

        foreach (['real_name', 'name', 'id_card', 'company', 'title'] as $key) {
            $value = trim((string) ($payload[$key] ?? ''));
            if ($value !== '') {
                return $value;
            }
        }

        foreach ($payload as $value) {
            if (is_scalar($value)) {
                $text = trim((string) $value);
                if ($text !== '') {
                    return $text;
                }
            }
        }

        return '认证资料';
    }

    protected function buildPreviewFields(string $type, array $payload): array
    {
        $fields = db('verify_field')
            ->where('verify_type', $type)
            ->order(['sort' => 'asc', 'id' => 'asc'])
            ->select()
            ->toArray();

        $result = [];
        foreach ($fields as $field) {
            $name = (string) ($field['name'] ?? '');
            if ($name === '') {
                continue;
            }

            $value = $payload[$name] ?? '';
            if (is_array($value)) {
                $value = implode(' / ', array_map(static fn($item) => trim((string) $item), $value));
            }

            $value = trim((string) $value);
            if ($value === '') {
                continue;
            }

            $result[] = [
                'label' => (string) ($field['title'] ?? $name),
                'value' => $value,
            ];
        }

        if (!$result) {
            foreach ($payload as $key => $value) {
                if (is_array($value)) {
                    $value = implode(' / ', array_map(static fn($item) => trim((string) $item), $value));
                }
                $text = trim((string) $value);
                if ($text === '') {
                    continue;
                }
                $result[] = [
                    'label' => (string) $key,
                    'value' => $text,
                ];
            }
        }

        return $result;
    }

    protected function getTypeTabs(): array
    {
        $tabs = [['label' => '全部', 'value' => '']];
        $types = db('users_verify_type')->where(['status' => 1])->column('title', 'name');
        foreach ($types as $name => $title) {
            $tabs[] = [
                'label' => (string) $title,
                'value' => (string) $name,
            ];
        }

        return $tabs;
    }

    protected function getTypeLabel(string $type): string
    {
        if ($type === '') {
            return '-';
        }

        $label = db('users_verify_type')->where('name', $type)->value('title');
        return (string) ($label ?: $type);
    }

    protected function normalizeIds($ids): array
    {
        if (is_array($ids)) {
            $values = $ids;
        } else {
            $values = explode(',', (string) $ids);
        }

        $result = [];
        foreach ($values as $value) {
            $id = intval($value);
            if ($id > 0) {
                $result[] = $id;
            }
        }

        return array_values(array_unique($result));
    }

    protected function formatTime($timestamp): string
    {
        $value = intval($timestamp);
        return $value > 0 ? date('Y-m-d H:i:s', $value) : '-';
    }
}
