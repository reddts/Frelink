<?php
// +----------------------------------------------------------------------
// | WeCenter 简称 WC
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://wecenter.isimpo.com
// +----------------------------------------------------------------------
// | WeCenter团队一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: WeCenter团队 <devteam@wecenter.com>
// +----------------------------------------------------------------------

namespace app\common\library\helper;

class HtmlHelper
{
    /**
     * 统一清理富文本内容中常见的编辑器残留属性与空链接
     * @param string|null $content
     * @return string
     */
    public static function normalizeContentHtml(?string $content): string
    {
        $content = (string) $content;
        if ($content === '') {
            return '';
        }

        $patterns = [
            '/\sdata-[a-z0-9_-]+=(["\']).*?\1/is',
            '/\sdir=(["\']).*?\1/is',
            '/\scontenteditable=(["\']).*?\1/is',
            '/\sstyle=(["\'])\s*(?:background-color:\s*rgb\(255,\s*255,\s*255\);?\s*|font-size:\s*1rem;?\s*)+\1/is',
            '/<a\b([^>]*)\shref=(["\'])\s*\2([^>]*)>(.*?)<\/a>/is',
            '/<a\b([^>]*)>(\s*)<\/a>/is',
        ];

        $replacements = [
            '',
            '',
            '',
            '',
            '<span$1$3>$4</span>',
            '$2',
        ];

        $content = preg_replace($patterns, $replacements, $content);
        $content = preg_replace('/<pre>\s*(?:(?:<div[^>]*>\s*)|(?:<\/div>\s*))+\s*<code([^>]*)>/is', '<pre><code$1>', $content);
        $content = preg_replace('/<\/code>\s*(?:<\/div>\s*)+<\/pre>/is', '</code></pre>', $content);
        $content = preg_replace('/<pre>\s*(?:<div>\s*<\/div>\s*)+/is', '<pre>', $content);
        $content = preg_replace('/<pre>\s*<code([^>]*)>\s*/is', '<pre><code$1>', $content);
        $content = preg_replace('/\s*<\/code>\s*<\/pre>/is', '</code></pre>', $content);
        $content = preg_replace('/<p>\s*<\/p>/is', '', $content);

        return $content ?: '';
    }

