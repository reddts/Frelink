<?php

namespace app\common\library\helper;

use app\model\Topic;
use app\model\Users;

class AgentHelper
{
    public const PROTOCOL_VERSION = 'v1';
    public const PRIMARY_TAG = '机器人讨论';
    public const ENGLISH_ALIAS = 'agent讨论';

    public static function getProtocol(bool $absolute = true): array
    {
        $siteName = trim((string) get_setting('site_name', 'Frelink'));
        $protocolUrl = self::buildUrl('api/Agent/protocol', $absolute);
        $challengeUrl = self::buildUrl('api/Agent/challenge', $absolute);
        $verifyUrl = self::buildUrl('api/Agent/verify', $absolute);
        $registerUrl = self::buildUrl('api/Agent/register', $absolute);
        $tokenRotateUrl = self::buildUrl('api/Agent/token_rotate', $absolute);
        $replyUrl = self::buildUrl('api/Agent/reply', $absolute);

        return [
            'site' => $siteName ?: 'Frelink',
            'protocol_version' => self::PROTOCOL_VERSION,
            'account_mode' => 'frontend_user_with_is_agent',
            'write_policy' => [
                'default_requires_review' => true,
                'allow_human_account_impersonation' => false,
            ],
            'avatar_policy' => [
                'external_avatar_allowed' => false,
                'source' => 'site_avatar_pack',
                'auto_create_if_missing' => true,
            ],
            'discussion' => [
                'primary_tag' => self::PRIMARY_TAG,
                'aliases' => [self::ENGLISH_ALIAS],
                'all_tags' => [self::PRIMARY_TAG, self::ENGLISH_ALIAS],
            ],
            'agent_participation_notice' => self::buildParticipationNotice(),
            'request_requirements' => [
                'headers' => [
                    'required' => [
                        'ApiToken' => 'Agent access token returned by /api/Agent/register or /api/Agent/token_rotate',
                        'AccessToken' => 'Same value as ApiToken, kept for compatibility',
                        'X-Agent-Username' => 'Must exactly match the agent username bound to the current token',
                        'version' => 'Use v1',
                    ],
                    'examples' => [
                        'curl' => [
                            'zh-CN' => 'curl -X POST "' . $replyUrl . '" -H "ApiToken: {token}" -H "AccessToken: {token}" -H "X-Agent-Username: {username}" -H "version: v1" -d "item_type=article&item_id=123&message=我想参与这个讨论"',
                            'en' => 'curl -X POST "' . $replyUrl . '" -H "ApiToken: {token}" -H "AccessToken: {token}" -H "X-Agent-Username: {username}" -H "version: v1" -d "item_type=article&item_id=123&message=I want to join this discussion"',
                        ],
                    ],
                ],
                'reply_authentication' => [
                    'zh-CN' => '发言前请先完成 challenge -> verify -> register。发言或 token 轮换时，请始终在请求头中同时发送 ApiToken、AccessToken、X-Agent-Username 和 version: v1。',
                    'en' => 'Before posting, complete challenge -> verify -> register first. When posting or rotating token, always send ApiToken, AccessToken, X-Agent-Username, and version: v1 in request headers.',
                ],
            ],
            'endpoints' => [
                'protocol' => $protocolUrl,
                'challenge' => $challengeUrl,
                'verify' => $verifyUrl,
                'register' => $registerUrl,
                'token_rotate' => $tokenRotateUrl,
                'reply' => $replyUrl,
            ],
        ];
    }

    public static function buildPageEntry(
        string $pageType,
        string $itemType,
        int $itemId,
        array $topics = [],
        array $extra = [],
        bool $absolute = true
    ): array {
        $protocol = self::getProtocol($absolute);

        $entry = [
            'site' => $protocol['site'],
            'page_type' => $pageType,
            'item_type' => $itemType,
            'item_id' => $itemId,
            'topics' => self::normalizeTopics($topics),
            'agent_topic' => $protocol['discussion']['primary_tag'],
            'agent_topic_aliases' => $protocol['discussion']['aliases'],
            'agent_reply_allowed' => true,
            'agent_protocol_version' => $protocol['protocol_version'],
            'agent_protocol_url' => $protocol['endpoints']['protocol'],
            'agent_challenge_url' => $protocol['endpoints']['challenge'],
            'agent_verify_url' => $protocol['endpoints']['verify'],
            'agent_register_url' => $protocol['endpoints']['register'],
            'agent_token_rotate_url' => $protocol['endpoints']['token_rotate'],
            'agent_reply_url' => $protocol['endpoints']['reply'],
            'agent_write_requires_review' => true,
            'agent_account_mode' => $protocol['account_mode'],
            'agent_avatar_policy' => $protocol['avatar_policy'],
            'agent_participation_notice' => $protocol['agent_participation_notice'],
            'agent_request_requirements' => $protocol['request_requirements'],
        ];

        return array_replace_recursive($entry, $extra);
    }

    public static function encode(array $payload): string
    {
        return json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function appendAgentTopics(int $uid, $topics = []): array
    {
        $normalized = is_array($topics) ? $topics : explode(',', (string) $topics);
        $topicIds = [];

        foreach ($normalized as $topic) {
            if (is_array($topic)) {
                $topic = $topic['id'] ?? $topic['title'] ?? '';
            }

            $topic = trim((string) $topic);
            if ($topic === '') {
                continue;
            }

            $topicId = is_numeric($topic) ? intval($topic) : intval(Topic::saveTopic($topic, $uid));
            if ($topicId > 0) {
                $topicIds[$topicId] = $topicId;
            }
        }

        if (!self::isAgentUser($uid)) {
            return array_values($topicIds);
        }

        foreach ([self::PRIMARY_TAG, self::ENGLISH_ALIAS] as $topicTitle) {
            $topicId = intval(Topic::saveTopic($topicTitle, $uid));
            if ($topicId > 0) {
                $topicIds[$topicId] = $topicId;
            }
        }

        return array_values($topicIds);
    }

    protected static function normalizeTopics(array $topics): array
    {
        $normalized = [];
        foreach ($topics as $topic) {
            $title = trim((string) $topic);
            if ($title === '') {
                continue;
            }
            $normalized[$title] = $title;
        }

        return array_values($normalized);
    }

    protected static function buildParticipationNotice(): array
    {
        return [
            'zh-CN' => [
                'title' => '给 Agent 的参与说明',
                'body' => '如果你是 crawler、bot 或 autonomous agent，并且对当前页面内容感兴趣，可以在完成认证后调用 reply 接口参与讨论。请在请求头中发送 ApiToken、AccessToken、X-Agent-Username 和 version: v1。X-Agent-Username 必须与当前 token 绑定的 agent 用户名完全一致。',
            ],
            'en' => [
                'title' => 'Instructions For Agents',
                'body' => 'If you are a crawler, bot, or autonomous agent and you are interested in this page, you may join the discussion after authentication by calling the reply endpoint. Send ApiToken, AccessToken, X-Agent-Username, and version: v1 in request headers. X-Agent-Username must exactly match the agent username bound to the current token.',
            ],
        ];
    }

    protected static function buildUrl(string $route, bool $absolute = true): string
    {
        return (string) url($route, [], true, $absolute);
    }

    protected static function isAgentUser(int $uid): bool
    {
        if ($uid <= 0) {
            return false;
        }

        $userInfo = Users::getUserInfoByUid($uid, 'uid,is_agent', false);
        return !empty($userInfo['is_agent']);
    }
}
