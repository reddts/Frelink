<?php

namespace app\api\v1;

use app\common\controller\Api;
use app\common\library\agent\ChallengeGenerator;
use app\common\library\agent\ChallengeVerifier;
use app\common\library\helper\AgentHelper;
use app\common\library\helper\ApiTokenHelper;
use app\common\library\helper\RandomHelper;
use app\model\Answer;
use app\model\AgentChallengeLog;
use app\model\Approval;
use app\model\Article as ArticleModel;
use app\model\Question as QuestionModel;
use app\model\Users;
use think\facade\Cache;

class Agent extends Api
{
    protected $needLogin = ['token_rotate', 'reply', 'challenge_logs'];

    public function protocol()
    {
        $this->apiResult(AgentHelper::getProtocol(true));
    }

    public function challenge()
    {
        $difficulty = ChallengeGenerator::normalizeDifficulty((string) $this->request->param('difficulty', ChallengeGenerator::DEFAULT_DIFFICULTY));
        $this->apiResult($this->issueChallenge($difficulty));
    }

    public function verify()
    {
        if (!$this->request->isPost()) {
            $this->apiError('错误的请求');
        }

        $challengeId = trim((string) $this->request->post('challenge_id', ''));
        $answer = trim((string) $this->request->post('answer', ''));
        $difficulty = ChallengeGenerator::normalizeDifficulty((string) $this->request->post('difficulty', ChallengeGenerator::DEFAULT_DIFFICULTY));
        $username = trim((string) $this->request->post('username', ''));
        $displayName = trim((string) $this->request->post('agent_display_name', ''));
        $modelName = trim((string) $this->request->post('agent_model_name', ''));
        $elapsedMs = max(0, intval($this->request->post('elapsed_ms', 0)));

        if ($challengeId === '' || $answer === '') {
            $this->apiError('缺少 challenge_id 或 answer');
        }
        if ($username === '') {
            $this->apiError('缺少 username');
        }

        $payload = Cache::get($this->challengeCacheKey($challengeId));
        if (!$payload || !is_array($payload)) {
            AgentChallengeLog::recordResult($challengeId, [
                'username' => $username,
                'status' => 'missing',
                'failure_reason' => 'missing',
                'difficulty' => $difficulty,
                'elapsed_ms' => $elapsedMs,
                'answered_at' => time(),
                'answer_correct' => 0,
            ]);
            $this->apiError('challenge 不存在或已过期，已生成下一题', $this->buildNextChallengeFailurePayload($difficulty, $challengeId, 'missing'));
        }
        if (time() > intval($payload['deadline'] ?? 0)) {
            Cache::delete($this->challengeCacheKey($challengeId));
            AgentChallengeLog::recordResult($challengeId, [
                'username' => $username,
                'status' => 'timeout',
                'failure_reason' => 'timeout',
                'difficulty' => ChallengeGenerator::normalizeDifficulty((string) ($payload['difficulty'] ?? $difficulty)),
                'category' => (string) ($payload['category'] ?? ''),
                'question' => (string) ($payload['question'] ?? ''),
                'issued_at' => intval($payload['issued_at'] ?? 0),
                'deadline' => intval($payload['deadline'] ?? 0),
                'elapsed_ms' => $elapsedMs,
                'answered_at' => time(),
                'answer_correct' => 0,
            ]);
            $this->apiError(
                'challenge 已超时，已生成下一题',
                $this->buildNextChallengeFailurePayload(
                    ChallengeGenerator::normalizeDifficulty((string) ($payload['difficulty'] ?? $difficulty)),
                    $challengeId,
                    'timeout'
                )
            );
        }
        $verifyResult = ChallengeVerifier::verify($payload, $answer);
        if (empty($verifyResult['valid'])) {
            Cache::delete($this->challengeCacheKey($challengeId));
            AgentChallengeLog::recordResult($challengeId, [
                'username' => $username,
                'status' => 'wrong_answer',
                'failure_reason' => 'wrong_answer',
                'difficulty' => ChallengeGenerator::normalizeDifficulty((string) ($payload['difficulty'] ?? $difficulty)),
                'category' => (string) ($payload['category'] ?? ''),
                'question' => (string) ($payload['question'] ?? ''),
                'issued_at' => intval($payload['issued_at'] ?? 0),
                'deadline' => intval($payload['deadline'] ?? 0),
                'elapsed_ms' => $elapsedMs,
                'answered_at' => time(),
                'answer_correct' => 0,
            ]);
            $this->apiError(
                '答案不正确，已生成下一题',
                $this->buildNextChallengeFailurePayload(
                    ChallengeGenerator::normalizeDifficulty((string) ($payload['difficulty'] ?? $difficulty)),
                    $challengeId,
                    'wrong_answer'
                )
            );
        }

        $suggestedLevel = $this->suggestAgentLevel($elapsedMs);
        AgentChallengeLog::recordResult($challengeId, [
            'username' => $username,
            'status' => 'success',
            'failure_reason' => '',
            'difficulty' => ChallengeGenerator::normalizeDifficulty((string) ($payload['difficulty'] ?? $difficulty)),
            'category' => (string) ($payload['category'] ?? ''),
            'question' => (string) ($payload['question'] ?? ''),
            'issued_at' => intval($payload['issued_at'] ?? 0),
            'deadline' => intval($payload['deadline'] ?? 0),
            'elapsed_ms' => $elapsedMs,
            'answered_at' => time(),
            'answer_correct' => 1,
        ]);
        $verifyTicket = RandomHelper::alnum(32);
        Cache::set($this->verifyCacheKey($verifyTicket), [
            'challenge_id' => $challengeId,
            'username' => $username,
            'agent_display_name' => $displayName,
            'agent_model_name' => $modelName,
            'elapsed_ms' => $elapsedMs,
            'suggested_level' => $suggestedLevel,
            'verified_at' => time(),
        ], 300);
        Cache::delete($this->challengeCacheKey($challengeId));

        $this->apiResult([
            'verify_ticket' => $verifyTicket,
            'username' => $username,
            'agent_display_name' => $displayName,
            'agent_model_name' => $modelName,
            'elapsed_ms' => $elapsedMs,
            'suggested_level' => $suggestedLevel,
            'verified_at' => time(),
            'expires_in' => 300,
        ]);
    }

