<?php
    require_once 'parser.php';

    use GuzzleHttp\Client;
    use Guzzle\Http\EntityBody;
    use Guzzle\Http\Message\Request;
    use Guzzle\Http\Message\Response;
    use GuzzleHttp\Exception\RequestException;
    require_once __DIR__ . '/../vendor/autoload.php';


    interface ParserComponentImpl {
        public function parse($data, $isServerMode);
    }

    class SpiderDecorator implements ParserComponentImpl
    {
        private $component;
        private $site;
        private $isServerMode;
        protected $parsedData = 0;

        function __construct($component) {
            $this->component = $component;
        }
        function start($site, $isServerMode) {
            $this->site = $site;
            $this->isServerMode = $isServerMode;
            $this->connect();
        }

        private function connect() {
            /*if one of proxy will be broken, i can connect to another
              if connection established loop kills
            */
            /*$proxies = array(
                            array('192.200.200.113', '3128'),
                            array('138.197.222.35', '8080'),
                            array('165.227.215.62', '8080'),
                            array('165.227.215.71', '8080'),
                            array('173.192.128.238', '8123'),
                            array('162.243.108.129', '3128')
                        );*/
            while (true) {
                #foreach ($proxies as $proxy) {
                      $output = $this->getRawContent('192.200.200.113', '3128');
                      if ($output != 0) {
                          return 0;
                      }
                #};
            }
        }

        private function getRawContent($ip, $port) {
            $agent = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';
            try {
                $client = new Client([
                'base_uri' => "https://www.coupons.com/coupon-codes/"]);
                /*curl proxy settings*/
                $response = $client->get($this->site
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
                $raw = (string) $response -> getBody() -> getContents();
                $output = $this->parse($raw, $this->isServerMode);
                return 1;
            } catch (Exception $e) {
                return 0;
            }
        }

        function parse($data, $isServerMode) {
            try {
                $this->parsedData = $this->component->parse($data, $isServerMode);
            }
            catch (Exception $e) {
                return 0;
            }
        }
    }
?>