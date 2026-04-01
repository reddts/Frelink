<?php

namespace app\model;

use app\common\library\agent\ChallengeGenerator;

class AgentChallengeLog extends BaseModel
{
    protected $name = 'agent_challenge_log';
    protected $pk = 'id';

    public const STATUS_LABELS = [
        'issued' => '已出题',
        'success' => '答对',
        'timeout' => '超时',
        'wrong_answer' => '答错',
        'missing' => '题目失效',
    ];

    public const FAILURE_REASON_LABELS = [
        '' => '无',
        'timeout' => '超时',
        'wrong_answer' => '答案错误',
        'missing' => '题目失效',
    ];

    public static function getStatusOptions(): array
    {
        $result = [];
        foreach (self::STATUS_LABELS as $value => $label) {
            $result[] = [
                'value' => (string) $value,
                'label' => (string) $label,
            ];
        }

        return $result;
    }

    public static function getFailureReasonOptions(): array
    {
        $result = [];
        foreach (self::FAILURE_REASON_LABELS as $value => $label) {
            $result[] = [
                'value' => (string) $value,
                'label' => (string) $label,
            ];
        }

        return $result;
    }

    public static function isAvailable(): bool
    {
        return checkTableExist('agent_challenge_log');
    }

    public static function recordIssued(array $challenge): void
    {
        if (!self::isAvailable() || empty($challenge['challenge_id'])) {
            return;
        }

        $now = time();
        $data = [
            'challenge_id' => (string) $challenge['challenge_id'],
            'uid' => 0,
            'username' => '',
            'difficulty' => (string) ($challenge['difficulty'] ?? ''),
            'category' => (string) ($challenge['category'] ?? ''),
            'question' => (string) ($challenge['question'] ?? ''),
            'status' => 'issued',
            'failure_reason' => '',
            'issued_at' => intval($challenge['issued_at'] ?? $now),
            'deadline' => intval($challenge['deadline'] ?? $now),
            'answered_at' => 0,
            'elapsed_ms' => 0,
            'answer_correct' => 0,
            'create_time' => $now,
            'update_time' => $now,
        ];

        $existingId = db('agent_challenge_log')->where('challenge_id', $data['challenge_id'])->value('id');
        if ($existingId) {
            db('agent_challenge_log')->where('id', intval($existingId))->update($data);
            return;
        }

        db('agent_challenge_log')->insert($data);
    }

    public static function recordResult(string $challengeId, array $payload = []): void
    {
        if (!self::isAvailable() || $challengeId === '') {
            return;
        }

        $now = time();
        $data = [
            'challenge_id' => $challengeId,
            'uid' => intval($payload['uid'] ?? 0),
            'username' => trim((string) ($payload['username'] ?? '')),
            'difficulty' => trim((string) ($payload['difficulty'] ?? '')),
            'category' => trim((string) ($payload['category'] ?? '')),
            'question' => trim((string) ($payload['question'] ?? '')),
            'status' => trim((string) ($payload['status'] ?? 'issued')),
            'failure_reason' => trim((string) ($payload['failure_reason'] ?? '')),
            'issued_at' => intval($payload['issued_at'] ?? 0),
            'deadline' => intval($payload['deadline'] ?? 0),
            'answered_at' => intval($payload['answered_at'] ?? $now),
            'elapsed_ms' => max(0, intval($payload['elapsed_ms'] ?? 0)),
            'answer_correct' => !empty($payload['answer_correct']) ? 1 : 0,
            'create_time' => intval($payload['create_time'] ?? $now),
            'update_time' => $now,
        ];

        $existing = db('agent_challenge_log')->where('challenge_id', $challengeId)->find();
        if ($existing) {
            $existingStatus = trim((string) ($existing['status'] ?? ''));
            if ($existingStatus !== '' && $existingStatus !== 'issued') {
                return;
            }

            unset($data['challenge_id'], $data['create_time']);
            foreach (['difficulty', 'category', 'question', 'issued_at', 'deadline'] as $field) {
                if ($data[$field] === '' || $data[$field] === 0) {
                    unset($data[$field]);
                }
            }
            if ($data['username'] === '') {
                unset($data['username']);
            }
            if (!$data['uid']) {
                unset($data['uid']);
            }
            db('agent_challenge_log')->where('challenge_id', $challengeId)->update($data);
            return;
        }

        db('agent_challenge_log')->insert($data);
    }