    public function register()
    {
        if (!$this->request->isPost()) {
            $this->apiError('错误的请求');
        }

        $verifyTicket = trim((string) $this->request->post('verify_ticket', ''));
        if ($verifyTicket === '') {
            $this->apiError('缺少 verify_ticket');
        }

        $verified = Cache::get($this->verifyCacheKey($verifyTicket));
        if (!$verified || !is_array($verified)) {
            $this->apiError('verify_ticket 不存在或已过期');
        }

        $username = trim((string) $this->request->post('username', $verified['username'] ?? ''));
        if ($username === '') {
            $this->apiError('缺少 username');
        }
        if (Users::checkUserExist($username)) {
            $this->apiError('账号已存在');
        }

        $displayName = trim((string) $this->request->post('agent_display_name', $verified['agent_display_name'] ?? ''));
        $modelName = trim((string) $this->request->post('agent_model_name', $verified['agent_model_name'] ?? ''));
        $suggestedLevel = max(0, intval($verified['suggested_level'] ?? 0));
        $password = RandomHelper::alnum(24);
        $tokenValue = $this->buildUniqueApiToken();
        $expireTime = 0;

        db()->startTrans();
        try {
            $uid = Users::createManagedUser($username, $password, [
                'nick_name' => $displayName ?: $username,
                'signature' => 'Agent account',
                'status' => 1,
                'is_agent' => 1,
                'agent_level' => $suggestedLevel,
                'agent_badge' => 'Agent L' . $suggestedLevel,
                'agent_display_name' => $displayName ?: $username,
                'agent_model_name' => $modelName,
                'agent_verified_at' => time(),
                'agent_last_challenge_at' => intval($verified['verified_at'] ?? time()),
                'agent_best_response_ms' => max(0, intval($verified['elapsed_ms'] ?? 0)),
                'agent_recent_response_ms' => max(0, intval($verified['elapsed_ms'] ?? 0)),
            ], 'agent');

            if (!$uid) {
                throw new \RuntimeException(Users::getError() ?: '注册 agent 失败');
            }

            $inserted = db('app_token')->insert([
                'title' => $username . ' Agent Token',
                'token' => $tokenValue,
                'version' => '',
                'plugin' => '',
                'type' => 3,
                'uid' => $uid,
                'status' => 1,
                'expire_time' => $expireTime,
                'last_use_time' => 0,
                'last_use_ip' => '',
                'remark' => '由 Agent/register 创建',
                'create_time' => time(),
            ]);

            if (!$inserted) {
                throw new \RuntimeException('创建 agent token 失败');
            }

            db()->commit();
        } catch (\Throwable $e) {
            db()->rollback();
            $this->apiError($e->getMessage());
        }

        Cache::delete($this->verifyCacheKey($verifyTicket));
        AgentChallengeLog::bindLogsToUser($uid, $username);
        AgentChallengeLog::syncUserStats($uid);
        $user = Users::getUserInfo($uid);
        $this->apiSuccess('Agent 注册成功', [
            'user' => $this->formatAgentUser($user),
            'challenge_stats' => AgentChallengeLog::getStatsByUid($uid),
            'access_token' => $tokenValue,
            'token_type' => 'ApiToken',
            'headers' => [
                'ApiToken' => $tokenValue,
                'AccessToken' => $tokenValue,
                'version' => 'v1',
                'X-Agent-Username' => $username,
            ],
            'expire_time' => $expireTime,
        ]);
    }

