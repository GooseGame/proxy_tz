<?php

namespace Db;

class ClientResponse
{
    private $config;
    private $db;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->db = new DB($this->config);
    }

    public function getShopFromDB(string $site): string
    {
        return $this->db->getShopIdBySite($site);
    }

    public function getCouponsByShopId(string $id): array
    {
        return $this->db->getCouponsByShopId($id);
    }

    public function getCategories(int $getMaxCategories): array 
    {
        return $this->db->getCategories($getMaxCategories);
    }

    public function getCouponsByCategoryId(string $category_id) {
        return $this->db->getCouponsByCategoryId($category_id);
    }

    public function insertIP($ip, $id, $idOf) {
        return $this->db->insertIP($ip, $id, $idOf);
    }

    public function getIPIfo($ip) {
        return $this->db->getIPInfo($ip);
    }

    public function getThemes() {
        return $this->db->getThemes();
    }
}
