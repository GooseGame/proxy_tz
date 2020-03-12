<?php

namespace Parser;

use PHPHtmlParser\Dom;

class ParseSites implements ParserComponentImpl
{
    private $baseUrl;

    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function parse(string $rawHTML): array
    {
        try {
            $base_url = $this->baseUrl;
            $result = [];
            $dom = new Dom;
            $dom->load($rawHTML);
            $contentItemBlock = $dom->getElementsByClass('item');

            foreach ($contentItemBlock as $item) {
                $siteUrl = $item->find('a')->href;
                $siteUrl = substr($siteUrl, strlen($base_url));
                $siteName = $item->find('a')->text;
                $result[] = array($siteName, $siteUrl);
            }
            echo "** Successfully parsed shops" . PHP_EOL;
            return $result;
        }
        catch(\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }
}