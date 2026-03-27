<?php

namespace app\common\command;

use app\model\Insight;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;

class InsightReport extends Command
{
    protected function configure()
    {
        $this->setName('insight:report')
            ->addOption('days', null, Option::VALUE_REQUIRED, 'Rolling insight window: 1, 3, 7, 30', 7)
            ->addOption('limit', null, Option::VALUE_REQUIRED, 'Rows per section', 5)
            ->addOption('format', null, Option::VALUE_REQUIRED, 'Output format: markdown or json', 'markdown')
            ->addOption('save', null, Option::VALUE_NONE, 'Persist the generated report snapshot to runtime/insight/daily')
            ->setDescription('Generate a rolling operations report for AI-assisted publishing and curation');
    }

    protected function execute(Input $input, Output $output)
    {
        if (!checkTableExist('analytics_event')) {
            $output->error('analytics_event table not found. Run docs/analytics-upgrade.sql first.');
            return 1;
        }

        $days = Insight::normalizeDays(intval($input->getOption('days') ?: 7));
        $limit = max(1, min(20, intval($input->getOption('limit') ?: 5)));
        $format = strtolower(trim((string) $input->getOption('format')));
        if (!in_array($format, ['markdown', 'json'], true)) {
            $output->error('Invalid format. Use --format=markdown or --format=json');
            return 1;
        }

        $report = Insight::buildDailyReport($days, $limit);
        if ($input->hasOption('save') && $input->getOption('save')) {
            $report = Insight::storeDailyReport($days, $limit);
        }

        if ($format === 'json') {
            $output->writeln(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            return 0;
        }

        $output->writeln('# Frelink 运营报告');
        $output->writeln('');
        $output->writeln(Insight::renderDailyReport($report));

        return 0;
    }
}
