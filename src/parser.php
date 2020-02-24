<?php
    use PHPHtmlParser\Dom;
    require_once __DIR__ . '/../vendor/autoload.php';


    class ParseCoupons implements ParserComponentImpl {
        private function find($data, $templateStart, $templateFinish) {
            $startPos = 0;
            $output = array();
            while (($startPos = strpos($data, $templateStart, $startPos))!== false) {
                $endPos = strpos($data, $templateFinish, $startPos);
                $value = substr($data, $startPos+strlen($templateStart), $endPos-$startPos-strlen($templateStart));
                $output[] = $value;
                $startPos = $startPos + strlen($templateStart);
            }
            return $output;
        }


        function parse($data, $isServerMode) {
            try {
                $result = [];
                $dom = new Dom;
                $dom -> load($data);
                $contentTitle = $dom -> getElementsByClass('couponTitle');
                $contentId = $this->find($data, ' id="coupon-', '"><div');
                $contentDate = $this->find($data, 'itemProp="validThrough"> <!-- -->', "</span>");
                $contentImg = $dom -> getElementsByClass("coupons-product-image-box");
                $contentImgPlaceholder = $dom -> getElementsByClass("counpon-sale-box-sale-single-text");
                $contentDesc = $dom -> getElementsByClass("coupon-readmore-fix-overflow");
                $contentTimes = $dom -> getElementsByClass("used-times");
                $counter = 0;


                for ($i=0; $i<$contentTitle->count(); $i+=2) {
                    $title = ['title' => substr(explode('>', (string) $contentTitle[$i])[1], 0, -5)]; /*slice </tag>*/
                    $img = ['img_src' => substr(explode('>', (string) $contentImgPlaceholder[$i])[1], 0, -5)]; /*slice </tag>*/
                    $desc = ['desc' => substr(explode('>', (string) $contentDesc[$i])[2], 0, -5)];  /*slice </tag>*/
                    $times = ['times' => substr(explode('>', (string) $contentTimes[$i])[2], 0, -6)]; /*slice </tag>*/
                    $id = ['id' => $contentId[$i/2]];
                    $endDate = ['date' => $contentDate[$i]];


                    if (!is_string($desc['desc'])) {
                        $desc['desc'] = "";
                    }

                    if (!is_string($img['img_src'])) {
                        $row = explode('>', (string) $contentImg[$counter])[1];
                        $img = ['img_src' => substr($row, 10, stripos($row, "png?")-7)];    /*slice </tag> and extra info in link*/
                        $counter+=4;
                        /*+4 because coupons site is perfectly balanced, as all should because
                            it's uploading 4 equal images - for every type of devises (iPad, mobile, desktop ...)
                            good decision:/
                        */
                    }

                    $result[] = array_merge($title,$img,$desc,$times, $id, $endDate); /*make array to json*/
                }
                return $result;
            }
            catch (Exception $e) {
                echo $e->getMessage();
                return 0;
                exit;
            }
        }
    }

    class ParseSites implements ParserComponentImpl {
        function parse($data, $isServerMode) {
            try {
                $base_url = "https://www.coupons.com/coupon-codes/";
                $result = [];
                $dom = new Dom;
                $dom -> load($data);
                $contentItemBlock = $dom -> getElementsByClass('item');

                for ($i=0; $i<$contentItemBlock->count(); $i++) {
                    $itemData = explode('>', (string) $contentItemBlock[$i]);
                    $siteURL = substr($itemData[1], 9+strlen($base_url), -1);
                    $siteName = substr($itemData[2], 0, -3);
                    $result[] = array($siteName, $siteURL);
                }

                return $result;
            }
            catch(Exception $e) {
                echo $e->getMessage();
                return 0;
            }
        }
    }
?>