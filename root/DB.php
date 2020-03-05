<?php
    namespace tz;

    class DB {
        private $db;

        function __construct() {
            $this->db = new DBConnector();
        }

        function deleteOverdueCoupons() {
            $dt = new \DateTime();
            $now = $dt->format('Y-m-d');
            $checkedValue = $this->getValue('coupons','coupon_id', 'date', $now, '<');

            foreach ($checkedValue as $v) {
                $this->deleteCoupon($v['coupon_id']);
            }
        }

        function getDoubleSelectValue(string $tableName, string $needColumn ,string $column1, string $column2, $val1, $val2, string $operator1, string $operator2) {
            $query = $this->db->query('SELECT ' . $needColumn . ' FROM shopsandcoupons.' . $tableName .
                ' WHERE ' . $column1 . ' ' . $operator1  . ' "' . $val1 .
                '"  AND ' . $column2 . ' ' . $operator2  . ' "' . $val2 . '"');

            if (!$query) {
                return false;
            }
            $rows = array();
            while ($row = $this->db->fetch($query)) {
                $rows[] = $row;
            }

            return $rows;
        }

        function getValue(string $tableName, string $needColumn ,string $column, $val, string $operator) {
            $query = $this->db->query('SELECT ' . $needColumn . ' FROM shopsandcoupons.' . $tableName . ' WHERE ' . $column . ' ' . $operator  . ' "' . $val . '"');

            if (!$query) {
                return false;
            }

            $rows = array();
            while ($row = $this->db->fetch($query)) {
                $rows[] = $row;
            }

            return $rows;
        }

        function deleteCoupon(string $id) {
            echo "delete coupon id=" . $id;
            $this->db->query('DELETE FROM shopsandcoupons.coupons WHERE coupon_id = ' . $id);
        }

        function insertAll() {
            $spider = new SpiderDBDecorator(new ParseSites());
            $spider->start("stores/", true);
            $data = $spider->getData();
            $i = 1;
            foreach ($data as $item) {
                if (!$this->getValue("shops", "name", "name", $item[0], '=')) {
                    echo "insert new shop (" . $item[0] . ")";
                    $item = $this->sliceExtraQuotes($item);
                    $this->db->query('INSERT INTO shopsandcoupons.shops VALUES ("' . strval($i) . '" , "' . $item[0] . '", "' . $item[1] . '")');
                    $this->insertCoupons($item, $i);
                }
                else {
                    echo "starting to insert into existing shop (" . $item[0] . ")";
                    $shop_id = $this->getValue("shops", "shop_id", "name", $item[0], '=');
                    $this->insertCoupons($item[1], $shop_id[0]['shop_id']);
                }
                $i++;
            }
        }

        function insertCoupons(string $site, $i) {
            try {
                $spider = new SpiderDBDecorator(new ParseCoupons);
                $spider->start($site, true);
                $data = $spider->getData();
                foreach ($data as $item) {
                    if (!$this->getDoubleSelectValue("coupons", "coupon_id", "date", "title", $item['date'], $item['title'], '=', '=')) {
                        $item = $this->sliceExtraQuotes($item);
                        print_r($item);
                        $this->db->query('INSERT INTO shopsandcoupons.coupons VALUES(' .
                            strval($item["id"]) . ', "' . $item["title"] .
                            '", "' . $item["desc"] . '", "' . $item["img_src"] .
                            '", "' .$item["times"] . '", ' . strval($i) .
                            ', "' . $item["date"] . '")');
                    }
                }
            }
            catch (Exception $e) {
                exit;
            }
        }
        function sliceExtraQuotes(array $args) {
            foreach ($args as &$item) {
                if ((strpos($item, '"') !== false) || (strpos($item, "'") !== false)) {
                    $item = str_replace(array( '"', "'" ), '', $item);
                }
            }
            return $args;
        }
    }
?>