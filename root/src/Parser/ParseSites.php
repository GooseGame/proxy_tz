<?php

namespace Parser;

use Katzgrau\KLogger\Logger;
use PHPHtmlParser\Dom;

class ParseSites implements ParserComponentImpl
{
    private $baseUrl;
    private $logger;

    public function __construct($baseUrl, Logger $logger)
    {
        $this->baseUrl = $baseUrl;
        $this->logger = $logger;
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
            $this->logger->info("Successfully parsed shops");
            $this->logger->debug('Shops: ', $result);
            return $result;
        }
        catch(\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }
}