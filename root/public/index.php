<?php

require_once __DIR__ . '/../../vendor/autoload.php';

const SERVER_ERROR_MESSAGE = "There are some problem here. Try to refresh site.";

header('Content-Type: application/json');

try {
    #initialise logger
    $logger = new \Katzgrau\KLogger\Logger(__DIR__ . '/../../logs');
    #parse app.ini
    $config = parse_ini_file(__DIR__ . "/../src/app.ini");
    #check app.ini validity
    $validator = new \Validation\DataValidation;
    $validator->checkConfigValidation($config);
    #get attribute from client
    $site = isset($_GET['site']) ? $_GET['site'] : null;
    $getMaxCategories = isset($_GET['getMaxCategories']) ? $_GET['getMaxCategories'] : null;
    $category_id = isset($_GET['id']) ? $_GET['id'] : null;
    $check = isset($_GET['check']) ? $_GET['check'] : null;
    $themes = isset($_GET['themes']) ? $_GET['themes'] : null;

    $clientResponse = new Db\ClientResponse($config);
    $user_ip = $_SERVER['REMOTE_ADDR'];

    if (!is_null($getMaxCategories)) {
        $data = $clientResponse->getCategories((int) $getMaxCategories);
    }
    if (!is_null($site)) {
        #check site validity
        $validator->checkInputSiteValidation($site);
        #get shop id from db by site
        $shopId = $clientResponse->getShopFromDB($site);
        #get coupons by shop id
        $data = $clientResponse->getCouponsByShopId($shopId);
        #output result in json format
        $clientResponse->insertIP($user_ip, $shopId, "shops");

    }
    if (!is_null($category_id)) {
        $data = $clientResponse->getCouponsByCategoryId($category_id);
        $clientResponse->insertIP($user_ip, $category_id, "categories");
    }
    if (!is_null($check)) {
        $data = $clientResponse->getIPIfo($user_ip);
    }
    if (!is_null($themes)) {
        $data = $clientResponse->getThemes();
    }
    echo json_encode($data);

} catch (UnexpectedValueException $e) {
    $logger->error($e->getMessage());
    $data = array('message' => SERVER_ERROR_MESSAGE);
    echo json_encode($data);
} catch (Exception $e) {
    $logger->error($e->getMessage());
    $data = array('message' => $e->getMessage());
    echo json_encode($data);
}



/*json response (normal):
            [{'title': "...", 'img': "...", "desc": "...", "times": "..."}, ...]
        */
/*json response (error):
    [{'message': "some error"}]
*/