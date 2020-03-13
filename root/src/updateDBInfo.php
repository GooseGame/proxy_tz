<?php

require_once __DIR__ . '/../../vendor/autoload.php';

try {
    $logger = new \Katzgrau\KLogger\Logger(__DIR__ . '/../../logs');
    #parse app.ini
    $config = parse_ini_file(__DIR__ . "/app.ini");
    #check validity
    $validator = new Validation\DataValidation();
    $validator->checkConfigValidation($config);
    #connect to db
    $db = new Db\DB($config);
    $logger->info('Successfully connected to database');
    $db->deleteOverdueCoupons();
    $logger->info("Successfully deleted overdue coupons");

    #connect to https://www.coupons.com/coupon-codes/ via proxy
    $proxy = new \Proxy\ProxyConnector($config, $logger);
    $raw = $proxy->connectAndGetRawData($config['stores_handler']);
    #parse data
    $parser = new Parser\ParseSites($config['baseUrl'], $logger);
    $shops = $parser->parse($raw);
    #insert parsed data
    $db->insertShops($shops);
    $logger->info("Successfully inserted new shops");
    #get full list of shops urls from db
    $urls = $db->getListOfShopUrls();
    #connect and insert coupons, using urls
    foreach ($urls as $url) {
        $raw = $proxy->connectAndGetRawData($url);
        $parser = new Parser\ParseCoupons($logger);
        $coupons = $parser->parse($raw);
        $db->insertCoupons($coupons, $url);
        $logger->info("Successfully inserted coupons from " . $url);
    }

} catch (Exception $e) {
    $logger->error($e->getMessage());
    exit;
}
