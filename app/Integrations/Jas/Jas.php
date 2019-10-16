<?php

namespace App\Integrations\Jas;

use Illuminate\Support\Facades\Storage;
use SoapClient;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Class Jas
 * @package App\Integrations\Jas
 */
class Jas
{
    /**
     * @var
     */
    protected $url;

    /**
     * @var
     */
    protected $user;

    /**
     * @var
     */
    protected $password;

    /**
     * @var null
     */
    protected $data;

    /**
     * @var
     */
    public $client;

    /**
     * @var
     */
    public $addShipmentResponse;

    /**
     * Jas constructor.
     * @param $config
     * @param null $data
     */
    public function __construct($config, $data = null)
    {
        $this->url = $config['production_url'];
        $this->user = $config['production_login'];
        $this->password = $config['production_password'];
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function login()
    {
        $paramsSoap = array(
            'encoding' => 'UTF-8',
            'verifypeer' => false,
            'verifyhost' => false,
            'soap_version' => SOAP_1_2,
            'trace' => 1,
            'exceptions' => 1,
            "connection_timeout" => 180,
        );

        $this->client = new SoapClient($this->url, $paramsSoap);
        $params = [
            'user' => $this->user,
            'password' => $this->password,
        ];
        $this->client->__soapCall('Logon', array($params));
        $result = $this->client->__getLastResponse();
        preg_match('/>(\d+)</', $result, $matches);
        preg_match('/>-(\d+)</', $result, $matchesError);
        if (isset($matchesError[1])) {
            $resultError = $matchesError[1];
            $this->getError($resultError);
        }
        if(isset($matches[1])) {
            $userId = $matches[1];

            return $userId;
        } else {
            return false;
        }
    }

    /**
     * @param $userId
     * @param $arg
     * @return mixed
     */
    public function createNewContractor($userId, $arg)
    {
        if (isset($this->data['delivery_address']['nip'])) {
            $nip = $this->data['delivery_address']['nip'];
        } else {
            $nip = 'null';
        }

        $params = [
            'userID' => $userId,
            'name' => $this->data[$arg.'_address']['firstname'] . ' ' . $this->data[$arg.'_address']['lastname'],
            'NIP' => $nip,
            'postalCode' => $this->data[$arg.'_address']['postal_code'],
            'city' => $this->data[$arg.'_address']['city'],
            'address' => $this->data[$arg.'_address']['address'],
            'houseNo' => $this->data[$arg.'_address']['flat_number'],
            'placeNo' => $this->data[$arg.'_address']['flat_number'],
            'contact' => $this->data[$arg.'_address']['phone']
        ];
        try {
            $this->client->__soapCall('NewContractor', array($params));
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $params);
        }


        $result = $this->client->__getLastResponse();
        preg_match('/>(\d+)</', $result, $matches);
        preg_match('/>-(\d+)</', $result, $matchesError);
        if (isset($matchesError[1])) {
            $resultError = $matchesError[1];
            $this->getError($resultError);
        }
        $contractorId = $matches[1];

        return $contractorId;
    }

    /**
     * @param $userId
     * @param $contractorId
     * @param $warehouseId
     * @return mixed
     */
    public function createWaybill($userId, $contractorId, $warehouseId)
    {
        $date = new Carbon($this->data['delivery_address']['delivery_date']);
        if (isset($this->data['number_account_for_cash_on_delivery'])) {
            $numberAccount = $this->data['number_account_for_cash_on_delivery'];
        } else {
            $numberAccount = null;
        }
        if (isset($this->data['price_for_cash_on_delivery'])) {
            $cashOnDeliveryAmount = $this->data['price_for_cash_on_delivery'];
        } else {
            $cashOnDeliveryAmount = 0;
        }
        if (isset($this->data['bank_name'])) {
            $bankName = $this->data['bank_name'];
        } else {
            $bankName = null;
        }
        $params = [
            'userID' => $userId,
            'freightPay' => 3,
            'otherLoadPlaceID' => $warehouseId,
            'addresseeID' => '-1',
            'otherUnloadPlaceID' => $contractorId,
            'sendDate' => $this->data['pickup_address']['parcel_date'],
            'sendHour' => '08:00',
            'deliveryDate' => $date->toDateString(),
            'deliveryHour' => '16:00',
            'goodsKind' => $this->data['content'],
            'cashOnGoods' => (double)$cashOnDeliveryAmount,
            'declareValue' => (double)$this->data['amount'],
            'bank' => $bankName,
            'IBAN' => $numberAccount,
            'palletReturn' => false,
            'palletValue' => 0,
            'wayBillReturn' => true,
            'documentReturn' => false,
            'remark' => $this->data['notices'],
            'duty' => false,
            'customOfficeID' => 0,
            'dutyZone' => null,
            'remarkBeforDuty' => null,
            'remarkAfterDuty' => null,
        ];

        try {
            $this->client->__soapCall('NewWayBill', array($params));
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $params);
        }
        $result = $this->client->__getLastResponse();

