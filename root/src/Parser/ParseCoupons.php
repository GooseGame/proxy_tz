<?php

namespace Parser;

use PHPHtmlParser\Dom;

class ParseCoupons implements ParserComponentImpl
{
    public function parse(string $rawHTML)
    {
        $result = [];
        $dom = new Dom;
        $dom->load($rawHTML);
        $couponBlock = $dom->getElementsByClass('coupons-detail-box');
        foreach ($couponBlock as $coupon) {
            #get data from Dom tree
            $contentImage = $this->getContentImage($coupon);
            $contentTitle = $this->getContentTitle($coupon);
            $contentDesc = $this->getContentDesc($coupon);
            $contentDate = $this->getContentDate($coupon);
            $contentTimes = $this->getContentTimes($coupon);
            #associate elements and merge
            $title = array('title'=>$contentTitle);
            $img = array('img_src'=>$contentImage);
            $desc = array('desc'=>$contentDesc);
            $times = array('times'=>$contentTimes);
            $endDate = array('date'=>$contentDate);
            $result[] = array_merge($title,$img,$desc,$times, $endDate);
        }
        echo "** Successfully parsed coupons" . PHP_EOL;
        $result = $this->getUniqueCoupons($result);
        return $result;
    }

    private function getUniqueCoupons($coupons): array
    {
        $result = array();
        for ($i=0; $i<count($coupons); $i+=2) {
            $result[] = $coupons[$i];
        }
        return $result;
    }

    private function getContentImage($coupon): string
    {
        $contentImage = $coupon
            ->firstChild()
            ->firstChild()
            ->firstChild()->text;
        #if we have real image
        if (is_null($contentImage)) {
            $contentImage = $coupon
                ->firstChild()
                ->firstChild()
                ->firstChild()->src;
        }
        return $contentImage;
    }

    private function getContentTitle($coupon): string
    {
        $contentTitle = $coupon
            ->firstChild()->nextSibling()->nextSibling()
            ->firstChild();
        $contentTitle = $this->goToSiblingIfHiddenElement($contentTitle);
        $contentTitle = $contentTitle
            ->firstChild()
            ->firstChild()->text;

        return $contentTitle;
    }

    private function getContentDesc($coupon): string
    {
        $contentDesc = $coupon
            ->firstChild()->nextSibling()->nextSibling()
            ->firstChild();
        $contentDesc = $this->goToSiblingIfHiddenElement($contentDesc);
        $contentDesc = $contentDesc->nextSibling()->nextSibling()->nextSibling()
            ->firstChild()
            ->firstChild()
            ->firstChild()->text;

        return $contentDesc;
    }

    private function getContentDate($coupon): string
    {
        $contentDate = $coupon
            ->firstChild()->nextSibling()->nextSibling()
            ->firstChild();
        $contentDate = $this->goToSiblingIfHiddenElement($contentDate);
        $contentDate = $contentDate->nextSibling()->nextSibling()->nextSibling()->nextSibling()
            ->firstChild()->nextSibling()
            ->firstChild()->nextSibling()
            ->firstChild();
        #parsing leaves ' ' at [0]
        $contentDate = ltrim($contentDate, ' ');
        return $contentDate;
    }

    private function getContentTimes($coupon): string
    {
        $contentTimes = $coupon
            ->firstChild()->nextSibling()->nextSibling()
            ->firstChild();
        $contentTimes = $this->goToSiblingIfHiddenElement($contentTimes);
        $contentTimes = $contentTimes->nextSibling()->nextSibling()->nextSibling()->nextSibling()
            ->firstChild()->nextSibling()->nextSibling()
            ->firstChild()->nextSibling()
            ->firstChild();
        #parsing leaves ' ' at [0]
        $contentTimes = ltrim($contentTimes, ' ');
        return $contentTimes;
    }

    private function goToSiblingIfHiddenElement($element)
    {
        if (!$this->contains($element->nextSibling()->class, 'coupons-btn')) {
            $element = $element->nextSibling();
        }
        return $element;
    }

    private function contains(string $str, string $pattern): bool
    {
        if (strpos($str, $pattern) !== false) {
            return true;
        } else {
            return false;
        }
    }
}
