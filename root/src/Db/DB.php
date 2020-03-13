<?php

namespace Db;

use stringEncode\Exception;

class DB
{
    private $db;
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->db = new DBConnector($config['host'], $config['username'], $config['password'], $config['dbName']);
    }

    public function deleteOverdueCoupons()
    {
        $dt = new \DateTime();
        $now = $dt->format('Y-m-d');
        $query = $this->db->query('DELETE FROM coupons WHERE date < ' . $now);
    }

    public function isShopExist(string $name): bool
    {
        $verifiedValuesArray = $this->db->escapeString(array($name));
        $verifiedName = $verifiedValuesArray[0];
        $query = $this->db->query('SELECT name FROM shops WHERE name = "' . $verifiedName . '"');
        $result = $this->db->fetch($query);
        if (is_null($result)) {
            #exist
            return false;
        } else {
            return true;
        }
    }

    public function getCouponsByShopId(string $shop_id): array
    {
        $verifiedValuesArray = $this->db->escapeString(array($shop_id));
        $verifiedId = $verifiedValuesArray[0];
        $query = $this->db->query('SELECT * FROM coupons WHERE shop_id = "' . $verifiedId . '"');
        $rows = array();
        while ($row = $this->db->fetch($query)) {
            $rows[] = $row;
        }
        if (empty($rows)) {
            throw new Exception('List of coupons is empty ' . $shop_id);
        }
        return $rows;
    }

    public function getListOfShopUrls(): array
    {
        $query = $this->db->query('SELECT site from shops');
        $rows = array();
        while ($row = $this->db->fetch($query)) {
            $rows[] = $row['site'];
        }
        if (empty($rows)) {
            throw new Exception("Unable to get list of shops.");
        }
        return $rows;
    }

    public function getShopIdBySite(string $site): string
    {
        $verifiedValuesArray = $this->db->escapeString(array($site));
        $verifiedSite = $verifiedValuesArray[0];
        $query = $this->db->query('SELECT shop_id FROM shops WHERE site = "' . $verifiedSite. '"');

        $result = $this->db->fetch($query);
        if (is_null($result)) {
            throw new Exception("Shop site is unknown.");
        } else {
            return $result['shop_id'];
        }
    }

    public function insertShops(array $data)
    {
        foreach ($data as $item) {
            if (!$this->isShopExist($item[0])) {
                $stmt = $this->db->getInitializedStatement();
                if ($this->db->prepare('INSERT INTO shops (name, site) VALUES (?, ?)', $stmt)) {
                    $stmt->bind_param('ss', $item[0], $item[1]);
                    $this->db->execute($stmt);
                }
            }
        }
    }

    public function insertCoupons(array $parsedCoupons, string $site)
    {
        $shop_id = $this->getShopIdBySite($site);
        foreach ($parsedCoupons as $item) {
            $verifiedValuesArray = $this->db->escapeString(array($item['date'], $item['title']));
            $date = $verifiedValuesArray[0];
            $title = $verifiedValuesArray[1];
            $query = $this->db->query('SELECT coupon_id FROM coupons WHERE date = ' . $date . ' AND title = "' . $title . '"');
            $result = $this->db->fetch($query);

            if (is_null($result)) {
                $stmt = $this->db->getInitializedStatement();
                if ($this->db->prepare("INSERT INTO coupons (title, `desc`, img_src, times, shop_id, date) VALUES (?, ?, ?, ?, ?, ?)", $stmt)) {
                    $stmt->bind_param('ssssis', $item["title"], $item["desc"], $item["img_src"], $item["times"], $shop_id, $item["date"]);
                    $this->db->execute($stmt);
                }
            }
        }
    }
}