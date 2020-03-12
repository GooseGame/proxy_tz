<?php

require_once __DIR__ . '/../../vendor/autoload.php';

const SERVER_ERROR_MESSAGE = "There are some problem here. Try to refresh site.";

header('Content-Type: application/json');

try {
    #parse app.ini
    $config = parse_ini_file(__DIR__ . "/../src/app.ini");
    #check app.ini validity
    $validator = new \Validation\DataValidation;
    $validator->checkConfigValidation($config);
    #get 'site' attribute from client
    $site = $_GET['site'];
    #check site validity
    $validator->checkInputSiteValidation($site);
    #get shop id from db by site
    $clientResponse = new Db\ClientResponse($config);
    $shopId = $clientResponse->getShopFromDB($site);
    #get coupons by shop id
    $data = $clientResponse->getCouponsByShopId($shopId);
    #output result in json format
    echo json_encode($data);
} catch (UnexpectedValueException $e) {
    $data = array('message' => SERVER_ERROR_MESSAGE);
    echo json_encode($data);
} catch (Exception $e) {
    $data = array('message' => $e->getMessage());
    echo json_encode($data);
}



/*json response (normal):
            [{'title': "...", 'img': "...", "desc": "...", "times": "..."}, ...]
        */
/*json response (error):
    [{'message': "some error"}]
*/