    /**
     * 将 Markdown 渲染为适合前台展示的 HTML
     * 仅支持当前项目文档页所需的常用语法，避免额外引入依赖
     */
    public static function markdownToHtml(string $markdown): string
    {
        $markdown = str_replace(["\r\n", "\r"], "\n", trim($markdown));
        if ($markdown === '') {
            return '';
        }

        $lines = explode("\n", $markdown);
        $html = [];
        $paragraph = [];
        $orderedList = [];
        $unorderedList = [];
        $tableRows = [];
        $inCode = false;
        $codeLang = '';
        $codeLines = [];

        $flushParagraph = function () use (&$paragraph, &$html): void {
            if (!$paragraph) {
                return;
            }
            $text = trim(implode(' ', $paragraph));
            if ($text !== '') {
                $html[] = '<p>' . self::renderMarkdownInline($text) . '</p>';
            }
            $paragraph = [];
        };

        $flushOrderedList = function () use (&$orderedList, &$html): void {
            if (!$orderedList) {
                return;
            }
            $html[] = '<ol>';
            foreach ($orderedList as $item) {
                $html[] = '<li>' . self::renderMarkdownInline($item) . '</li>';
            }
            $html[] = '</ol>';
            $orderedList = [];
        };

        $flushUnorderedList = function () use (&$unorderedList, &$html): void {
            if (!$unorderedList) {
                return;
            }
            $html[] = '<ul>';
            foreach ($unorderedList as $item) {
                $html[] = '<li>' . self::renderMarkdownInline($item) . '</li>';
            }
            $html[] = '</ul>';
            $unorderedList = [];
        };

        $flushTable = function () use (&$tableRows, &$html): void {
            if (!$tableRows) {
                return;
            }
            $rows = array_values(array_filter($tableRows, function ($row) {
                $trimmed = trim($row);
                return $trimmed !== '' && !preg_match('/^\|[\s:\-|]+\|$/', $trimmed);
            }));
            if (!$rows) {
                $tableRows = [];
                return;
            }
            $parsed = array_map(function ($row) {
                $row = trim($row);
                $row = trim($row, '|');
                return array_map('trim', explode('|', $row));
            }, $rows);
            $header = array_shift($parsed);
            $html[] = '<div class="table-responsive"><table class="table table-bordered table-sm markdown-table">';
            if ($header) {
                $html[] = '<thead><tr>';
                foreach ($header as $cell) {
                    $html[] = '<th>' . self::renderMarkdownInline($cell) . '</th>';
                }
                $html[] = '</tr></thead>';
            }
            if ($parsed) {
                $html[] = '<tbody>';
                foreach ($parsed as $row) {
                    $html[] = '<tr>';
                    foreach ($row as $cell) {
                        $html[] = '<td>' . self::renderMarkdownInline($cell) . '</td>';
                    }
                    $html[] = '</tr>';
                }
                $html[] = '</tbody>';
            }
            $html[] = '</table></div>';
            $tableRows = [];
        };

        foreach ($lines as $line) {
            $trimmed = rtrim($line);

            if (preg_match('/^```(\w+)?\s*$/', $trimmed, $matches)) {
                $flushParagraph();
                $flushOrderedList();
                $flushUnorderedList();
                if ($inCode) {
                    $code = htmlspecialchars(implode("\n", $codeLines), ENT_QUOTES, 'UTF-8');
                    $class = $codeLang !== '' ? ' class="language-' . htmlspecialchars($codeLang, ENT_QUOTES, 'UTF-8') . '"' : '';
                    $html[] = '<pre><code' . $class . '>' . $code . '</code></pre>';
                    $inCode = false;
                    $codeLang = '';
                    $codeLines = [];
                } else {
                    $flushTable();
                    $inCode = true;
                    $codeLang = $matches[1] ?? '';
                }
                continue;
            }

            if ($inCode) {
                $codeLines[] = $line;
                continue;
            }

            if ($trimmed === '') {
                $flushParagraph();
                $flushOrderedList();
                $flushUnorderedList();
                $flushTable();
                continue;
            }

            if (preg_match('/^\|.*\|$/', $trimmed)) {
                $flushParagraph();
                $flushOrderedList();
                $flushUnorderedList();
                $tableRows[] = $trimmed;
                continue;
            }

            if (preg_match('/^#{1,6}\s+(.+)$/', $trimmed, $matches)) {
                $flushParagraph();
                $flushOrderedList();
                $flushUnorderedList();
                $flushTable();
                $level = min(6, max(1, substr_count($trimmed, '#', 0, strspn($trimmed, '#'))));
                $text = trim($matches[1]);
                $html[] = '<h' . $level . '>' . self::renderMarkdownInline($text) . '</h' . $level . '>';
                continue;
            }

            if (preg_match('/^\d+\.\s+(.+)$/', $trimmed, $matches)) {
                $flushParagraph();
                $flushUnorderedList();
                $flushTable();
                $orderedList[] = trim($matches[1]);
                continue;
            }

            if (preg_match('/^[-*]\s+(.+)$/', $trimmed, $matches)) {
                $flushParagraph();
                $flushOrderedList();
                $flushTable();
                $unorderedList[] = trim($matches[1]);
                continue;
            }

            if (preg_match('/^>\s?(.*)$/', $trimmed, $matches)) {
                $flushParagraph();
                $flushOrderedList();
                $flushUnorderedList();
                $flushTable();
                $html[] = '<blockquote>' . self::renderMarkdownInline(trim($matches[1])) . '</blockquote>';
                continue;
            }

            $flushTable();
            if ($orderedList || $unorderedList) {
                $flushOrderedList();
                $flushUnorderedList();
            }
            $paragraph[] = $trimmed;
        }

        if ($inCode) {
            $code = htmlspecialchars(implode("\n", $codeLines), ENT_QUOTES, 'UTF-8');
            $class = $codeLang !== '' ? ' class="language-' . htmlspecialchars($codeLang, ENT_QUOTES, 'UTF-8') . '"' : '';
            $html[] = '<pre><code' . $class . '>' . $code . '</code></pre>';
        }

        $flushParagraph();
        $flushOrderedList();
        $flushUnorderedList();
        $flushTable();

        return implode("\n", $html);
    }