        preg_match('/>(\d+)</', $result, $matches);
        preg_match('/>-(\d+)</', $result, $matchesError);

        if (isset($matchesError[1])) {
            $resultError = $matchesError[1];
            $this->getError($resultError);
        }
        $wayBillId = $matches[1];

        return $wayBillId;
    }

    /**
     * @param $userId
     * @param $wayBillId
     * @return mixed
     */
    public function createNewCargo($userId, $wayBillId)
    {
        switch ($this->data['additional_data']['package_type']) {
            case 'EUR':
                $packagingId = 298;
                $packagingName = 'EPN';
                break;
            case 'INNA':
                $packagingId = 457;
                $packagingName = 'PPN';
                break;
            default:
                Log::error('Packaging is not valid');
                die();
        }

        $params = [
            'userID' => $userId,
            'shipmentID' => $wayBillId,
            'packaging' => $packagingName,
            'packagingID' => $packagingId,
            'length' => $this->data['width'],
            'width' => $this->data['length'],
            'height' => $this->data['height'],
            'weight' => $this->data['weight'],
            'quantity' => 1,
            'remark' => $this->data['notices'],
        ];

        try {
            $this->client->__soapCall('NewCargo1', array($params));
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $params);
        }
        $result = $this->client->__getLastResponse();

        preg_match('/>(\d+)</', $result, $matches);
        preg_match('/>-(\d+)</', $result, $matchesError);
        if (isset($matchesErrors[1])) {
            $resultError = $matchesError[1];
            $this->getError($resultError);
        }

        $cargoId = $matches[1];

        return $cargoId;

    }

    /**
     * @param $userId
     * @param $wayBillId
     * @return mixed
     */
    public function approveWayBill($userId, $wayBillId)
    {
        $params = [
            'userID' => $userId,
            'shipmentID' => $wayBillId,
        ];

        try {
            $this->client->__soapCall('ApproveWayBill', array($params));
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $params);
        }

        $result = $this->client->__getLastResponse();
        preg_match('/>(\d+)</', $result, $matches);
        preg_match('/>-(\d+)</', $result, $matchesError);
        if (isset($matchesErrors[1])) {
            $resultError = $matchesError[1];
            $this->getError($resultError);
        }

        $result2 = $this->client->__getLastRequest();
        Log::info($result);
        Log::info($result2);

        $approveWayBilLResponse = $matches[1];

        return $approveWayBilLResponse;
    }

    /**
     * @param $userId
     * @param $wayBillId
     */
    public function getPdfLabels($userId, $wayBillId)
    {
        $params = [
            'userID' => $userId,
            'shipmentID' => $wayBillId,
        ];

        try {
            $this->client->__soapCall('GetPdfLabels', array($params));
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $params);
        }

        $result = $this->client->__getLastResponse();
        $exp = explode('<GetPdfLabelsResult>', $result);
        $data = explode('</GetPdfLabelsResult', $exp[1]);

        Storage::disk('local')->put('public/jas/labels/label' . $wayBillId . '.pdf',
            base64_decode($data[0]));
    }

    /**
     * @param $userId
     * @param $wayBillId
     */
    public function getPdfLP($userId, $wayBillId)
    {
        $params = [
            'userID' => $userId,
            'shipmentID' => $wayBillId,
        ];

        try {
            $this->client->__soapCall('GetPdfLP', array($params));
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $params);
        }

        $result = $this->client->__getLastResponse();
        $exp = explode('<GetPdfLPResult>', $result);
        $data = explode('</GetPdfLPResult', $exp[1]);

        Storage::disk('local')->put('public/jas/protocols/protocol' . $wayBillId . '.pdf',
            base64_decode($data[0]));
    }

    /**
     * @param $userId
     * @param $wayBillId
     * @return mixed
     */
    public function getPackageStatus($userId, $wayBillId)
    {
        $params = [
            'userID' => $userId,
            'orderNo' => '',
            'waybillNo' => $wayBillId,
        ];

        try {
            $this->client->__soapCall('GetShipmentStatus', array($params));
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $params);
        }

        $result = $this->client->__getLastResponse();

        $exp = explode('<Status_Text>', $result);
        $data = explode('</Status_Text', $exp[1]);
        $status = $data[0];

        return $status;
    }


    /**
     * @param $error
     */
    public function getError($error)
    {
        $params = [
            'errorCode' => '-' . $error
        ];

        try {
            $this->client->__soapCall('GetError', array($params));
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $params);
        }
        $result = $this->client->__getLastResponse();
        Log::error(json_encode($result));
    }

}
