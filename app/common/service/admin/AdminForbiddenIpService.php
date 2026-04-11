<?php

namespace app\common\service\admin;

use app\common\library\helper\IpLocation;

class AdminForbiddenIpService
{
    public function getOverview(string $ip = ''): array
    {
        $keyword = trim($ip);

        return [
            'ip' => $keyword,
            'list' => $this->getList($keyword),
        ];
    }

    public function add(string $ips): array
    {
        $items = array_unique(array_filter(array_map('trim', explode(',', $ips))));
        if (!$items) {
            return ['code' => 0, 'msg' => '请填写要封禁的ip'];
        }

        $insertData = [];
        $time = time();
        foreach ($items as $item) {
            if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $item)) {
                $insertData[] = [
                    'uid' => 0,
                    'ip' => $item,
                    'time' => $time,
                ];
            }
        }

        if (!$insertData) {
            return ['code' => 0, 'msg' => '都是无效的ip地址'];
        }

        if (!db('forbidden_ip')->insertAll($insertData)) {
            return ['code' => 0, 'msg' => '操作失败'];
        }

        return ['code' => 1, 'msg' => '操作成功'];
    }

    public function remove($ids): array
    {
        $ids = $this->normalizeIds($ids);
        if (!$ids) {
            return ['code' => 0, 'msg' => '请选择要操作的数据'];
        }

        $rows = db('forbidden_ip')->whereIn('id', $ids)->select()->toArray();
        if (!$rows) {
            return ['code' => 0, 'msg' => '封禁ip不存在'];
        }

        $uids = [];
        foreach ($rows as $row) {
            $uid = intval($row['uid'] ?? 0);
            if ($uid > 0) {
                $uids[] = $uid;
            }
        }

        db('forbidden_ip')->whereIn('id', $ids)->delete();
        if ($uids) {
            db('users')->whereIn('uid', array_values(array_unique($uids)))->update(['forbidden_ip' => 0]);
        }

        return ['code' => 1, 'msg' => '操作成功'];
    }

    protected function getList(string $ip): array
    {
        $query = db('forbidden_ip')->order(['id' => 'desc']);
        if ($ip !== '') {
            $query->where('ip', $ip);
        }

        $list = $query->select()->toArray();
        $ipTool = new IpLocation();

        foreach ($list as &$item) {
            $item['id'] = intval($item['id'] ?? 0);
            $item['uid'] = intval($item['uid'] ?? 0);
            $item['ip'] = (string) ($item['ip'] ?? '');
            $item['time'] = intval($item['time'] ?? 0);
            $item['time_text'] = $item['time'] > 0 ? date('Y-m-d H:i:s', $item['time']) : '-';
            $location = $item['ip'] !== '' ? $ipTool->getLocation($item['ip']) : [];
            $item['address'] = (string) ($location['country'] ?? '-');
        }
        unset($item);

        return $list;
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
}
