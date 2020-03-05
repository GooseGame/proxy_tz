<?php
    namespace Db;

    class DB {
        private $db;

        function __construct() {
            $this->db = new DBConnector();
        }

        function deleteOverdueCoupons() {
            $dt = new \DateTime();
            $now = $dt->format('Y-m-d');
            $query = $this->db->query('DELETE FROM coupons WHERE date < ' . $now);
            echo "successfully deleted overdue coupons";
        }

        function getDoubleSelectValue(string $tableName, string $needColumn ,string $column1, string $column2, $val1, $val2, string $operator1, string $operator2) {
            /*i'd like to delete this method, but coupon_id in coupons.com is always different when you refresh the page,
            so i need to check coupon equality by checking it's title and date (as example) */
            $query = $this->db->query('SELECT ' . $needColumn . ' FROM ' . $tableName .
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
            /*i can make it by statement, but values in this (and previous) method can't contain quotes,
             so it's much easier for me to keep it and don't create a heavy bicycle*/
            $query = $this->db->query('SELECT ' . $needColumn . ' FROM ' . $tableName . ' WHERE ' . $column . ' ' . $operator  . ' "' . $val . '"');

            if (!$query) {
                return false;
            }

            $rows = array();
            while ($row = $this->db->fetch($query)) {
                $rows[] = $row;
            }

            return $rows;
        }

        function insertAll() {
            $spider = new \Spider\SpiderDBDecorator(new \Parser\ParseSites());
            $spider->start("stores/", true);
            $data = $spider->getData();
            foreach ($data as $item) {
                if (!$this->getValue("shops", "name", "name", $item[0], '=')) {
                    echo "insert new shop (" . $item[0] . ")";
                    if ($this->db->prepare('INSERT INTO shops (name, site) VALUES (?, ?)')) {
                        $stmt = $this->db->getStatement();
                        $stmt->bind_param('ss', $item[0], $item[1]);
                        $this->db->execute($stmt);
                    }
                    $this->insertCoupons($item[1]);
                }
                else {
                    echo "starting to insert into existing shop (" . $item[0] . ")";
                    $shop_id = $this->getValue("shops", "shop_id", "name", $item[0], '=');
                    $this->insertCoupons($item[1]);
                }
            }
        }

        function insertCoupons(string $site) {
            try {
                $spider = new \Spider\SpiderDBDecorator(new \Parser\ParseCoupons);
                $spider->start($site, true);
                $data = $spider->getData();
                $shop_id = $this->getValue('shops', 'shop_id', 'site', $site, '=');
                foreach ($data as $item) {
                    if (!$this->getDoubleSelectValue("coupons", "coupon_id", "date", "title", $item['date'], $item['title'], '=', '=')) {
                        print_r($item);
                        if ($this->db->prepare('INSERT INTO coupons VALUES (?, ?, ?, ?, ?, ?, ?)')) {
                            $stmt = $this->db->getStatement();
                            $stmt->bind_param('issssis', $item["id"], $item["title"], $item["desc"], $item["img_src"], $item["times"], $shop_id[0]['shop_id'], $item["date"]);
                            $this->db->execute($stmt);
                        }
                    }
                }
            }
            catch (Exception $e) {
                exit;
            }
        }
    }
?>