    protected static function renderMarkdownInline(string $text): string
    {
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);
        $text = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $text);
        $text = preg_replace('/\*(?!\s)([^*]+)\*/', '<em>$1</em>', $text);
        $text = preg_replace('/\[(.+?)\]\((https?:\/\/[^\s)]+)\)/', '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>', $text);
        return $text;
    }

	/**
	 * 替换内容中的图片地址
	 * @param string $content 内容原始html
	 * @return string 返回替换后的内容
	 */
	public static function parseImgUrl(string $content,$url=''): string
    {
        $images = ImageHelper::srcList($content);
        $url = $url ?: request()->domain();
		if($images)
		{
            foreach ($images as $v) {
                if(!strstr( $v,'http') && !strstr( $v,'https'))
                {
                    $image_url = $url.$v;
                    $content = str_replace($v, $image_url, $content);
                }
            }
		}
		return $content;
	}

    public static function replaceVideo(string $content,string $url=''): mixed
    {
        preg_match_all('/<iframe.*?\/iframe>/i', $content, $match2);
        $url = $url ?: request()->domain();
        $video = '';
        if(!empty($match2))
        {

            foreach ($match2[0] as $v)
            {
                preg_match('/[^>]*src=[\'"]?([^>\'"\s]*)[\'"]?[^>]*>/i', $v, $match);
                if(!empty($match))
                {
                    $video = $match[1];
                }
                if(!strstr( $video,'http') && !strstr( $video,'https'))
                {
                    str_replace($video,$url.$video,$content);
                }
            }
        }
        return str_replace('iframe','video',$content);
    }

    /**
     * 解析WEditor编辑器内容中单个视频地址
     * @param string $content
     * @param string $url
     * @return mixed|string
     */
    public static function parseVideoUrl(string $content,string $url=''): mixed
    {
        preg_match('/<iframe.*?\/iframe>/i', $content, $match2);
        $url = $url ?: request()->domain();
        $video = '';
        if(!empty($match2))
        {
            preg_match('/[^>]*src=[\'"]?([^>\'"\s]*)[\'"]?[^>]*>/i', $match2[0], $match);
            if(!empty($match))
            {
                $video = $match[1];
            }
        }

        if(!strstr( $video,'http') && !strstr( $video,'https'))
        {
            $video = $url.$video;
        }
        return $video;
    }

    /**
     * 获取内容中的视频
     * @param $content
     * @return array|mixed
     */
    public static function parseVideo($content)
    {
        preg_match_all('/<video[^>]*src=[\'"]?([^>\'"\s]*)[\'"]?[^>]*>/i',$content,$matches);
        $video = [];
        if(!empty($matches))
        {
            $video = $matches[1];
        }
        return !empty($video) ? $video : [];
    }

    public static function replaceVideoUrl($content,$url='')
    {
        preg_match_all('/<video[^>]*src=[\'"]?([^>\'"\s]*)[\'"]?[^>]*>/i',$content,$matches);
        if(empty($matches))
        {
            return $content;
        }

        $video = $matches[1];
        $url = $url ?: request()->domain();
        if($video)
        {
            foreach ($video as $v) {
                if(!strstr( $v,'http') && !strstr( $v,'https'))
                {
                    $image_url = $url.$v;
                    $content = str_replace($v, $image_url, $content);
                }
            }
        }
        return $content;
    }
    /**
     * 提取内容中的图片并本地化处理
     * @param $content
     * @param $type
     * @param $uid
     * @param bool $convert 是否解码html
     * @return array|mixed|string|string[]
     */
    public static function fetchContentImagesToLocal($content,$type,$uid,bool $convert=false)
    {
        if(get_setting('download_local_enable')=='N')
        {
            return $content;
        }

        //远程图片下载到本地逻辑处理钩子
        hook('fetch_content_image_local',['content'=>$content,'type'=>$type,'uid'=>$uid]);

        $content = htmlspecialchars_decode($content);

        $images = ImageHelper::srcList($content);
        if(!is_array($images) )return $content;

        foreach ($images as  $v)
        {
            if((strpos($v,'http://')!==false || strpos($v,'https://')!==false) && strpos($v,request()->domain())===false)
            {
                $fileName = explode('/',$v);
                $fileName = end($fileName);
                $imgByte = HttpHelper::get($v);
                if($imgByte['code'])
                {
                    $file = $imgByte['data'];
                    $result = UploadHelper::instance()->setAccessKey(md5($uid.time()))->setUploadPath($type)->upload($uid,$fileName,$file);
                    if($result['code']) {
                        $content = str_replace($v, $result['url'], $content);
                    }
                }
            }
        }
        return htmlspecialchars($content);
    }

    /**
     * 远程下载图片到本地
     * @param $url
     * @param string|null $save_dir 目录名称
     * @param int $uid
     * @return string
     */
    public static function downloadImageToLocal($url,string $save_dir='common',int $uid=0)
    {
        if($url !="" )
        {
            $url=str_replace(['&amp;'],['&'],$url); //url中特定字符替换
            $ext = strrchr($url, '.');
            $mimes = array('.gif', '.jpg', '.png','.ico');
            if (!in_array($ext, $mimes)) {
                $ext = '.jpg';
            }

            $save_path = config('filesystem.disks.public.root').DS.$save_dir .DS.date('Ymd',time()).DS;

            if (!file_exists($save_path)) {
                FileHelper::mkDirs($save_path);
            }

            $filename_r = md5(date('YmdHis',time())).$ext;	//给图片命名
            $filename = $save_path.$filename_r;
            $file_url = config('filesystem.disks.public.url').'/'.$save_dir.'/'.date('Ymd',time()).'/'.$filename_r;
            $imgByte = HttpHelper::get($url);
            if($imgByte['data'])
            {
                FileHelper::createFile($filename,$imgByte['data']);
            }

            return is_file($filename) ? $file_url : '';
        }else{
            return false;
        }
    }
}
