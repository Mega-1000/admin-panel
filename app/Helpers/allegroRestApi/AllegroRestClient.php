<?php

namespace App\Helpers\allegroRestApi;

use App\Enums\CourierName;
use App\Services\AllegroApiService;

class AllegroRestClient extends AllegroApiService
{
    protected $auth_record_id = 1;

    public function __construct()
    {
        parent::__construct();
    }

    public function sendTrackingNumber($package)
    {
        $formId = $package->order->selloTransaction->tr_CheckoutFormId;

        switch ($package->service_courier_name) {
            case CourierName::ALLEGRO_INPOST:
            case CourierName::INPOST;
                $carrierId = CourierName::INPOST;
                break;
            case CourierName::DPD:
                $carrierId = CourierName::DPD;
                break;
        }
        $waybill = $package->letter_number;
        $carrierName = $package->service_courier_name;
        $lineItems = ['id' => $this->prepareLineItems($package)];

        $json = [
            'carrierId' => $carrierId,
            'waybill' => $waybill,
            'carrierName' => $carrierName,
            'lineItems' => [$lineItems]
        ];
        $url = "/order/checkout-forms/$formId/shipments";
        return $this->request('POST', $url, $json);
    }

    private function prepareLineItems($package)
    {
        $formId = $package->order->selloTransaction->tr_CheckoutFormId;
        $url = "/order/checkout-forms/$formId";
        $resp = json_decode((string)$this->request('GET', $url, [])->getBody());
        return $resp->lineItems[0]->id;
    }
}