    public static function bindLogsToUser(int $uid, string $username): void
    {
        if (!self::isAvailable() || $uid <= 0 || $username === '') {
            return;
        }

        db('agent_challenge_log')
            ->where('uid', 0)
            ->where('username', $username)
            ->update([
                'uid' => $uid,
                'update_time' => time(),
            ]);
    }

    public static function getStatsByUid(int $uid): array
    {
        $default = [
            'total_count' => 0,
            'success_count' => 0,
            'failure_count' => 0,
            'pass_rate' => 0,
            'avg_response_ms' => 0,
            'best_response_ms' => 0,
            'recent_response_ms' => 0,
            'consecutive_success_count' => 0,
            'last_challenge_at' => 0,
        ];

        if (!self::isAvailable() || $uid <= 0) {
            return $default;
        }

        $rows = db('agent_challenge_log')
            ->where('uid', $uid)
            ->whereIn('status', ['success', 'timeout', 'wrong_answer', 'missing'])
            ->order('answered_at', 'desc')
            ->select()
            ->toArray();

        if (!$rows) {
            return $default;
        }

        $total = count($rows);
        $successCount = 0;
        $failureCount = 0;
        $responseTotal = 0;
        $responseCount = 0;
        $best = 0;
        $recent = 0;
        $consecutiveSuccess = 0;

        foreach ($rows as $index => $row) {
            $status = (string) ($row['status'] ?? '');
            $elapsed = max(0, intval($row['elapsed_ms'] ?? 0));

            if ($status === 'success') {
                $successCount++;
                if ($index === $consecutiveSuccess) {
                    $consecutiveSuccess++;
                }
            } else {
                $failureCount++;
            }

            if ($elapsed > 0) {
                if ($recent === 0) {
                    $recent = $elapsed;
                }
                $responseTotal += $elapsed;
                $responseCount++;
                if ($best === 0 || $elapsed < $best) {
                    $best = $elapsed;
                }
            }
        }

        return [
            'total_count' => $total,
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'pass_rate' => $total > 0 ? round(($successCount / $total) * 100, 2) : 0,
            'avg_response_ms' => $responseCount > 0 ? intval(round($responseTotal / $responseCount)) : 0,
            'best_response_ms' => $best,
            'recent_response_ms' => $recent,
            'consecutive_success_count' => $consecutiveSuccess,
            'last_challenge_at' => intval($rows[0]['answered_at'] ?? 0),
        ];
    }

    public static function syncUserStats(int $uid): void
    {
        if ($uid <= 0) {
            return;
        }

        $stats = self::getStatsByUid($uid);
        if (!$stats['total_count']) {
            return;
        }

        Users::updateUserFiled($uid, [
            'agent_challenge_total' => intval($stats['total_count'] ?? 0),
            'agent_challenge_success' => intval($stats['success_count'] ?? 0),
            'agent_challenge_failure' => intval($stats['failure_count'] ?? 0),
            'agent_pass_rate' => floatval($stats['pass_rate'] ?? 0),
            'agent_avg_response_ms' => intval($stats['avg_response_ms'] ?? 0),
            'agent_success_streak' => intval($stats['consecutive_success_count'] ?? 0),
            'agent_best_response_ms' => intval($stats['best_response_ms'] ?? 0),
            'agent_recent_response_ms' => intval($stats['recent_response_ms'] ?? 0),
            'agent_last_challenge_at' => intval($stats['last_challenge_at'] ?? 0),
        ]);
    }

