<?php
    namespace tz\db;

    class ClientResponse {
        private $parsedData = false;
        function checkAndGetDataFromDB(string $site) {
            $db = new DB();
            $shopIdFalseIfNotExist = $db->getValue("shops", "shop_id", "site", $site, '=');
            if (!$shopIdFalseIfNotExist) {
                $this->parsedData = array('message' => "The server database does not contain information about this store.");
            }
            else {
                $coupons = $db->getValue("coupons", "*", "shop_id", $shopIdFalseIfNotExist[0]['shop_id'], '=');
                $this->parsedData = $coupons;
            }
        }

        function echoJSONData() {
            $data = $this->parsedData;
            header('Content-Type: application/json');
            if (!$data) {
                $data = array('message' => "There are some problem here. Try to refresh site.");
            }
            /*json response (normal):
                [{'title': "...", 'img': "...", "desc": "...", "times": "..."}, ...]
            */
            /*json response (error):
                [{'message': "some error"}]
            */
            echo json_encode($data);
        }
    }
?>
