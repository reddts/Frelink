<?php

namespace app\model;

class AgentContentMeta extends BaseModel
{
    protected $name = 'agent_content_meta';
    protected $pk = 'id';

    public static function isAvailable(): bool
    {
        return checkTableExist('agent_content_meta');
    }

    public static function recordFromApproval(string $itemType, int $itemId, int $uid, array $data = []): void
    {
        if (!self::isAvailable() || $itemId <= 0 || $itemType === '' || empty($data['is_agent_content'])) {
            return;
        }

        $now = time();
        $payload = [
            'item_type' => trim($itemType),
            'item_id' => $itemId,
            'uid' => max(0, intval($uid)),
            'is_agent_content' => 1,
            'agent_level_snapshot' => max(0, intval($data['agent_level_snapshot'] ?? 0)),
            'agent_badge_snapshot' => trim((string) ($data['agent_badge_snapshot'] ?? '')),
            'agent_display_name_snapshot' => trim((string) ($data['agent_display_name'] ?? '')),
            'protocol_version' => trim((string) ($data['protocol_version'] ?? 'v1')),
            'create_time' => $now,
            'update_time' => $now,
        ];

        $existing = db('agent_content_meta')
            ->where(['item_type' => $payload['item_type'], 'item_id' => $payload['item_id']])
            ->find();

        if ($existing) {
            unset($payload['create_time']);
            db('agent_content_meta')->where('id', intval($existing['id']))->update($payload);
            return;
        }

        db('agent_content_meta')->insert($payload);
    }

    public static function getMetaMap(string $itemType, array $itemIds): array
    {
        if (!self::isAvailable() || !$itemIds) {
            return [];
        }

        $itemIds = array_values(array_unique(array_filter(array_map('intval', $itemIds))));
        if (!$itemIds) {
            return [];
        }

        $rows = db('agent_content_meta')
            ->where('item_type', trim($itemType))
            ->whereIn('item_id', $itemIds)
            ->select()
            ->toArray();

        $result = [];
        foreach ($rows as $row) {
            $result[intval($row['item_id'])] = [
                'is_agent_content' => !empty($row['is_agent_content']) ? 1 : 0,
                'agent_level_snapshot' => intval($row['agent_level_snapshot'] ?? 0),
                'agent_badge_snapshot' => trim((string) ($row['agent_badge_snapshot'] ?? '')),
                'agent_display_name_snapshot' => trim((string) ($row['agent_display_name_snapshot'] ?? '')),
                'protocol_version' => trim((string) ($row['protocol_version'] ?? 'v1')),
            ];
        }

        return $result;
    }

    public static function decorateRows(string $itemType, array $rows, string $userInfoKey = 'user_info'): array
    {
        $defaults = [
            'is_agent_content' => 0,
            'agent_level_snapshot' => 0,
            'agent_badge_snapshot' => '',
            'agent_display_name_snapshot' => '',
            'protocol_version' => '',
        ];

        if (!$rows) {
            return $rows;
        }

        $metaMap = self::getMetaMap($itemType, array_column($rows, 'id'));
        foreach ($rows as $key => $row) {
            $meta = $metaMap[intval($row['id'] ?? 0)] ?? $defaults;
            $rows[$key] = array_merge($row, $meta);
            if (!empty($rows[$key][$userInfoKey]) && !empty($rows[$key]['is_agent_content'])) {
                $rows[$key][$userInfoKey]['is_agent'] = 1;
                $rows[$key][$userInfoKey]['agent_level'] = intval($rows[$key]['agent_level_snapshot'] ?? 0);
                $rows[$key][$userInfoKey]['agent_badge'] = trim((string) ($rows[$key]['agent_badge_snapshot'] ?? ''));
                if (!empty($rows[$key]['agent_display_name_snapshot'])) {
                    $rows[$key][$userInfoKey]['agent_display_name'] = $rows[$key]['agent_display_name_snapshot'];
                }
            }
        }

        return $rows;
    }
}
