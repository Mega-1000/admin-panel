<?php

namespace App\Integrations\Apaczka;
use GuzzleHttp\Client;
date_default_timezone_set('Europe/Warsaw');

class apaczkaApi
{

//	Configuration
    var $appId = "";
    var $appSecret = "";

    var $outputFileName = "XOLTResult.log";

    var $Error = "";

    private $mode = array('trace' => 1, 'exceptions' => 0, 'encoding' => 'UTF-8');
    protected $client;

    private $isTest = 0;
    private $isVerboseMode = 0;

    function __construct($appId = '', $appSecret = '')
    {
        if ($appId != '' && $appSecret != '') {
            $this->appId = $appId;
            $this->appSecret = $appSecret;
            
        }

        $this->init();
    }

    function init()
    {
    }
    
    function getSignature( $string, $key ) 
    {
        return hash_hmac( 'sha256', $string, $key );
    }
    
    function stringToSign( $appId, $route, $data, $expires )
    {
        return sprintf( "%s:%s:%s:%s", $appId, $route, $data, $expires );
    }
    
    function makeRequest($route, $data) 
    {
      $expires = time()+1800;  
      $signature =  $this->getSignature($this->stringToSign( $this->appId, $route, $data, $expires ), $this->appSecret );
      $requestData = [
       'appId' => $this->appId,
       'request' => $data,
       'expires' => $expires,
       'signature' => $signature   
      ];
      $resp = $this->sendRequest($route, $requestData);
      return $resp;
    }

    function sendRequest($route, $requestData)
    {
        $client= new \GuzzleHttp\Client(["base_uri" => $route]);
        $options = $requestData;

        $resp = $client->post("/post", $options);
        return $resp;
    }


    function placeOrder(ApaczkaOrder $order)
    {
        $data = $order->getOrder();
        $route = 'https://www.apaczka.pl/api/v2/order_send';

        $resp = $this->makeRequest($route, $data);

        return $resp;
    }


    function getWaybillDocument($orderId = false)
    {

        if (!is_numeric($orderId) || !(intval($orderId) > 0)) {
            throw new Exception('orderId must be intval: [' . print_r($orderId, 1) . '] given.');
        }

        $route = 'https://www.apaczka.pl/api/v2/waybill'.$orderId; 
        $data = json_encode( [] );

        $resp = $this->makeRequest($route, $data);
        
        return $resp;
    }

    function getCollectiveTurnInCopyDocument($orderId)
    {
        $route = 'https://www.apaczka.pl/api/v2/turn_in';
        $data= json_encode(['order_ids' => [$orderId]]); 

        $resp = $this->makeRequest($route, $data);

        return $resp;
    }
}

class ApaczkaOrder
{

    var $notificationDelivered = array();
    var $notificationException = array();
    var $notificationNew = array();
    var $notificationSent = array();

    // cash on delivery
    var $accountNumber = "";
    var $codAmount = "";

    var $orderPickupType = "COURIER";
    var $pickupTimeFrom = "";
    var $pickupTimeTo = "";
    var $pickupDate = "";
    var $pickUp = array();

    var $options = "";

    private $address_receiver = array();
    private $address_sender = array();

    var $referenceNumber = '';
    var $serviceCode = "";
    var $isDomestic = "true";
    var $serviceId ="";
    var $comment = "";
    var $contents = "";

    var $shipment = array();

    private static $dictServiceCode = array('UPS_K_STANDARD', 'UPS_K_EX_SAV', 'UPS_K_EX', 'UPS_K_EXP_PLUS', 'UPS_Z_STANDARD', 'UPS_Z_EX_SAV', 'UPS_Z_EX', 'UPS_Z_EXPEDITED', 'UPS_K_TODAY_STANDARD', 'UPS_K_TODAY_EXPRESS', 'UPS_K_TODAY_EXP_SAV', 'DPD_CLASSIC', 'DPD_CLASSIC_FOREIGN', 'DHLSTD', 'DHL12', 'DHL09', 'DHL1722', 'KEX_EXPRESS', 'FEDEX', 'POCZTA_POLSKA', 'POCZTA_POLSKA_E24', 'TNT', 'TNT_Z', 'TNT_Z2', 'POCZTEX_EXPRESS_24');
    private static $dictOrderPickupType = array('COURIER', 'SELF');
    private static $dictOrderOptions = array('POBRANIE', 'ZWROT_DOK', 'DOR_OSOBA_PRYW', 'DOST_SOB', 'PODPIS_DOROS');

    function ApaczkaOrder()
    {
        $this->notificationDelivered = $this->emptyNotification();
        $this->notificationException = $this->emptyNotification();
        $this->notificationNew = $this->emptyNotification();
        $this->notificationSent = $this->emptyNotification();
    }
 
    function setPobranie($accountNumber, $codAmount)
    {
        if (strlen($accountNumber) < 26) {
            throw new Exception('Bank account number to short: len(' . strlen($accountNumber) . ')[' . $accountNumber . ']');
        }

        if (!($codAmount > 0)) {
            throw new Exception('Cash on delivery amount must be greater then 0: [' . $codAmount . ']');
        }

        $this->accountNumber = $accountNumber;
        $this->codAmount = $codAmount;
        $this->addOrderOption('POBRANIE');
    }

