<?php

require_once __DIR__ . '/../../vendor/autoload.php';

try {
    #parse app.ini
    $config = parse_ini_file(__DIR__ . "/app.ini");
    #check validity
    $validator = new Validation\DataValidation();
    $validator->checkConfigValidation($config);
    #connect to db
    $db = new Db\DB($config);
    echo "* deleting overdue coupons" . PHP_EOL;
    $db->deleteOverdueCoupons();

    echo "* inserting new coupons" . PHP_EOL;
    #connect to https://www.coupons.com/coupon-codes/ via proxy
    $proxy = new \Proxy\ProxyConnector($config);
    $raw = $proxy->connectAndGetRawData($config['stores_handler']);
    #parse data
    $parser = new Parser\ParseSites($config['baseUrl']);
    $shops = $parser->parse($raw);
    #insert parsed data
    $db->insertShops($shops);
    #get full list of shops urls from db
    $urls = $db->getListOfShopUrls();
    #connect and insert coupons, using urls
    foreach ($urls as $url) {
        $raw = $proxy->connectAndGetRawData($url);
        $parser = new Parser\ParseCoupons();
        $coupons = $parser->parse($raw);
        $db->insertCoupons($coupons, $url);
    }

} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}
