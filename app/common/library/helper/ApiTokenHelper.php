<?php

namespace app\common\library\helper;

use app\model\Users;

class ApiTokenHelper
{
    public static function decodeCandidates(string $token): array
    {
        $token = trim($token);
        if ($token === '') {
            return [];
        }

        $candidates = [$token];
        $decoded = authCode($token);
        if (is_string($decoded) && $decoded !== '') {
            $candidates[] = $decoded;
        }

        return array_values(array_unique(array_filter($candidates)));
    }

    public static function resolveAppToken(string $token, ?string $version = null): array
    {
        return self::resolveByType($token, [1, 3], $version, null);
    }

    public static function resolvePluginToken(string $token, string $plugin): array
    {
        return self::resolveByType($token, [2], null, $plugin);
    }

    protected static function resolveByType(string $token, array $types, ?string $version = null, ?string $plugin = null): array
    {
        $candidates = self::decodeCandidates($token);
        if (!$candidates) {
            return [];
        }

        $rows = db('app_token')
            ->whereIn('token', $candidates)
            ->whereIn('type', $types)
            ->select()
            ->toArray();

        if (!$rows) {
            return [];
        }

        $now = time();
        foreach ($rows as $row) {
            if (intval($row['status'] ?? 1) !== 1) {
                continue;
            }

            $expireTime = intval($row['expire_time'] ?? 0);
            if ($expireTime > 0 && $expireTime <= $now) {
                continue;
            }

            $type = intval($row['type'] ?? 0);
            $rowVersion = trim((string) ($row['version'] ?? ''));
            if ($type === 1 && $rowVersion !== '' && $rowVersion !== trim((string) $version)) {
                continue;
            }

            if ($type === 2 && $plugin !== null && $plugin !== '' && trim((string) ($row['plugin'] ?? '')) !== trim($plugin)) {
                continue;
            }

            if ($type === 3 && ($row['uid'] ?? 0) > 0) {
                $userInfo = Users::getUserInfo(intval($row['uid']));
                if (!$userInfo) {
                    continue;
                }
                $row['user_info'] = $userInfo;
            }

            self::touch($row);
            return $row;
        }

        return [];
    }

    public static function touch(array $tokenInfo): void
    {
        if (empty($tokenInfo['id'])) {
            return;
        }

        db('app_token')->where('id', intval($tokenInfo['id']))->update([
            'last_use_time' => time(),
            'last_use_ip' => request()->ip(),
        ]);
    }

    public static function buildToken(): string
    {
        try {
            return bin2hex(random_bytes(16));
        } catch (\Throwable $e) {
            return RandomHelper::alnum(32);
        }
    }
}
