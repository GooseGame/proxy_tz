<?php
    namespace tz;

    use PHPHtmlParser\Dom;

    class ParseSites implements ParserComponentImpl {
        private $config;

        function __construct() {
            $this->config = parse_ini_file('app.ini');
        }

        function parse(string $rawHTML, bool $isServerMode) {
            try {
                $base_url = $this->config["baseUrl"];
                $result = [];
                $dom = new Dom;
                $dom->load($rawHTML);
                $contentItemBlock = $dom->getElementsByClass('item');

                for ($i=0; $i<$contentItemBlock->count(); $i++) {
                    $itemData = explode('>', (string) $contentItemBlock[$i]);
                    $siteURL = substr($itemData[1], 9+strlen($base_url), -1);
                    $siteName = substr($itemData[2], 0, -3);
                    $result[] = array($siteName, $siteURL);
                }

                return $result;
            }
            catch(Exception $e) {
                return 0;
            }
        }
    }
?>