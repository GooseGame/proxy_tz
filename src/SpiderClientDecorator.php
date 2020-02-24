<?php
    require_once 'DB.php';
    require_once 'Spider.php';
    class SpiderClientDecorator extends SpiderDecorator {
        function checkDataInDBBeforeStart($site) {
            $db = new DB();
            $shopIdFalseIfNotExist = $db->getValue("shops", "shop_id", "site", $site, '=');
            if (!$shopIdFalseIfNotExist) {
                $this->start($site, false);
            }
            else {
                $coupons = $db->getValue("coupons", "*", "shop_id", $shopIdFalseIfNotExist[0]['shop_id'], '=');
                $this->parsedData = $coupons;
            }
        }
        function getData() {
            $data = $this->parsedData;
            if ($data != 0) {
                header('Content-Type: application/json');
                echo json_encode($data);
                /*json response:
                    [{'title': "...", 'img': "...", "desc": "...", "times": "..."}, ...]
                */
            }
        }
    }
?>