    function setReferenceNumber($referenceNumber) {
        $this->referenceNumber = $referenceNumber;
    }
    function createNotification($isReceiverEmail, $isReceiverSms, $isSenderEmail, $isSenderSms)
    {
        $notification = array();
        $notification['isReceiverEmail'] = $isReceiverEmail;
        $notification['isReceiverSms'] = $isReceiverSms;
        $notification['isSenderEmail'] = $isSenderEmail;
        $notification['isSenderSms'] = $isSenderSms;

        return $notification;
    }

    function emptyNotification()
    {
        $notification = array();
        $notification['isReceiverEmail'] = '';
        $notification['isReceiverSms'] = '';
        $notification['isSenderEmail'] = '';
        $notification['isSenderSms'] = '';

        return $notification;
    }

    function setPickup($orderPickupType, $pickupTimeFrom, $pickupTimeTo, $pickupDate)
    {
        if (!in_array($orderPickupType, self::$dictOrderPickupType)) {
            throw new Exception('UNSUPPORTED order pickup type: [' . $orderPickupType . '] must be one of: ' . print_r(self::$dictOrderPickupType, 1));
        }

        $this->pickUp = [
            'type' => $orderPickupType,
            'date' => $pickupDate,
            'hours_from' => $pickupTimeFrom,
            'hours_to' => $pickupTimeTo
        ];
    }

    function setServiceCode($serviceCode, $carrierId)
    {
        if (!in_array($serviceCode, self::$dictServiceCode)) {
            throw new Exception('UNSUPPORTED service code: [' . $serviceCode . '] must be one of: ' . print_r(self::$dictServiceCode, 1));
        }

        $this->serviceCode = $serviceCode;
        $this->serviceId = $carrierId;
    }

    function addOrderOption($option)
    {
        if (!in_array($option, self::$dictOrderOptions)) {
            throw new Exception('UNSUPPORTED order option: [' . $option . '] must be one of: ' . print_r(self::$dictOrderOptions, 1));
        }

        if ($this->options == "") {
            $this->options = array('string' => $option);
        } else if (!is_array($this->options['string'])) {
            $tmp_option = $this->options['string'];

            if ($tmp_option != $option) {
                $this->options['string'] = array($tmp_option, $option);
            }
        } else {
            if (in_array($option, self::$dictOrderOptions)) {
                $this->options['string'][] = $option;
            }
        }
    }

    function setReceiverAddress($name = '', $contactName = '', $addressLine1 = '', $addressLine2 = '', $city = '', $countryId = '', $postalCode = '', $stateCode = '', $email = '', $phone = '')
    {
        $this->address_receiver = $this->createAddress($name, $contactName, $addressLine1, $addressLine2, $city, $countryId, $postalCode, $stateCode, $email, $phone);
    }

    function setSenderAddress($name = '', $contactName = '', $addressLine1 = '', $addressLine2 = '', $city = '', $countryId = '', $postalCode = '', $stateCode = '', $email = '', $phone = '')
    {
        $this->address_sender = $this->createAddress($name, $contactName, $addressLine1, $addressLine2, $city, $countryId, $postalCode, $stateCode, $email, $phone);
    }

    function createAddress($name = '', $contactName = '', $addressLine1 = '', $addressLine2 = '', $city = '', $countryId = '', $postalCode = '', $stateCode = '', $email = '', $phone = '')
    {

        $address = array();
        $address['country_code'] = 'PL';
        $address['name'] = substr($name, 0, 50);        
        $address['line1'] = $addressLine1;
        $address['line2'] = $addressLine2;
        $address['postalCode'] = $postalCode;
        $address['city'] = $city;
        $address['is_residential'] = 1;
        $address['contactName'] = $contactName;
        $address['email'] = $email;
        $address['phone'] = $phone;
        if ($stateCode != '') {
            $address['stateCode'] = $stateCode;
        }


        return $address;
    }

     function createShipment($shipmentTypeCode, $dim1 = '', $dim2 = '', $dim3 = '', $weight = '')
    {

        $shipment = [
            'dimension1' => $dim1,
            'dimension2' => $dim2,
            'dimension3' => $dim3,
            'weight' => $weight,
            'is_nstd' => 0,
            'shipment_type_code' => $shipmentTypeCode
        ];
        $this->shipment = $shipment;
    }

    function getOrder()
    {
//        if (!$this->accountNumber || !$this->codAmount){
//            $this->codAmount = 0;
//        }
        $order = json_encode([
            'service_id' => $this->serviceId,
            'address' => [
                'sender' => $this->address_sender,
                'receiver' => $this->address_receiver                
            ],
            'option' => [
                '31' => 1, // powiadomienie sms,
                '11' => 0, // rod
                '19' => 0, // dostawa w sobotę,
                '25' => 0, // dostawa w godzinach,
                '58' => 0, // ostrożnie  
            ],
            'notification' => [
                'new' => $this->notificationNew,
                'sent' => $this->notificationSent,
                'exception' => $this->notificationException,
                'delivered' => $this->notificationDelivered
            ],
            'shipment_value' => '',
            'cod' => [
                'amount' => $this->codAmount,
                'bankaccount' => $this->accountNumber     
            ],
            'pickup' => $this->pickUp,
            'shipment' => $this->shipment,
            'comment' => $this->comment,
            'content' => $this->contents           
        ]);
        
        return $order;
    }

}

