<?php
namespace app\common\command;

use app\common\library\helper\SitemapHelper;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;

class Sitemap extends Command
{
    public function configure()
    {
        $this->setName('sitemap:build');
        $this->addOption('domain', null, Option::VALUE_OPTIONAL, 'Sitemap domain, e.g. https://example.com');
        $this->addOption('limit', null, Option::VALUE_OPTIONAL, 'Max rows per content type', 500);
        $this->setDescription('Build sitemap.xml for search engine indexing');
    }

    public function execute(Input $input, Output $output)
    {
        $domain = (string)$input->getOption('domain');
        $limit = intval($input->getOption('limit'));
        $result = SitemapHelper::generate($domain, $limit);
        if (!empty($result['status'])) {
            $output->writeln('Sitemap build success: ' . ($result['file'] ?? 'public/sitemap.xml') . ', total ' . intval($result['count']) . ' urls');
            return 0;
        }
        $output->error($result['message'] ?? 'Sitemap build failed');
        return 1;
    }
}