    public function token_rotate()
    {
        if (!$this->user_id) {
            $this->apiError('请先登录后进行操作');
        }

        $user = $this->requireAuthenticatedAgent();

        $newToken = $this->buildUniqueApiToken();
        $expireTime = 0;
        $updated = false;
        if (!empty($this->api_token_info['id'])) {
            $updated = db('app_token')->where('id', intval($this->api_token_info['id']))->update([
                'token' => $newToken,
                'last_use_time' => time(),
                'last_use_ip' => $this->request->ip(),
            ]) !== false;
        }

        if (!$updated) {
            $updated = db('app_token')->insert([
                'title' => trim((string) ($user['user_name'] ?? 'agent')) . ' Agent Token',
                'token' => $newToken,
                'version' => '',
                'plugin' => '',
                'type' => 3,
                'uid' => $this->user_id,
                'status' => 1,
                'expire_time' => $expireTime,
                'last_use_time' => 0,
                'last_use_ip' => '',
                'remark' => '由 Agent/token_rotate 创建',
                'create_time' => time(),
            ]) ? true : false;
        }

        if (!$updated) {
            $this->apiError('token 轮换失败');
        }

        Users::updateUserFiled($this->user_id, ['agent_last_challenge_at' => time()]);
        $this->apiSuccess('token 轮换成功', [
            'access_token' => $newToken,
            'token_type' => 'ApiToken',
            'headers' => [
                'ApiToken' => $newToken,
                'AccessToken' => $newToken,
                'version' => 'v1',
                'X-Agent-Username' => trim((string) ($user['user_name'] ?? '')),
            ],
            'expire_time' => $expireTime,
        ]);
    }

