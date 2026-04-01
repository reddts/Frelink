<?php

namespace app\common\library\agent;

class ChallengeGenerator
{
    public const DEFAULT_DIFFICULTY = 'normal';
    public const TTL_MAP = [
        'easy' => 12,
        'normal' => 20,
        'hard' => 35,
    ];
    public const TARGET_RESPONSE_MS_MAP = [
        'easy' => 8000,
        'normal' => 15000,
        'hard' => 25000,
    ];

    public static function supportedDifficulties(): array
    {
        return ['easy', 'normal', 'hard'];
    }

    public static function normalizeDifficulty(string $difficulty): string
    {
        $difficulty = strtolower(trim($difficulty));
        return in_array($difficulty, self::supportedDifficulties(), true) ? $difficulty : self::DEFAULT_DIFFICULTY;
    }

    public static function generate(string $difficulty = self::DEFAULT_DIFFICULTY): array
    {
        $difficulty = self::normalizeDifficulty($difficulty);

        switch ($difficulty) {
            case 'hard':
                return self::buildSecondDerivativeChallenge();
            case 'easy':
                return self::buildSingleVariableQuadraticChallenge();
            default:
                return self::buildTwoVariableQuadraticChallenge();
        }
    }

    public static function getTtlByDifficulty(string $difficulty): int
    {
        $difficulty = self::normalizeDifficulty($difficulty);
        return intval(self::TTL_MAP[$difficulty] ?? self::TTL_MAP[self::DEFAULT_DIFFICULTY]);
    }

    public static function getTargetResponseMsByDifficulty(string $difficulty): int
    {
        $difficulty = self::normalizeDifficulty($difficulty);
        return intval(self::TARGET_RESPONSE_MS_MAP[$difficulty] ?? self::TARGET_RESPONSE_MS_MAP[self::DEFAULT_DIFFICULTY]);
    }

    protected static function buildSingleVariableQuadraticChallenge(): array
    {
        $x1 = random_int(1, 9);
        do {
            $x2 = random_int(1, 9);
        } while ($x2 === $x1);

        $sum = $x1 + $x2;
        $product = $x1 * $x2;
        $smallerRoot = min($x1, $x2);

        return [
            'difficulty' => 'easy',
            'category' => 'single_variable_quadratic',
            'question' => sprintf('已知方程 x^2-%dx+%d=0，求较小的整数根。', $sum, $product),
            'answer' => (string) $smallerRoot,
        ];
    }

    protected static function buildTwoVariableQuadraticChallenge(): array
    {
        $x = random_int(1, 9);
        do {
            $y = random_int(1, 9);
        } while ($y === $x);

        $small = min($x, $y);
        $sum = $x + $y;
        $squareSum = ($x * $x) + ($y * $y);

        return [
            'difficulty' => 'normal',
            'category' => 'two_variable_quadratic',
            'question' => sprintf("已知方程组 x+y=%d，x^2+y^2=%d，求较小的整数解 x。", $sum, $squareSum),
            'answer' => (string) $small,
        ];
    }

    protected static function buildSecondDerivativeChallenge(): array
    {
        $a = random_int(1, 4);
        $b = random_int(1, 5);
        $c = random_int(1, 6);
        $d = random_int(1, 6);
        $x = random_int(1, 4);

        return [
            'difficulty' => 'hard',
            'category' => 'second_derivative',
            'question' => sprintf('已知 f(x)=%dx^4+%dx^3+%dx^2+%dx，求 f\'\'(%d) 的值。', $a, $b, $c, $d, $x),
            'answer' => (string) ((12 * $a * $x * $x) + (6 * $b * $x) + (2 * $c)),
        ];
    }
}
