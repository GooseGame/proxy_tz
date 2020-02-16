<?php
    use PHPHtmlParser\Dom;

    function parse($raw) {
        try {
                $result = [];
                $dom = new Dom;
                $dom -> load($raw);
                $contentTitle = $dom -> getElementsByClass('couponTitle');
                $contentImg = $dom -> getElementsByClass("coupons-product-image-box");
                $contentImgPlaceholder = $dom -> getElementsByClass("counpon-sale-box-sale-single-text");
                $contentDesc = $dom -> getElementsByClass("coupon-readmore-fix-overflow");
                $contentTimes = $dom -> getElementsByClass("used-times");

                $counter = 0;

                for ($i=0; $i<$contentTitle->count(); $i+=2) {

                    $title = ['title' => substr(explode('>', (string) $contentTitle[$i])[1], 0, -5)]; /*slice </tag>*/

                    $img = ['img' => substr(explode('>', (string) $contentImgPlaceholder[$i])[1], 0, -5)]; /*slice </tag>*/

                    if (!is_string($img['img'])) {
                        $row = explode('>', (string) $contentImg[$counter])[1];
                        $img = ['img' => substr($row, 10, stripos($row, "png?")-7)];    /*slice </tag> and extra info in link*/
                        $counter+=4;
                        /*+4 because coupons site is perfectly balanced, as all should because
                            it's uploading 4 equal images - for every type of devises (iPad, mobile, desktop ...)
                            good decision:/
                        */
                    }

                    $desc = ['desc' => substr(explode('>', (string) $contentDesc[$i])[2], 0, -5)];  /*slice </tag>*/

                    if (!is_string($desc['desc'])) {
                        $desc['desc'] = "";
                    }

                    $times = ['times' => substr(explode('>', (string) $contentTimes[$i])[2], 0, -6)]; /*slice </tag>*/

                    $result[] = array_merge($title,$img,$desc,$times); /*make array to json*/
                }

            }
            catch (Exception $e) {
                exit;
            }
            header('Content-Type: application/json');
            return json_encode($result);
            /*json response:
                [{'title': "...", 'img': "...", "desc": "...", "times": "..."}, ...]
            */
        }

?>