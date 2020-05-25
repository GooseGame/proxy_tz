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

    public function isIpExist(string $ip): bool
    {
        $verifiedValuesArray = $this->db->escapeString(array($ip));
        $verifiedName = $verifiedValuesArray[0];
        $query = $this->db->query('SELECT ip FROM ips WHERE ip = "' . $verifiedName . '"');
        $result = $this->db->fetch($query);
        if (is_null($result)) {
            #exist
            return false;
        } else {
            return true;
        }
    }

    public function isCategoryExist(string $name): bool
    {
        $verifiedValuesArray = $this->db->escapeString(array($name));
        $verifiedName = $verifiedValuesArray[0];
        $query = $this->db->query('SELECT name FROM categories WHERE name = "' . $verifiedName . '"');
        $result = $this->db->fetch($query);
        if (is_null($result)) {
            #exist
            return false;
        } else {
            return true;
        }
    }

    public function getIPInfo($ip) {
        $query = $this->db->query('SELECT * FROM ips WHERE ip = "' . $ip . '"');

        $rows = $this->db->fetch($query);
        $result = array();
            
        if (empty($rows)) {
            throw new Exception('unknown ip error');
        }
        
        if (!is_null($rows['shop_id'])) {
            $shopInfo = $this->getShopById($rows['shop_id']);
            $result += array('shop_id' => $shopInfo['shop_id'],
                                'site' => $shopInfo['site'],
                                'shop_name' => $shopInfo['name']
                            );
        }
        if (!is_null($rows['category_id'])) {
            $categoryInfo = $this->getCategoryById($rows['category_id']);
            $result += array('category_id' => $categoryInfo['category_id'],
                                'category_name' => $categoryInfo['name']
                            );
        }
        return $result;
    }

    public function getThemes() {
        $query = $this->db->query('SELECT * FROM themes');
        $rows = array();
        while ($row = $this->db->fetch($query)) {
            $rows[] = $row;
        }
        if (empty($rows)) {
            throw new Exception('List of themes is empty ' . $shop_id);
        }
        return $rows;
    }

    public function getCategoryById($id) {
        $query = $this->db->query('SELECT * FROM categories WHERE category_id = "' . $id . '"');
        $rows = $this->db->fetch($query);
        if (empty($rows)) {
            throw new Exception('List of coupons is empty ' . $shop_id);
        }
        return $rows;
    } 

    public function getShopById($id) {
        $query = $this->db->query('SELECT * FROM shops WHERE shop_id = "' . $id . '"');
        $rows = $this->db->fetch($query);
        if (empty($rows)) {
            throw new Exception('List of coupons is empty ' . $shop_id);
        }
        return $rows;
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

    public function insertIP($ip, $id, $idOf) {
        if ($idOf == 'categories') {
            if (!$this->isIpExist($ip)) {
                $stmt = $this->db->getInitializedStatement();
                if ($this->db->prepare('INSERT INTO ips (ip, category_id) VALUES (?, ?)', $stmt)) {
                    $stmt->bind_param('si', $ip, $id);
                    $this->db->execute($stmt);
                    return true;
                }
            }
            else {
                if ($query = $this->db->query('UPDATE ips SET category_id = ' . $id . ' WHERE ip = "' . $ip . '"')) {
                    return true;
                }

        }
        }
        else {
            if (!$this->isIpExist($ip)) {
                $stmt = $this->db->getInitializedStatement();
                if ($this->db->prepare('INSERT INTO ips (ip, shop_id) VALUES (?, ?)', $stmt)) {
                    $stmt->bind_param('si', $ip, $id);
                    $this->db->execute($stmt);
                    return true;
                }
            }
            else {
                    if ($query = $this->db->query('UPDATE ips SET shop_id = ' . $id . ' WHERE ip = "' . $ip . '"')) {
                        return true;
                    }

            }
        }
    }
    

    public function getCouponsByCategoryId(string $category_id): array
    {
        $verifiedValuesArray = $this->db->escapeString(array($category_id));
        $verifiedId = $verifiedValuesArray[0];
        $query = $this->db->query('SELECT * FROM coupons WHERE category_id = "' . $verifiedId . '"');
        $rows = array();
        while ($row = $this->db->fetch($query)) {
            $rows[] = $row;
        }
        if (empty($rows)) {
            throw new Exception('List of coupons is empty ' . $category_id);
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
        if (is_null($result['shop_id'])) {
            return false;
        } else {
            return $result['shop_id'];
        }
    }

    public function getCategoryIdByUrl(string $url): string
    {
        $verifiedValuesArray = $this->db->escapeString(array($url));
        $verifiedName = $verifiedValuesArray[0];
        $query = $this->db->query('SELECT category_id FROM categories WHERE url = "' . $verifiedName. '"');

        $result = $this->db->fetch($query);
        echo $result['category_id'] . PHP_EOL;
        if (is_null($result['category_id'])) {
            return false;
        } else {
            return $result['category_id'];
        }
    }

    public function getCategories(int $limit) {
        $query = $this->db->query('SELECT * FROM categories LIMIT 0, ' . $limit);
        $rows = array();
        while ($row = $this->db->fetch($query)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getListOfCategoryUrls() {
        $query = $this->db->query('SELECT url from categories');
        $rows = array();
        while ($row = $this->db->fetch($query)) {
            $rows[] = $row['url'];
        }
        if (empty($rows)) {
            throw new Exception("Unable to get list of categories.");
        }
        return $rows;
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

    public function insertCategories(array $data)
    {
        foreach ($data as $item) {
            if (!$this->isCategoryExist($item[0])) {
                $stmt = $this->db->getInitializedStatement();
                if ($this->db->prepare('INSERT INTO categories (name, url) VALUES (?, ?)', $stmt)) {
                    $stmt->bind_param('ss', $item[0], $item[1]);
                    $this->db->execute($stmt);
                }
            }
        }
    }



    public function insertCoupons(array $parsedCoupons, string $site)
    {
        $shop_id = $this->getShopIdBySite($site);
        if (!$shop_id) {
            $category_id = $this->getCategoryIdByUrl($site);
        }
        foreach ($parsedCoupons as $item) {
            $verifiedValuesArray = $this->db->escapeString(array($item['date'], $item['title']));
            $date = $verifiedValuesArray[0];
            $title = $verifiedValuesArray[1];
            $query = $this->db->query('SELECT coupon_id FROM coupons WHERE date = ' . $date . ' AND title = "' . $title . '"');
            $result = $this->db->fetch($query);

            if (is_null($result)) {
                $stmt = $this->db->getInitializedStatement();
                if (!isset($category_id)) {
                    if ($this->db->prepare("INSERT INTO coupons (title, `desc`, img_src, times, shop_id, date) VALUES (?, ?, ?, ?, ?, ?)", $stmt)) {
                        $stmt->bind_param('ssssis', $item["title"], $item["desc"], $item["img_src"], $item["times"], $shop_id, $item["date"]);
                        $this->db->execute($stmt);
                    }
                }
                else {
                    if ($this->db->prepare("INSERT INTO coupons (title, `desc`, img_src, times, shop_id, date, category_id) VALUES (?, ?, ?, ?, ?, ?, ?)", $stmt)) {
                        $stmt->bind_param('ssssisi', $item["title"], $item["desc"], $item["img_src"], $item["times"], $shop_id, $item["date"], $category_id);
                        $this->db->execute($stmt);
                    }
                }
            }
        }
    }
}