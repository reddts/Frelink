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

        $report = [
            'window_days' => $days,
            'generated_at' => date('Y-m-d H:i:s'),
            'summary' => Insight::getWindowSummary($days),
            'opportunities' => Insight::getSearchOpportunities($days, $limit),
            'content' => Insight::getContentTrends($days, $limit),
            'topics' => Insight::getTopicTrends($days, $limit),
            'recommendations' => Insight::getRecommendations($days, $limit),
        ];

        if ($format === 'json') {
            $output->writeln(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            return 0;
        }

        $output->writeln('# Frelink 运营报告');
        $output->writeln('');
        $output->writeln('- 统计窗口：最近 ' . $days . ' 天');
        $output->writeln('- 生成时间：' . $report['generated_at']);
        $output->writeln('- 搜索次数：' . intval($report['summary']['search_count'] ?? 0));
        $output->writeln('- 曝光次数：' . intval($report['summary']['impression_count'] ?? 0));
        $output->writeln('- 点击次数：' . intval($report['summary']['click_count'] ?? 0));
        $output->writeln('- 详情阅读：' . intval($report['summary']['detail_view_count'] ?? 0));
        $output->writeln('- 窗口 CTR：' . (string) ($report['summary']['ctr'] ?? 0));
        $output->writeln('');

        $this->renderTableSection($output, '搜索缺口', $report['opportunities'], function ($row) {
            return '- `' . $row['keyword'] . '` 搜索 ' . $row['search_count'] . ' 次，覆盖 ' . $row['matched_content_count'] . '，建议：' . $row['suggestion'];
        });

        $this->renderTableSection($output, '内容热点', $report['content'], function ($row) {
            return '- [' . $row['item_type'] . '] ' . $row['title'] . '，曝光 ' . $row['impressions'] . '，点击 ' . $row['clicks'] . '，阅读 ' . $row['detail_views'] . '，CTR ' . $row['ctr'];
        });

        $this->renderTableSection($output, '主题热点', $report['topics'], function ($row) {
            return '- ' . $row['title'] . '，阅读 ' . $row['detail_views'] . '，内容数 ' . $row['content_count'] . '，CTR ' . $row['ctr'];
        });

        $this->renderTableSection($output, 'Agent 建议', $report['recommendations'], function ($row) {
            return '- [' . $row['priority'] . '] ' . $row['title'] . '：' . $row['suggestion'];
        });

        return 0;
    }

    protected function renderTableSection(Output $output, string $title, array $rows, callable $formatter): void
    {
        $output->writeln('## ' . $title);
        if (!$rows) {
            $output->writeln('- 暂无数据');
            $output->writeln('');
            return;
        }

        foreach ($rows as $row) {
            $output->writeln($formatter($row));
        }
        $output->writeln('');
    }
}
