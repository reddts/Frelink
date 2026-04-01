<?php

namespace app\common\library\agent;

class ChallengeVerifier
{
    public static function normalizeAnswer(string $answer): string
    {
        $answer = trim($answer);
        $answer = str_replace(["\r", "\n", "\t", ' '], '', $answer);
        return $answer;
    }

    public static function validateDefinition(array $challenge, string $expectedDifficulty = ''): array
    {
        $errors = [];

        if ($expectedDifficulty !== '' && ($challenge['difficulty'] ?? '') !== $expectedDifficulty) {
            $errors[] = 'difficulty mismatch';
        }
        if (empty($challenge['question']) || !is_string($challenge['question'])) {
            $errors[] = 'question missing';
        }
        if (!array_key_exists('answer', $challenge) || self::normalizeAnswer((string) $challenge['answer']) === '') {
            $errors[] = 'answer missing';
        }
        if (empty($challenge['category']) || !is_string($challenge['category'])) {
            $errors[] = 'category missing';
        }

        return $errors;
    }

    public static function verify(array $challenge, string $answer): array
    {
        $definitionErrors = self::validateDefinition($challenge);
        if ($definitionErrors) {
            return [
                'valid' => false,
                'reason' => implode('; ', $definitionErrors),
                'expected_answer' => self::normalizeAnswer((string) ($challenge['answer'] ?? '')),
                'provided_answer' => self::normalizeAnswer($answer),
            ];
        }

        $expected = self::normalizeAnswer((string) $challenge['answer']);
        $provided = self::normalizeAnswer($answer);

        return [
            'valid' => $expected === $provided,
            'reason' => $expected === $provided ? 'ok' : 'answer mismatch',
            'expected_answer' => $expected,
            'provided_answer' => $provided,
        ];
    }
}
