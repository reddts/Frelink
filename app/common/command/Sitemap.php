<?php

namespace app\common\command;

use app\common\library\helper\SitemapHelper;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;

class Sitemap extends Command
{
    protected function configure()
    {
        $this->setName('sitemap:build')
            ->addOption('base-url', null, Option::VALUE_REQUIRED, 'The site base URL, e.g. https://www.frelink.top')
            ->setDescription('Build sitemap.xml for the current Frelink instance');
    }

    protected function execute(Input $input, Output $output)
    {
        $baseUrl = rtrim((string) $input->getOption('base-url'), '/');
        if ($baseUrl === '') {
            $output->error('Missing required option: --base-url=https://your-domain');
            return 1;
        }

        $result = SitemapHelper::generate($baseUrl, 5000);
        if (empty($result['status'])) {
            $output->error((string) ($result['message'] ?? 'Sitemap generation failed'));
            return 1;
        }

        $output->info((string) ($result['message'] ?? 'Sitemap generated'));
        $output->writeln('File: ' . (string) ($result['file'] ?? ''));
        $output->writeln('URL count: ' . (int) ($result['count'] ?? 0));
        return 0;
    }
}
