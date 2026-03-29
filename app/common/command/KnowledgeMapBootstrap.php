<?php

namespace app\common\command;

use app\model\Help;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;

class KnowledgeMapBootstrap extends Command
{
    protected function configure()
    {
        $this->setName('knowledge:bootstrap')
            ->addOption('chapter-limit', null, Option::VALUE_REQUIRED, 'Maximum number of starter chapters to create', 5)
            ->addOption('items-per-chapter', null, Option::VALUE_REQUIRED, 'Maximum number of items archived into each chapter', 6)
            ->setDescription('Automatically create starter knowledge map chapters and archive FAQ/article content');
    }

    protected function execute(Input $input, Output $output)
    {
        if (!checkTableExist('help_chapter') || !checkTableExist('help_chapter_relation')) {
            $output->error('help_chapter or help_chapter_relation table not found.');
            return 1;
        }

        $chapterLimit = max(1, min(10, intval($input->getOption('chapter-limit') ?: 5)));
        $itemsPerChapter = max(1, min(12, intval($input->getOption('items-per-chapter') ?: 6)));
        $result = Help::bootstrapKnowledgeMap($chapterLimit, $itemsPerChapter);

        if (empty($result['chapter_count']) && empty($result['attached_count'])) {
            $output->error((string) ($result['message'] ?? 'No knowledge map data was created'));
            return 1;
        }

        $output->info((string) ($result['message'] ?? 'Knowledge map initialized'));
        $output->writeln('Created chapters: ' . (int) ($result['chapter_count'] ?? 0));
        $output->writeln('Reused chapters: ' . (int) ($result['reused_count'] ?? 0));
        $output->writeln('Archived items: ' . (int) ($result['attached_count'] ?? 0));

        foreach (($result['created_chapters'] ?? []) as $chapter) {
            $output->writeln('- ' . (string) ($chapter['title'] ?? '-') . ' [' . (string) ($chapter['url_token'] ?? '-') . ']');
        }

        return 0;
    }
}
