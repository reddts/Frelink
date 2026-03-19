<?php
namespace app\common\library\helper;

class SitemapHelper
{
    /**
     * 生成 sitemap.xml
     * @param string $domain 例如 https://example.com
     * @param int $limit 每类内容最大条数
     * @return array
     */
    public static function generate(string $domain = '', int $limit = 500): array
    {
        $start = microtime(true);
        $limit = max(50, min(5000, intval($limit)));
        $domain = self::resolveDomain($domain);
        if (!$domain) {
            $result = ['status' => false, 'message' => 'Sitemap 生成失败：无法识别站点域名'];
            self::writeLog($result, $start, $domain);
            return $result;
        }

        $urls = [];
        $now = date('Y-m-d');

        $staticUrls = [
            ['url' => $domain . '/', 'priority' => '1.0', 'changefreq' => 'daily', 'lastmod' => $now],
            ['url' => $domain . (string)get_url('question/index'), 'priority' => '0.8', 'changefreq' => 'daily', 'lastmod' => $now],
            ['url' => $domain . (string)get_url('article/index'), 'priority' => '0.8', 'changefreq' => 'daily', 'lastmod' => $now],
            ['url' => $domain . (string)get_url('topic/index'), 'priority' => '0.8', 'changefreq' => 'daily', 'lastmod' => $now],
            ['url' => $domain . (string)get_url('index/index',['sort' => 'new']), 'priority' => '0.7', 'changefreq' => 'daily', 'lastmod' => $now],
            ['url' => $domain . (string)get_url('index/index',['sort' => 'hot']), 'priority' => '0.7', 'changefreq' => 'daily', 'lastmod' => $now],
        ];
        foreach ($staticUrls as $item) {
            $urls[] = $item;
        }

        $questionList = db('question')
            ->where(['status' => 1])
            ->field('id,update_time,create_time')
            ->order('update_time', 'desc')
            ->limit($limit)
            ->select()
            ->toArray();
        foreach ($questionList as $item) {
            $urls[] = [
                'url' => $domain . (string)get_url('question/detail', ['id' => $item['id']]),
                'priority' => '0.9',
                'changefreq' => 'daily',
                'lastmod' => self::formatDate($item['update_time'] ?: $item['create_time']),
            ];
        }

        $articleList = db('article')
            ->where(['status' => 1])
            ->field('id,update_time,create_time')
            ->order('update_time', 'desc')
            ->limit($limit)
            ->select()
            ->toArray();
        foreach ($articleList as $item) {
            $urls[] = [
                'url' => $domain . (string)get_url('article/detail', ['id' => $item['id']]),
                'priority' => '0.8',
                'changefreq' => 'weekly',
                'lastmod' => self::formatDate($item['update_time'] ?: $item['create_time']),
            ];
        }

        $topicList = db('topic')
            ->where(['status' => 1])
            ->field('id,create_time,discuss_update')
            ->order('discuss_update', 'desc')
            ->limit($limit)
            ->select()
            ->toArray();
        foreach ($topicList as $item) {
            $urls[] = [
                'url' => $domain . (string)get_url('topic/detail', ['id' => $item['id']]),
                'priority' => '0.7',
                'changefreq' => 'weekly',
                'lastmod' => self::formatDate($item['discuss_update'] ?: $item['create_time']),
            ];
        }

        $urls = self::uniqueUrls($urls);
        $xml = self::buildXml($urls);
        $file = public_path() . 'sitemap.xml';
        file_put_contents($file, $xml);

        $result = [
            'status' => true,
            'message' => 'Sitemap 生成完成',
            'file' => $file,
            'count' => count($urls),
        ];
        self::writeLog($result, $start, $domain);
        return $result;
    }

