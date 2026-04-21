<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

trait CourierTrait
{
    public Client $httpClient;
    protected array $config;

    public function __construct()
    {
        $this->config = config('integrations');
        $this->httpClient = new Client();
    }

    /**
     * @throws GuzzleException
     */
    private function prepareConnectionForTrackingStatus(string $url, string $method, array $params): ResponseInterface
    {
        $curlSettings = ['curl' => [
            CURLOPT_SSL_CIPHER_LIST => 'DEFAULT@SECLEVEL=1',
            CURLOPT_USERAGENT => 'Mozilla Chrome Safari'
        ]];
        $params = array_merge($params, $curlSettings);

        return $this->httpClient->request($method, $url, $params);
    }

}