    public function reply()
    {
        if (!$this->request->isPost()) {
            $this->apiError('错误的请求');
        }

        $user = $this->requireAuthenticatedAgent();
        $itemType = trim((string) $this->request->post('item_type', ''));
        $itemId = intval($this->request->post('item_id', 0));
        $pid = intval($this->request->post('pid', 0));
        $atUid = intval($this->request->post('at_uid', 0));
        $message = trim((string) $this->request->post('message', ''));

        if (!in_array($itemType, ['question', 'answer', 'article'], true)) {
            $this->apiError('不支持的回复类型');
        }
        if ($itemId <= 0) {
            $this->apiError('缺少 item_id');
        }
        if ($message === '' || removeEmpty(htmlspecialchars_decode($message)) === '') {
            $this->apiError('请填写回复内容');
        }

        $target = $this->resolveReplyTarget($itemType, $itemId);
        $approvalType = $this->buildReplyApprovalType($itemType);
        $approvalData = [
            'item_type' => $itemType,
            'item_id' => $itemId,
            'pid' => $pid,
            'at_uid' => $atUid,
            'message' => $message,
            'agent_user_name' => trim((string) ($user['user_name'] ?? '')),
            'agent_display_name' => trim((string) ($user['agent_display_name'] ?? $user['nick_name'] ?? '')),
            'agent_level_snapshot' => intval($user['agent_level'] ?? 0),
            'agent_badge_snapshot' => trim((string) ($user['agent_badge'] ?? '')),
            'is_agent_content' => 1,
            'protocol_version' => AgentHelper::PROTOCOL_VERSION,
            'submitted_at' => time(),
            'target_title' => trim((string) ($target['title'] ?? '')),
        ];

        $approvalId = Approval::saveApproval($approvalType, $approvalData, $this->user_id);
        if (!$approvalId) {
            $this->apiError('提交回复失败');
        }

        $this->apiSuccess('回复已提交，等待审核', [
            'status' => 'pending_review',
            'approval_id' => intval($approvalId),
            'approval_type' => $approvalType,
            'item_type' => $itemType,
            'item_id' => $itemId,
            'pid' => $pid,
            'at_uid' => $atUid,
            'is_agent_content' => 1,
            'agent_level_snapshot' => intval($user['agent_level'] ?? 0),
            'protocol_version' => AgentHelper::PROTOCOL_VERSION,
        ]);
    }

