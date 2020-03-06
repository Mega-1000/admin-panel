<?php

namespace App\Integrations\Apaczka;

use GuzzleHttp\Client;

date_default_timezone_set('Europe/Warsaw');

class ApaczkaOrder {

    private $notificationDelivered = array();
    private $notificationException = array();
    private $notificationNew = array();
    private $notificationSent = array();
    // cash on delivery
    private $accountNumber = "";
    private $codAmount = "";
    private $orderPickupType = "COURIER";
    private $pickupTimeFrom = "";
    private $pickupTimeTo = "";
    private $pickupDate = "";
    private $pickUp = array();
    private $options = "";
    private $address_receiver = array();
    private $address_sender = array();
    private $referenceNumber = '';
    private $serviceCode = "";
    private $isDomestic = "true";
    private $serviceId = "";
    private $comment = "";
    private $contents = "";
    private $shipment = array();
    private static $dictServiceCode = array('UPS_K_STANDARD', 'UPS_K_EX_SAV', 'UPS_K_EX', 'UPS_K_EXP_PLUS', 'UPS_Z_STANDARD', 'UPS_Z_EX_SAV', 'UPS_Z_EX', 'UPS_Z_EXPEDITED', 'UPS_K_TODAY_STANDARD', 'UPS_K_TODAY_EXPRESS', 'UPS_K_TODAY_EXP_SAV', 'DPD_CLASSIC', 'DPD_CLASSIC_FOREIGN', 'DHLSTD', 'DHL12', 'DHL09', 'DHL1722', 'KEX_EXPRESS', 'FEDEX', 'POCZTA_POLSKA', 'POCZTA_POLSKA_E24', 'TNT', 'TNT_Z', 'TNT_Z2', 'POCZTEX_EXPRESS_24');
    private static $dictOrderPickupType = array('COURIER', 'SELF');
    private static $dictOrderOptions = array('POBRANIE', 'ZWROT_DOK', 'DOR_OSOBA_PRYW', 'DOST_SOB', 'PODPIS_DOROS');

    function ApaczkaOrder() {
        $this->notificationDelivered = $this->emptyNotification();
        $this->notificationException = $this->emptyNotification();
        $this->notificationNew = $this->emptyNotification();
        $this->notificationSent = $this->emptyNotification();
    }

    function setPobranie($accountNumber, $codAmount) {
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

    function createNotification($isReceiverEmail, $isReceiverSms, $isSenderEmail, $isSenderSms = null) {
        $notification = array();
        $notification['isReceiverEmail'] = $isReceiverEmail;
        $notification['isReceiverSms'] = $isReceiverSms;
        $notification['isSenderEmail'] = $isSenderEmail;
        if (!is_null($isSenderSms)) {
            $notification['isSenderSms'] = $isSenderSms;
        }
        return $notification;
    }

    function emptyNotification() {
        $notification = array();
        $notification['isReceiverEmail'] = '';
        $notification['isReceiverSms'] = '';
        $notification['isSenderEmail'] = '';
        $notification['isSenderSms'] = '';

        return $notification;
    }

    function setPickup($orderPickupType, $pickupTimeFrom, $pickupTimeTo, $pickupDate) {
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

    function setServiceCode($serviceCode, $carrierId) {
        if (!in_array($serviceCode, self::$dictServiceCode)) {
            throw new Exception('UNSUPPORTED service code: [' . $serviceCode . '] must be one of: ' . print_r(self::$dictServiceCode, 1));
        }

        $this->serviceCode = $serviceCode;
        $this->serviceId = $carrierId;
    }

    function addOrderOption($option) {
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

    function setReceiverAddress($name = '', $contactName = '', $addressLine1 = '', $addressLine2 = '', $city = '', $countryId = '', $postalCode = '', $stateCode = '', $email = '', $phone = '') {
        $this->address_receiver = $this->createAddress($name, $contactName, $addressLine1, $addressLine2, $city, $countryId, $postalCode, $stateCode, $email, $phone);
    }

    function setSenderAddress($name = '', $contactName = '', $addressLine1 = '', $addressLine2 = '', $city = '', $countryId = '', $postalCode = '', $stateCode = '', $email = '', $phone = '') {
        $this->address_sender = $this->createAddress($name, $contactName, $addressLine1, $addressLine2, $city, $countryId, $postalCode, $stateCode, $email, $phone);
    }

    function createAddress($name = '', $contactName = '', $addressLine1 = '', $addressLine2 = '', $city = '', $countryId = '', $postalCode = '', $stateCode = '', $email = '', $phone = '') {
        $address = array();
        $address['country_code'] = 'PL';
        $address['name'] = substr($name, 0, 50);
        $address['line1'] = $addressLine1;
        $address['line2'] = $addressLine2;
        $address['postalCode'] = $postalCode;
        $address['city'] = $city;
        $address['is_residential'] = 1;
        $address['contact_person'] = $contactName;
        $address['email'] = $email;
        $address['phone'] = $phone;
        if ($stateCode != '') {
            $address['stateCode'] = $stateCode;
        }


        return $address;
    }

    function createShipment($shipmentTypeCode, $dim1 = '', $dim2 = '', $dim3 = '', $weight = '') {

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

    function getOrder() {
//        if (!$this->accountNumber || !$this->codAmount){
//            $this->codAmount = 0;
//        }
        $order = [
            'service_id' => $this->serviceId,
            'address' => [
                'sender' => $this->address_sender,
                'receiver' => $this->address_receiver
            ],
            'option' => [
//                '31' => 0, // powiadomienie sms,
//                '11' => 0, // rod
//                '19' => 0, // dostawa w sobotę,
//                '25' => 0, // dostawa w godzinach,
                '58' => 0 // ostrożnie  
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
            'shipment' => [$this->shipment],
            'comment' => $this->comment,
            'content' => $this->contents
        ];
        return json_encode([
            'order' => $order
                ]);
    }

}