    public static function getOverview(int $startAt = 0, int $endAt = 0): array
    {
        $default = [
            'total_logs' => 0,
            'unique_usernames' => 0,
            'bound_agent_count' => 0,
            'issued_count' => 0,
            'success_count' => 0,
            'failure_count' => 0,
            'pass_rate' => 0,
            'avg_response_ms' => 0,
            'avg_success_response_ms' => 0,
            'recent_test_at' => 0,
            'top_failure_reasons' => [],
            'top_difficulties' => [],
            'top_categories' => [],
            'top_usernames' => [],
            'daily_stats' => [],
            'ttl_timeout_stats' => [],
        ];

        if (!self::isAvailable()) {
            return $default;
        }

        $query = db('agent_challenge_log');
        if ($startAt > 0) {
            $query->where('update_time', '>=', $startAt);
        }
        if ($endAt > 0) {
            $query->where('update_time', '<=', $endAt);
        }
        $rows = $query->select()->toArray();
        if (!$rows) {
            return $default;
        }

        $usernames = [];
        $boundUids = [];
        $responseTotal = 0;
        $responseCount = 0;
        $successResponseTotal = 0;
        $successResponseCount = 0;
        $issuedCount = 0;
        $successCount = 0;
        $failureCount = 0;
        $recentTestAt = 0;
        $failureReasonCounts = [];
        $difficultyCounts = [];
        $categoryCounts = [];
        $usernameCounts = [];
        $dailyStats = [];
        $ttlTimeoutStats = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime('-' . $i . ' day'));
            $dailyStats[$day] = [
                'day' => $day,
                'issued_count' => 0,
                'success_count' => 0,
                'failure_count' => 0,
                'response_count' => 0,
                'response_total_ms' => 0,
                'success_response_count' => 0,
                'success_response_total_ms' => 0,
            ];
        }

        foreach ($rows as $row) {
            $status = trim((string) ($row['status'] ?? ''));
            $username = trim((string) ($row['username'] ?? ''));
            $uid = intval($row['uid'] ?? 0);
            $elapsedMs = max(0, intval($row['elapsed_ms'] ?? 0));
            $answeredAt = intval($row['answered_at'] ?? 0);
            $issuedAt = intval($row['issued_at'] ?? 0);
            $recentCandidate = max($answeredAt, $issuedAt);
            $difficulty = trim((string) ($row['difficulty'] ?? ''));
            $category = trim((string) ($row['category'] ?? ''));

            if ($username !== '') {
                $usernames[$username] = true;
                $usernameCounts[$username] = intval($usernameCounts[$username] ?? 0) + 1;
            }
            if ($uid > 0) {
                $boundUids[$uid] = true;
            }
            if ($recentCandidate > $recentTestAt) {
                $recentTestAt = $recentCandidate;
            }
            if ($difficulty !== '') {
                $difficultyCounts[$difficulty] = intval($difficultyCounts[$difficulty] ?? 0) + 1;
                if (!isset($ttlTimeoutStats[$difficulty])) {
                    $ttlTimeoutStats[$difficulty] = [
                        'difficulty' => $difficulty,
                        'ttl_seconds' => ChallengeGenerator::getTtlByDifficulty($difficulty),
                        'target_response_ms' => ChallengeGenerator::getTargetResponseMsByDifficulty($difficulty),
                        'issued_count' => 0,
                        'resolved_count' => 0,
                        'timeout_count' => 0,
                    ];
                }
            }
            if ($category !== '') {
                $categoryCounts[$category] = intval($categoryCounts[$category] ?? 0) + 1;
            }

            if ($status === 'issued') {
                $issuedCount++;
                if ($difficulty !== '' && isset($ttlTimeoutStats[$difficulty])) {
                    $ttlTimeoutStats[$difficulty]['issued_count']++;
                }
            } elseif ($status === 'success') {
                $successCount++;
                if ($difficulty !== '' && isset($ttlTimeoutStats[$difficulty])) {
                    $ttlTimeoutStats[$difficulty]['resolved_count']++;
                }
            } elseif (in_array($status, ['timeout', 'wrong_answer', 'missing'], true)) {
                $failureCount++;
                $reason = trim((string) ($row['failure_reason'] ?? $status));
                $failureReasonCounts[$reason] = intval($failureReasonCounts[$reason] ?? 0) + 1;
                if ($difficulty !== '' && isset($ttlTimeoutStats[$difficulty])) {
                    $ttlTimeoutStats[$difficulty]['resolved_count']++;
                    if ($status === 'timeout' || $reason === 'timeout') {
                        $ttlTimeoutStats[$difficulty]['timeout_count']++;
                    }
                }
            }

            $dailyKey = $recentCandidate > 0 ? date('Y-m-d', $recentCandidate) : '';
            if ($dailyKey !== '' && isset($dailyStats[$dailyKey])) {
                if ($status === 'issued') {
                    $dailyStats[$dailyKey]['issued_count']++;
                } elseif ($status === 'success') {
                    $dailyStats[$dailyKey]['success_count']++;
                } elseif (in_array($status, ['timeout', 'wrong_answer', 'missing'], true)) {
                    $dailyStats[$dailyKey]['failure_count']++;
                }
                if ($elapsedMs > 0 && $status !== 'issued') {
                    $dailyStats[$dailyKey]['response_count']++;
                    $dailyStats[$dailyKey]['response_total_ms'] += $elapsedMs;
                    if ($status === 'success') {
                        $dailyStats[$dailyKey]['success_response_count']++;
                        $dailyStats[$dailyKey]['success_response_total_ms'] += $elapsedMs;
                    }
                }
            }

            if ($elapsedMs > 0 && $status !== 'issued') {
                $responseTotal += $elapsedMs;
                $responseCount++;
                if ($status === 'success') {
                    $successResponseTotal += $elapsedMs;
                    $successResponseCount++;
                }
            }
        }

        arsort($failureReasonCounts);
        arsort($difficultyCounts);
        arsort($categoryCounts);
        arsort($usernameCounts);
        $topFailureReasons = [];
        foreach (array_slice($failureReasonCounts, 0, 3, true) as $reason => $count) {
            $topFailureReasons[] = [
                'value' => $reason,
                'label' => self::FAILURE_REASON_LABELS[$reason] ?? $reason,
                'count' => intval($count),
            ];
        }
        $topDifficulties = [];
        foreach (array_slice($difficultyCounts, 0, 3, true) as $difficulty => $count) {
            $topDifficulties[] = [
                'value' => $difficulty,
                'label' => $difficulty,
                'count' => intval($count),
            ];
        }
        $topCategories = [];
        foreach (array_slice($categoryCounts, 0, 3, true) as $category => $count) {
            $topCategories[] = [
                'value' => $category,
                'label' => $category,
                'count' => intval($count),
            ];
        }
        $topUsernames = [];
        foreach (array_slice($usernameCounts, 0, 5, true) as $username => $count) {
            $topUsernames[] = [
                'value' => $username,
                'label' => $username,
                'count' => intval($count),
            ];
        }
        $ttlTimeoutRows = [];
        foreach (ChallengeGenerator::supportedDifficulties() as $difficulty) {
            $stat = $ttlTimeoutStats[$difficulty] ?? [
                'difficulty' => $difficulty,
                'ttl_seconds' => ChallengeGenerator::getTtlByDifficulty($difficulty),
                'target_response_ms' => ChallengeGenerator::getTargetResponseMsByDifficulty($difficulty),
                'issued_count' => 0,
                'resolved_count' => 0,
                'timeout_count' => 0,
            ];
            $resolvedCount = intval($stat['resolved_count'] ?? 0);
            $timeoutCount = intval($stat['timeout_count'] ?? 0);
            $ttlTimeoutRows[] = [
                'difficulty' => (string) $difficulty,
                'label' => (string) $difficulty,
                'ttl_seconds' => intval($stat['ttl_seconds'] ?? 0),
                'target_response_ms' => intval($stat['target_response_ms'] ?? 0),
                'issued_count' => intval($stat['issued_count'] ?? 0),
                'resolved_count' => $resolvedCount,
                'timeout_count' => $timeoutCount,
                'timeout_rate' => $resolvedCount > 0 ? round(($timeoutCount / $resolvedCount) * 100, 2) : 0,
            ];
        }

        $dailyStats = array_map(static function ($item) {
            $finishedCount = intval($item['success_count'] ?? 0) + intval($item['failure_count'] ?? 0);
            $responseCount = intval($item['response_count'] ?? 0);
            $successResponseCount = intval($item['success_response_count'] ?? 0);

            $item['pass_rate'] = $finishedCount > 0 ? round((intval($item['success_count'] ?? 0) / $finishedCount) * 100, 2) : 0;
            $item['avg_response_ms'] = $responseCount > 0 ? intval(round(intval($item['response_total_ms'] ?? 0) / $responseCount)) : 0;
            $item['avg_success_response_ms'] = $successResponseCount > 0 ? intval(round(intval($item['success_response_total_ms'] ?? 0) / $successResponseCount)) : 0;
            unset(
                $item['response_count'],
                $item['response_total_ms'],
                $item['success_response_count'],
                $item['success_response_total_ms']
            );

            return $item;
        }, array_values($dailyStats));

        return [
            'total_logs' => count($rows),
            'unique_usernames' => count($usernames),
            'bound_agent_count' => count($boundUids),
            'issued_count' => $issuedCount,
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'pass_rate' => ($successCount + $failureCount) > 0 ? round(($successCount / ($successCount + $failureCount)) * 100, 2) : 0,
            'avg_response_ms' => $responseCount > 0 ? intval(round($responseTotal / $responseCount)) : 0,
            'avg_success_response_ms' => $successResponseCount > 0 ? intval(round($successResponseTotal / $successResponseCount)) : 0,
            'recent_test_at' => $recentTestAt,
            'recent_test_at_text' => $recentTestAt > 0 ? date('Y-m-d H:i:s', $recentTestAt) : '',
            'top_failure_reasons' => $topFailureReasons,
            'top_difficulties' => $topDifficulties,
            'top_categories' => $topCategories,
            'top_usernames' => $topUsernames,
            'daily_stats' => $dailyStats,
            'ttl_timeout_stats' => $ttlTimeoutRows,
        ];
    }

    public static function getLogsByRange(int $startAt = 0, int $endAt = 0, int $limit = 50): array
    {
        if (!self::isAvailable()) {
            return [];
        }

        $limit = max(1, min(200, $limit));
        $query = db('agent_challenge_log')->order('id', 'desc');
        if ($startAt > 0) {
            $query->where('update_time', '>=', $startAt);
        }
        if ($endAt > 0) {
            $query->where('update_time', '<=', $endAt);
        }

        $rows = $query->limit($limit)->select()->toArray();
        foreach ($rows as $key => $row) {
            $rows[$key]['status_label'] = self::STATUS_LABELS[$row['status'] ?? ''] ?? (string) ($row['status'] ?? '');
            $rows[$key]['failure_reason_label'] = self::FAILURE_REASON_LABELS[$row['failure_reason'] ?? ''] ?? (string) ($row['failure_reason'] ?? '');
            $rows[$key]['status_meta'] = [
                'value' => (string) ($row['status'] ?? ''),
                'label' => (string) ($rows[$key]['status_label'] ?? ''),
            ];
            $rows[$key]['failure_reason_meta'] = [
                'value' => (string) ($row['failure_reason'] ?? ''),
                'label' => (string) ($rows[$key]['failure_reason_label'] ?? ''),
            ];
            $rows[$key]['issued_at_text'] = !empty($row['issued_at']) ? date('Y-m-d H:i:s', intval($row['issued_at'])) : '';
            $rows[$key]['deadline_text'] = !empty($row['deadline']) ? date('Y-m-d H:i:s', intval($row['deadline'])) : '';
            $rows[$key]['answered_at_text'] = !empty($row['answered_at']) ? date('Y-m-d H:i:s', intval($row['answered_at'])) : '';
            $rows[$key]['update_time_text'] = !empty($row['update_time']) ? date('Y-m-d H:i:s', intval($row['update_time'])) : '';
        }

        return $rows;
    }
}
