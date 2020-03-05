<?php
    namespace Parser;

    use PHPHtmlParser\Dom;

    class ParseCoupons implements ParserComponentImpl {
        private function find(string $rawHTML, string $templateStart, string $templateFinish) {
            $startPos = 0;
            $output = array();
            while (($startPos = strpos($rawHTML, $templateStart, $startPos))!== false) {
                $endPos = strpos($rawHTML, $templateFinish, $startPos);
                $value = substr($rawHTML, $startPos+strlen($templateStart), $endPos-$startPos-strlen($templateStart));
                $output[] = $value;
                $startPos = $startPos + strlen($templateStart);
            }
            return $output;
        }


        function parse($rawHTML, $isServerMode) {
            try {
                $result = [];
                $dom = new Dom;
                $dom->load($rawHTML);
                $contentTitle = $dom -> getElementsByClass('couponTitle');
                $contentId = $this->find($rawHTML, ' id="coupon-', '"><div');
                $contentDate = $this->find($rawHTML, 'itemProp="validThrough"> <!-- -->', "</span>");
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
                        /*+4 because coupons site is perfectly balanced, as all should be, because
                            it's uploading 4 equal images - for every type of devises (iPad, mobile, desktop ...)
                            good decision:/
                        */
                    }

                    $result[] = array_merge($title,$img,$desc,$times, $id, $endDate); /*make array to json*/
                }
                return $result;
            }
            catch (Exception $e) {
                return 0;
            }
        }
    }
?>