<?php

namespace app\common\command;

use app\common\library\agent\ChallengeGenerator;
use app\common\library\agent\ChallengeVerifier;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;

class AgentChallengeTest extends Command
{
    protected function configure()
    {
        $this->setName('agent:challenge:test')
            ->addOption('difficulty', null, Option::VALUE_REQUIRED, 'Challenge difficulty: easy, normal, hard, all', 'all')
            ->addOption('runs', null, Option::VALUE_REQUIRED, 'Number of samples per difficulty', 3)
            ->addOption('show-answer', null, Option::VALUE_NONE, 'Print generated answers for manual verification')
            ->setDescription('Run standalone smoke tests for the agent challenge generator');
    }

    protected function execute(Input $input, Output $output)
    {
        $difficulty = strtolower(trim((string) $input->getOption('difficulty')));
        $runs = max(1, min(20, intval($input->getOption('runs') ?: 3)));
        $showAnswer = (bool) $input->getOption('show-answer');

        $difficulties = $difficulty === 'all'
            ? ChallengeGenerator::supportedDifficulties()
            : [ChallengeGenerator::normalizeDifficulty($difficulty)];

        $total = 0;
        $passed = 0;

        foreach ($difficulties as $itemDifficulty) {
            $output->writeln('Difficulty: ' . $itemDifficulty);
            for ($i = 1; $i <= $runs; $i++) {
                $challenge = ChallengeGenerator::generate($itemDifficulty);
                $errors = ChallengeVerifier::validateDefinition($challenge, $itemDifficulty);
                $positiveCheck = ChallengeVerifier::verify($challenge, (string) ($challenge['answer'] ?? ''));
                $negativeCheck = ChallengeVerifier::verify($challenge, (string) ((intval($challenge['answer'] ?? 0)) + 1));
                $total++;

                if ($errors || empty($positiveCheck['valid']) || !empty($negativeCheck['valid'])) {
                    if (empty($positiveCheck['valid'])) {
                        $errors[] = 'positive verification failed';
                    }
                    if (!empty($negativeCheck['valid'])) {
                        $errors[] = 'negative verification failed';
                    }
                    $output->error(sprintf('  [%d] FAIL %s', $i, implode('; ', $errors)));
                    continue;
                }

                $passed++;
                $line = sprintf('  [%d] PASS %s', $i, $challenge['question']);
                if ($showAnswer) {
                    $line .= ' => ' . $challenge['answer'];
                }
                $output->writeln($line);
            }
        }

        $output->writeln(sprintf('Summary: %d/%d passed', $passed, $total));
        return $passed === $total ? 0 : 1;
    }
}
