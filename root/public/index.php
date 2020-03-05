<?php
    require_once __DIR__ . '/../../vendor/autoload.php';

    $site = $_GET['site'];
    $clientResponse = new Db\ClientResponse;

    $clientResponse->checkAndGetDataFromDB($site);
    $clientResponse->echoJSONData();
?>