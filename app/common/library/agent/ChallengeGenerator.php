<?php

namespace app\common\library\agent;

class ChallengeGenerator
{
    public const DEFAULT_DIFFICULTY = 'normal';

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
                return self::buildDerivativeChallenge();
            case 'easy':
                return self::buildAdditionChallenge();
            default:
                return self::buildMixedArithmeticChallenge();
        }
    }

    protected static function buildAdditionChallenge(): array
    {
        $a = random_int(10, 99);
        $b = random_int(10, 99);

        return [
            'difficulty' => 'easy',
            'category' => 'arithmetic',
            'question' => sprintf('请计算 %d + %d 的结果。', $a, $b),
            'answer' => (string) ($a + $b),
        ];
    }

    protected static function buildMixedArithmeticChallenge(): array
    {
        $a = random_int(2, 9);
        $b = random_int(2, 9);
        $c = random_int(2, 9);

        return [
            'difficulty' => 'normal',
            'category' => 'arithmetic',
            'question' => sprintf('请计算 (%d × %d) + %d 的结果。', $a, $b, $c),
            'answer' => (string) (($a * $b) + $c),
        ];
    }

    protected static function buildDerivativeChallenge(): array
    {
        $a = random_int(2, 8);
        $b = random_int(2, 8);
        $x = random_int(2, 6);

        return [
            'difficulty' => 'hard',
            'category' => 'derivative',
            'question' => sprintf('已知 f(x)=%dx^2+%dx，求 f\'(%d) 的值。', $a, $b, $x),
            'answer' => (string) ((2 * $a * $x) + $b),
        ];
    }
}
