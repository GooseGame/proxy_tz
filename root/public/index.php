<?php
    namespace tz\public;

    require_once __DIR__ . '/../vendor/autoload.php';

    $site = $_GET['site'];
    $clientResponse = new ClientResponse;

    $clientResponse->checkAndGetDataFromDB($site);
    $clientResponse->echoJSONData();
?>