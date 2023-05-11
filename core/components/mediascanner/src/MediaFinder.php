<?php
namespace MediaScanner;

use voku\helper\HtmlDomParser;

class MediaFinder {
    /** @var \modX */
    private $modx;
    private $baseUrl;
    private $basePath;

    public function __construct(&$modx)
    {
        $this->modx =& $modx;

        $this->baseUrl = $this->modx->getOption('base_url');
        $this->basePath = $this->modx->getOption('base_path');
    }

    public function findMedia($content, callable $callback)
    {
        $dom = HtmlDomParser::str_get_html($content);
        $tags = $dom->findMulti('img');
        foreach ($tags as $tag) {
            $url = $tag->getAttribute('src');

            if (!$this->checkUrl($url)) continue;

            $callback($url);
        }

        $tags = $dom->findMulti('a');
        foreach ($tags as $tag) {
            $url = $tag->getAttribute('href');

            if (!$this->checkUrl($url)) continue;

            $callback($url);
        }
    }

    private function checkUrl($url) {
        if (empty($url)) return false;

        if (preg_match('/^(https?:)?\/\//', $url)) {
            return false;
        }

        // ignore tags
        $matches = [];
        $tagsFound = $this->modx->getParser()->collectElementTags($url, $matches);
        if ($tagsFound > 0) {
            return false;
        }

        if (!empty($this->baseUrl) && substr_compare($url, $this->baseUrl, 0, strlen($this->baseUrl)) === 0) {
            $url = substr($url, strlen($this->baseUrl));
        }

        $path = $this->basePath . $url;

        if (!file_exists($path)) {
            return false;
        }

        if (!is_file($path)) {
            return false;
        }

        return true;
    }
}