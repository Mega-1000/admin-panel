<?php

namespace App\Integrations\Apaczka;

use GuzzleHttp\Client;

class ApaczkaGuzzleClient {

//	Configuration
    private $appId = "";
    private $appSecret = "";
    private $outputFileName = "XOLTResult.log";
    private $Error = "";

    const DPD_CLASSIC = 21;
    const DHL12 = 83;
    const DHL09 = 84;
    const DHLSTD = 82;
    const FEDEX = 151;
    const TNT = 170;
    const INPOST = 42;
    const PACZKOMAT = 41;
    const POCZTEX_EXPRESS_24 = 161;

    private $mode = array('trace' => 1, 'exceptions' => 0, 'encoding' => 'UTF-8');
    protected $client;
    private $isTest = 0;
    private $isVerboseMode = 0;

    function __construct($appId = '', $appSecret = '') {
        if ($appId != '' && $appSecret != '') {
            $this->appId = $appId;
            $this->appSecret = $appSecret;
        }

        $this->init();
    }

    function init() {
        
    }

    function getSignature($string, $key) {
        return hash_hmac('sha256', $string, $key);
    }

    function stringToSign($appId, $route, $data, $expires) {
        return sprintf("%s:%s:%s:%s", $appId, $route, $data, $expires);
    }

    function placeOrder(ApaczkaOrder $order) {
        $data = $order->getOrder();
        $route = 'order_send/';

        $requestData = $this->makeRequest($route, $data);
        $resp = $this->sendRequest($route, $requestData);
        return $resp;
    }

    function makeRequest($route, $data) {
        $expires = time() + 1800;
        $signature = $this->getSignature($this->stringToSign($this->appId, $route, $data, $expires), $this->appSecret);
        $requestData = [
            'app_id' => $this->appId,
            'request' => $data,
            'expires' => $expires,
            'signature' => $signature
        ];
        return $requestData;
    }

    function sendRequest($route, $requestData) {

        $client = new \GuzzleHttp\Client(["base_uri" => 'https://www.apaczka.pl/api/v2/']);

        $options = ['form_params' => $requestData];
        $resp = $client->post($route, $options);
        return $resp;
    }

    function getWaybillDocument($orderId = false) {

        if (!is_numeric($orderId) || !(intval($orderId) > 0)) {
            throw new Exception('orderId must be intval: [' . print_r($orderId, 1) . '] given.');
        }

        $route = 'waybill/' . $orderId . '/';
        $data = json_encode([]);

        $requestData = $this->makeRequest($route, $data);
        $resp = $this->sendRequest($route, $requestData);

        return $resp;
    }

    function getCollectiveTurnInCopyDocument($orderId) {
        $route = 'turn_in/';
        $data = json_encode(['order_ids' => [$orderId]]);

        $requestData = $this->makeRequest($route, $data);
        $resp = $this->sendRequest($route, $requestData);

        return $resp;
    }

}