    protected static function resolveDomain(string $domain): string
    {
        $domain = trim($domain);
        if ($domain) {
            return self::normalizeDomain($domain);
        }

        try {
            $requestDomain = request()->domain();
            if ($requestDomain) {
                return self::normalizeDomain($requestDomain);
            }
        } catch (\Exception $e) {
        }

        $appHost = config('app.app_host', '');
        if ($appHost) {
            return self::normalizeDomain($appHost);
        }

        $cdnUrl = trim((string)get_setting('cdn_url', ''));
        if ($cdnUrl && (strpos($cdnUrl, 'http://') === 0 || strpos($cdnUrl, 'https://') === 0)) {
            return self::normalizeDomain($cdnUrl);
        }

        return '';
    }

    protected static function normalizeDomain(string $domain): string
    {
        $domain = trim($domain);
        if ($domain === '') {
            return '';
        }

        if (strpos($domain, 'http://') !== 0 && strpos($domain, 'https://') !== 0) {
            $domain = 'https://' . ltrim($domain, '/');
        }

        $parts = parse_url($domain);
        if (!$parts || empty($parts['host'])) {
            return rtrim($domain, '/');
        }

        $scheme = strtolower($parts['scheme'] ?? 'https');
        $host = strtolower($parts['host']);
        $port = isset($parts['port']) ? ':' . $parts['port'] : '';
        $path = isset($parts['path']) ? rtrim($parts['path'], '/') : '';

        // Bare domains are normalized onto the www host to avoid invalid host variants in sitemap links.
        if (self::shouldForceWww($host)) {
            $host = 'www.' . $host;
        }

        return $scheme . '://' . $host . $port . $path;
    }

    protected static function shouldForceWww(string $host): bool
    {
        if ($host === '' || strpos($host, 'www.') === 0) {
            return false;
        }

        if ($host === 'localhost' || filter_var($host, FILTER_VALIDATE_IP)) {
            return false;
        }

        return substr_count($host, '.') === 1;
    }

    protected static function formatDate($time): string
    {
        $time = intval($time);
        if (!$time) {
            return date('Y-m-d');
        }
        return date('Y-m-d', $time);
    }

    protected static function uniqueUrls(array $urls): array
    {
        $seen = [];
        $result = [];
        foreach ($urls as $item) {
            $u = trim($item['url'] ?? '');
            if (!$u || isset($seen[$u])) {
                continue;
            }
            $seen[$u] = 1;
            $result[] = $item;
        }
        return $result;
    }

    protected static function buildXml(array $urls): string
    {
        $xml = [];
        $xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls as $item) {
            $loc = htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8');
            $lastmod = htmlspecialchars($item['lastmod'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8');
            $changefreq = htmlspecialchars($item['changefreq'] ?? 'weekly', ENT_QUOTES, 'UTF-8');
            $priority = htmlspecialchars($item['priority'] ?? '0.5', ENT_QUOTES, 'UTF-8');
            $xml[] = '<url>';
            $xml[] = '<loc>' . $loc . '</loc>';
            $xml[] = '<lastmod>' . $lastmod . '</lastmod>';
            $xml[] = '<changefreq>' . $changefreq . '</changefreq>';
            $xml[] = '<priority>' . $priority . '</priority>';
            $xml[] = '</url>';
        }
        $xml[] = '</urlset>';
        return implode("\n", $xml);
    }

    protected static function writeLog(array $result, float $start, string $domain): void
    {
        $cost = round((microtime(true) - $start) * 1000, 2);
        $line = sprintf(
            "[%s] status=%s domain=%s count=%d cost_ms=%s message=%s\n",
            date('Y-m-d H:i:s'),
            !empty($result['status']) ? 'ok' : 'fail',
            $domain ?: '-',
            intval($result['count'] ?? 0),
            $cost,
            str_replace(["\r", "\n"], ' ', (string)($result['message'] ?? ''))
        );

        $logDir = runtime_path() . 'log' . DIRECTORY_SEPARATOR;
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        @file_put_contents($logDir . 'sitemap.log', $line, FILE_APPEND);
    }
}
