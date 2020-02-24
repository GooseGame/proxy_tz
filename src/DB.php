<?php
    require_once 'MyDB.php';
    require_once 'SpiderDBDecorator.php';
    require_once 'parser.php';

    class DB {
        private $db;

        function __construct() {
            $this->db = new MyDB();
        }

        function deleteOverdueCoupons() {
            $dt = new DateTime();
            $now = $dt->format('Y-m-d');
            $checkedValue = $this->getValue('coupons','coupon_id', 'date', $now, '=');
            foreach ($checkedValue as $v) {
                 $this->deleteCoupon($v['coupon_id']);
            }
        }

        function getMultiplySelectValue($table, $needType ,$param1, $param2, $val1, $val2, $operator1, $operator2) {
             $query = $this->db->query('SELECT ' . $needType . ' FROM shopsandcoupons.' . $table . ' WHERE ' . $param1 . ' ' . $operator1  . ' "' . $val1 . '"  AND ' . $param2 . ' ' . $operator2  . ' "' . $val2 . '"');
             if (!$query) {
                 return false;
             }
             $rows = array();
             while ($row = $query->fetch_assoc()) {
                 $rows[] = $row;
             }

             return $rows;
        }

        function getValue($table, $needType ,$param, $val, $operator) {
            $query = $this->db->query('SELECT ' . $needType . ' FROM shopsandcoupons.' . $table . ' WHERE ' . $param . ' ' . $operator  . ' "' . $val . '"');
            if (!$query) {
                return false;
            }
            $rows = array();
            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }

            return $rows;
        }

        function deleteCoupon($id) {
            $this->db->query('DELETE FROM shopsandcoupons.coupons WHERE coupon_id = ' . $id);
        }

        function insertAll() {
            $spider = new SpiderDBDecorator(new ParseSites);
            $spider->start("stores/", true);
            $data = $spider->getData();
            $i = 1;
            foreach ($data as $item) {
                if (!$this->getValue("shops", "name", "name", $item[0], '=')) {
                    $this->db->query('INSERT INTO shopsandcoupons.shops VALUES ("' . strval($i) . '" , "' . $item[0] . '", "' . $item[1] . '")');
                    $this->insertCoupons($item, $i);
                }
                else {
                    $shop_id = $this->getValue("shops", "shop_id", "name", $item[0], '=');
                    $this->insertCoupons($item, $shop_id[0]['shop_id']);
                }
                $i++;
            }
        }

        function insertCoupons($item, $i) {
            try {
                $spider = new SpiderDBDecorator(new ParseCoupons);
                $spider->start($item[1], true);
                $result = $spider->getData();
                foreach ($result as $resultItem) {
                    if (!$this->getMultiplySelectValue("coupons", "coupon_id", "date", "title", $resultItem['date'], $resultItem['title'], '=', '=')) {
                        echo "inserting coupon number id=" . $resultItem['id'] . ",  img_src is " . $resultItem['img'] . "       ";

                        $this->db->query('INSERT INTO shopsandcoupons.coupons
                                    VALUES(' . strval($resultItem["id"]) . ', "' . $resultItem["title"] .
                                            '", "' . $resultItem["desc"] . '", "' . $resultItem["img"] .
                                            '", "' .$resultItem["times"] . '", ' . strval($i) .
                                            ', "' . $resultItem["date"] . '")');
                    }
                }
            }
            catch (Exception $e) {
                exit;
            }
        }
    }
?>