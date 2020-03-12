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
}
