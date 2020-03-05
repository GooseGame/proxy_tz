<?php
    namespace tz\src\spider;

    use GuzzleHttp\Client;
    use Guzzle\Http\Message\Request;
    use Guzzle\Http\Message\Response;
    use GuzzleHttp\Exception\ConnectException;
    use GuzzleHttp\Exception\RequestException;



    class SpiderDecorator implements ParserComponentImpl
    {
        private $config;
        private $rawContent;
        private $component;
        private $site;
        private $isServerMode;
        protected $parsedData = 0;

        function __construct(ParserComponentImpl $component) {
            $this->config = parse_ini_file('app.ini');
            $this->component = $component;
        }

        function start(string $site, bool $isServerMode) {
            $this->site = $site;
            $this->isServerMode = $isServerMode;
            $this->connect();
        }

        function connect() {
            for ($i=0; $i<30; $i++) {
                echo $i;
                $isConnected = $this->tryToConnectProxy($this->config['ip'], $this->config['port']);
                if ($isConnected) {
                    $this->parse($this->rawContent, $this->isServerMode);
                    break;
                }
            }
        }

        function tryToConnectProxy(string $ip, string $port) {
            $agent = $this->config['agent'];
            try {
                $client = new Client([
                'base_uri' => $this->config['baseUrl']]);
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
                $this->rawContent = (string) $response -> getBody() -> getContents();
                return true;
            } catch (ConnectException | RequestException $e) {
                return false;
            }
        }

        function parse(string $rawHTML, bool $isServerMode) {
            try {
                $this->parsedData = $this->component->parse($rawHTML, $isServerMode);
            }
            catch (Exception $e) {
                return 0;
            }
        }
    }
?>