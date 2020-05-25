<?php

namespace Proxy;

use GuzzleHttp\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Katzgrau\KLogger\Logger;

class ProxyConnector
{
    private $config;
    private $rawContent;
    private $site;
    private $logger;
    const MAX_TRIES = 50;

    function __construct($config, Logger $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    function connectAndGetRawData(string $site): string
    {
        $this->site = $site;
        $this->logger->info('Trying to connect proxy');
        for ($i=0; $i<self::MAX_TRIES; $i++) {
            $isConnected = $this->tryToConnectProxy($this->config['ip'], $this->config['port']);
            if ($isConnected) {
                return $this->rawContent;
            }
        }
        return new \Exception("Unable to connect this proxy. Use another or try again");
    }

    function tryToConnectProxy(string $ip, string $port): bool
    {
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
            $this->setRawContent($response -> getBody() -> getContents());
            return true;
        } catch (ConnectException | RequestException $e) {
            return false;
        }
    }
    public function setRawContent(string $response)
    {
        $this->rawContent = $response;
    }
}