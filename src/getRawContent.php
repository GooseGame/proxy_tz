<?php
    use GuzzleHttp\Client;
    use Guzzle\Http\EntityBody;
    use Guzzle\Http\Message\Request;
    use Guzzle\Http\Message\Response;

    use GuzzleHttp\Exception\RequestException;

    require_once 'parser.php';
    require_once __DIR__ . '/../vendor/autoload.php';

    function getRawContent($site, $ip, $port) {
        $agent = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';
            try {
              $client = new Client([
                'base_uri' => "https://www.coupons.com/coupon-codes/"]);
                /*curl proxy settings*/
              $response = $client->get($site
              , [

                'cookie' => true,
                'curl' => [
                    CURLOPT_USERAGENT => $agent,
                    CURLOPT_TIMEOUT => 25,
                    CURLOPT_PORT => "443",
                    CURLOPT_PROXY => $ip,
                    CURLOPT_PROXYPORT => $port,
                ],
              ]);
              echo parse((string) $response -> getBody() -> getContents());
              return true;
              exit;
            } catch (Exception $e) {
               return false;
            }
    }
?>