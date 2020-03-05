<?php
    namespace tz\src;

    require_once __DIR__ . '/../vendor/autoload.php';

    $db = new DB();
    echo "deleting overdue coupons";
    $db -> deleteOverdueCoupons();
    echo "inserting new coupons";
    $db -> insertAll();
?>