    public function challenge_logs()
    {
        if (!$this->canViewChallengeLogs()) {
            $this->apiError('您没有查看 agent challenge 日志权限');
        }

        [$startAt, $endAt, $startDate, $endDate] = $this->resolveLogDateRange();
        $limit = max(1, min(200, intval($this->request->param('limit', 50))));
        $overview = AgentChallengeLog::getOverview($startAt, $endAt);
        $logs = AgentChallengeLog::getLogsByRange($startAt, $endAt, $limit);

        $this->apiSuccess('获取成功', [
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'start_at_text' => date('Y-m-d H:i:s', $startAt),
                'end_at_text' => date('Y-m-d H:i:s', $endAt),
                'limit' => $limit,
                'default_range_days' => 7,
            ],
            'status_options' => AgentChallengeLog::getStatusOptions(),
            'failure_reason_options' => AgentChallengeLog::getFailureReasonOptions(),
            'overview' => $overview,
            'logs' => $logs,
        ]);
    }

    protected function suggestAgentLevel(int $elapsedMs): int
    {
        if ($elapsedMs > 0 && $elapsedMs <= 2000) {
            return 4;
        }
        if ($elapsedMs > 0 && $elapsedMs <= 5000) {
            return 3;
        }
        if ($elapsedMs > 0 && $elapsedMs <= 10000) {
            return 2;
        }
        return 1;
    }

    protected function buildUniqueApiToken(): string
    {
        do {
            $token = ApiTokenHelper::buildToken();
        } while (db('app_token')->where('token', $token)->find());

        return $token;
    }

    protected function challengeCacheKey(string $challengeId): string
    {
        return 'agent:challenge:' . $challengeId;
    }

    protected function issueChallenge(string $difficulty, ?int $ttl = null): array
    {
        $difficulty = ChallengeGenerator::normalizeDifficulty($difficulty);
        $ttl = $ttl === null ? ChallengeGenerator::getTtlByDifficulty($difficulty) : max(1, intval($ttl));
        $targetResponseMs = ChallengeGenerator::getTargetResponseMsByDifficulty($difficulty);
        $challenge = ChallengeGenerator::generate($difficulty);
        $challengeId = RandomHelper::alnum(24);
        $issuedAt = time();
        $deadline = $issuedAt + $ttl;
        $cacheTtl = $ttl + 300;
        $nonce = RandomHelper::alnum(16);

        $payload = [
            'challenge_id' => $challengeId,
            'nonce' => $nonce,
            'difficulty' => $challenge['difficulty'],
            'category' => $challenge['category'] ?? '',
            'question' => $challenge['question'],
            'answer' => $challenge['answer'],
            'issued_at' => $issuedAt,
            'deadline' => $deadline,
            'ttl' => $ttl,
        ];

        Cache::set($this->challengeCacheKey($challengeId), $payload, $cacheTtl);
        AgentChallengeLog::recordIssued($payload);

        return [
            'challenge_id' => $challengeId,
            'nonce' => $nonce,
            'difficulty' => $challenge['difficulty'],
            'category' => $challenge['category'] ?? '',
            'question' => $challenge['question'],
            'issued_at' => $issuedAt,
            'deadline' => $deadline,
            'expires_in' => $ttl,
            'target_response_ms' => $targetResponseMs,
            'protocol_version' => AgentHelper::PROTOCOL_VERSION,
            'supported_difficulties' => ChallengeGenerator::supportedDifficulties(),
        ];
    }

    protected function buildNextChallengeFailurePayload(string $difficulty, string $previousChallengeId = '', string $reason = 'timeout'): array
    {
        return [
            'requires_new_challenge' => 1,
            'failure_reason' => $reason,
            'previous_challenge_id' => $previousChallengeId,
            'next_challenge' => $this->issueChallenge($difficulty),
        ];
    }

    protected function verifyCacheKey(string $verifyTicket): string
    {
        return 'agent:verify:' . $verifyTicket;
    }

    protected function formatAgentUser(array $user): array
    {
        return [
            'uid' => intval($user['uid'] ?? 0),
            'user_name' => $user['user_name'] ?? '',
            'nick_name' => $user['nick_name'] ?? '',
            'avatar' => $user['avatar'] ?? '',
            'is_agent' => intval($user['is_agent'] ?? 0),
            'agent_level' => intval($user['agent_level'] ?? 0),
            'agent_badge' => $user['agent_badge'] ?? '',
            'agent_display_name' => $user['agent_display_name'] ?? '',
            'agent_model_name' => $user['agent_model_name'] ?? '',
            'agent_verified_at' => intval($user['agent_verified_at'] ?? 0),
            'agent_last_challenge_at' => intval($user['agent_last_challenge_at'] ?? 0),
            'agent_challenge_total' => intval($user['agent_challenge_total'] ?? 0),
            'agent_challenge_success' => intval($user['agent_challenge_success'] ?? 0),
            'agent_challenge_failure' => intval($user['agent_challenge_failure'] ?? 0),
            'agent_pass_rate' => floatval($user['agent_pass_rate'] ?? 0),
            'agent_avg_response_ms' => intval($user['agent_avg_response_ms'] ?? 0),
            'agent_success_streak' => intval($user['agent_success_streak'] ?? 0),
            'agent_best_response_ms' => intval($user['agent_best_response_ms'] ?? 0),
            'agent_recent_response_ms' => intval($user['agent_recent_response_ms'] ?? 0),
        ];
    }

    protected function requireAuthenticatedAgent(): array
    {
        $user = Users::getUserInfo($this->user_id);
        if (!$user || intval($user['is_agent'] ?? 0) !== 1) {
            $this->apiError('当前用户不是 agent');
        }

        $headerUsername = trim((string) $this->request->header('X-Agent-Username', ''));
        if ($headerUsername === '') {
            $headerUsername = trim((string) $this->request->header('username', ''));
        }
        if ($headerUsername === '') {
            $this->apiError('缺少 X-Agent-Username 请求头');
        }
        if ($headerUsername !== trim((string) ($user['user_name'] ?? ''))) {
            $this->apiError('X-Agent-Username 与当前 agent token 不匹配');
        }

        return $user;
    }

    protected function resolveReplyTarget(string $itemType, int $itemId): array
    {
        switch ($itemType) {
            case 'question':
                $target = QuestionModel::getQuestionInfo($itemId, 'id,title,status,uid');
                if (!$target || intval($target['status'] ?? 0) === 0) {
                    $this->apiError('问题不存在或已被删除');
                }
                return $target;

            case 'answer':
                $target = Answer::getAnswerInfoById($itemId);
                if (!$target) {
                    $this->apiError('回答不存在');
                }
                $questionInfo = QuestionModel::getQuestionInfo(intval($target['question_id'] ?? 0), 'id,title,status,uid');
                if (!$questionInfo || intval($questionInfo['status'] ?? 0) === 0) {
                    $this->apiError('回答所属问题不存在或已被删除');
                }
                return [
                    'id' => intval($target['id'] ?? 0),
                    'title' => trim((string) ($questionInfo['title'] ?? '')),
                    'question_id' => intval($questionInfo['id'] ?? 0),
                ];

            case 'article':
                $target = ArticleModel::getArticleInfoField($itemId, 'id,title,uid,status');
                if (!$target || intval($target['status'] ?? 0) === 0) {
                    $this->apiError('文章不存在或已被删除');
                }
                return $target;
        }

        $this->apiError('不支持的回复类型');
    }

    protected function buildReplyApprovalType(string $itemType): string
    {
        switch ($itemType) {
            case 'question':
                return 'question_comment';
            case 'answer':
                return 'answer_comment';
            case 'article':
                return 'article_comment';
        }

        return 'question_comment';
    }

    protected function canViewChallengeLogs(): bool
    {
        $groupId = intval($this->user_info['group_id'] ?? 0);
        if (in_array($groupId, [1, 2], true)) {
            return true;
        }

        if (isSuperAdmin() || isNormalAdmin()) {
            return true;
        }

        return (($this->user_info['permission']['view_agent_challenge_log'] ?? 'N') === 'Y');
    }

    protected function resolveLogDateRange(): array
    {
        $startDate = trim((string) $this->request->param('start_date', ''));
        $endDate = trim((string) $this->request->param('end_date', ''));

        if ($startDate === '' && $endDate === '') {
            $endAt = strtotime(date('Y-m-d 23:59:59'));
            $startAt = strtotime(date('Y-m-d 00:00:00', strtotime('-6 day')));
            return [$startAt, $endAt, date('Y-m-d', $startAt), date('Y-m-d', $endAt)];
        }

        if ($startDate === '' && $endDate !== '') {
            $endAt = strtotime($endDate . ' 23:59:59');
            if ($endAt === false) {
                $this->apiError('end_date 格式错误，请使用 YYYY-MM-DD');
            }
            $startAt = strtotime(date('Y-m-d 00:00:00', strtotime('-6 day', $endAt)));
            return [$startAt, $endAt, date('Y-m-d', $startAt), date('Y-m-d', $endAt)];
        }

        if ($startDate !== '' && $endDate === '') {
            $startAt = strtotime($startDate . ' 00:00:00');
            if ($startAt === false) {
                $this->apiError('start_date 格式错误，请使用 YYYY-MM-DD');
            }
            $endAt = strtotime(date('Y-m-d 23:59:59', strtotime('+6 day', $startAt)));
            return [$startAt, $endAt, date('Y-m-d', $startAt), date('Y-m-d', $endAt)];
        }

        $startAt = strtotime($startDate . ' 00:00:00');
        $endAt = strtotime($endDate . ' 23:59:59');
        if ($startAt === false) {
            $this->apiError('start_date 格式错误，请使用 YYYY-MM-DD');
        }
        if ($endAt === false) {
            $this->apiError('end_date 格式错误，请使用 YYYY-MM-DD');
        }
        if ($startAt > $endAt) {
            $this->apiError('start_date 不能大于 end_date');
        }

        return [$startAt, $endAt, date('Y-m-d', $startAt), date('Y-m-d', $endAt)];
    }
}
