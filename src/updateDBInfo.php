<?php
    require_once 'DB.php';
    $db = new DB();
    echo "deleting overdue coupons";
    $db -> deleteOverdueCoupons();
    echo "inserting new coupons";
    $db -> insertAll();
?>