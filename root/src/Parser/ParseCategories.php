<?php

namespace Parser;

use Katzgrau\KLogger\Logger;
use PHPHtmlParser\Dom;

class ParseCategories implements ParserComponentImpl
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
            $contentItemBlock = $dom->find('.bd.all-categories a');

            foreach ($contentItemBlock as $item) {
                $categoryUrl = $item->href;
                $categoryUrl = substr($categoryUrl, strlen($base_url));
                $categoryName = $item->find('span')->text;
                $result[] = array($categoryName, $categoryUrl);
            }
            $this->logger->info("Successfully parsed categories");
            $this->logger->debug('Categories: ', $result);
            return $result;
        }
        catch(\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }
}