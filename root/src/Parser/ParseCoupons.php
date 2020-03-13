<?php

namespace Parser;

use Katzgrau\KLogger\Logger;
use PHPHtmlParser\Dom;
use stringEncode\Exception;

class ParseCoupons implements ParserComponentImpl
{
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function parse(string $rawHTML)
    {
        $result = [];
        $dom = new Dom;
        $dom->load($rawHTML);
        $couponBlock = $dom->find('.coupons-detail-box.hide-on-mobile');
        foreach ($couponBlock as $coupon) {
            #get data from Dom tree,
            #associate elements and merge
            $result[] = [
                'title'=>$this->getContentTitle($coupon),
                'img_src'=>$this->getContentImage($coupon),
                'desc'=>$this->getContentDesc($coupon),
                'times'=>$this->getContentTimes($coupon),
                'date'=>$this->getContentDate($coupon)
            ];
        }
        $this->logger->info("Successfully parsed coupons");
        $this->logger->debug("Coupons: ", $result);
        return $result;
    }

    private function getContentImage($coupon): string
    {
        $contentImage = $coupon->find('.counpon-sale-box-sale-text.counpon-sale-box-sale-single-text')->text;
        #if we have real image
        if (is_null($contentImage)) {
            $contentImage = $coupon->find('.coupons-product-image-box img')->src;
        }
        if (is_null($contentImage)) {
            throw new Exception("Cannot parse image");
        }
        return $contentImage;
    }

    private function getContentTitle($coupon): string
    {
        $contentTitle = $coupon->find('.couponTitle')->text;
        if (is_null($contentTitle)) {
            throw new Exception("Cannot parse title");
        }
        return $contentTitle;
    }

    private function getContentDesc($coupon): string
    {
        $contentDesc = $coupon->find('.coupon-description.coupon-readmore-fix-overflow div')->text;
        if (is_null($contentDesc)) {
            throw new Exception("Cannot parse description");
        }

        return $contentDesc;
    }

    private function getContentDate($coupon): string
    {
        $contentDate = $coupon->find('.expire-row.hide-on-mobile div span')->text;
        if (is_null($contentDate)) {
            throw new Exception("Cannot parse date");
        }
        #parsing leaves ' ' at [0]
        $contentDate = ltrim($contentDate, ' ');
        return $contentDate;
    }

    private function getContentTimes($coupon): string
    {
        $contentTimes = $coupon->find('.used-times span')->text;
        if (is_null($contentTimes)) {
            throw new Exception("Cannot parse 'times' row");
        }
        #parsing leaves ' ' at [0]
        $contentTimes = ltrim($contentTimes, ' ');
        return $contentTimes;
    }
}
