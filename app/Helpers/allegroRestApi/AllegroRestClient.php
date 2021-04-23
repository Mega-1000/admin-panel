<?php

namespace App\Helpers\allegroRestApi;

use App\Entities\Allegro_Auth;
use GuzzleHttp\Client;

class AllegroRestClient
{

    public function __construct()
    {
        $this->token = Allegro_Auth::find(1);
        if (empty($this->token)) {
            \Log::error('Brak tokena autoryzującego allegro');
            return;
        }
        $this->client = new Client([
            'base_uri' => env('ALLEGRO_HOST'),
            'verify' => false
        ]);
    }

    public function sendTrackingNumber($package)
    {
        $formId = $package->order->selloTransaction->tr_CheckoutFormId;

        switch ($package->service_courier_name) {
            case 'ALLEGRO-INPOST':
            case 'INPOST':
                $carrierId = 'INPOST';
                break;
            case 'DPD':
                $carrierId = 'DPD';
                break;
            default:
                return;
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
        $resp = json_decode((string)$this->request('GET', $url)->getBody());
        return $resp->lineItems[0]->id;
    }

    private function request($method, $url, $params = false, $first = true)
    {
        if (empty($this->token)) {
            return false;
        }
        $headers = [
            'Accept' => 'application/vnd.allegro.public.v1+json',
            'Authorization' => "Bearer " . $this->token->access_token,
        ];

        if ($params) {
            $headers['Content-Type'] = 'application/vnd.allegro.public.v1+json';
        }
        try {
            $response = $this->client->request(
                $method,
                $url,
                ['headers' => $headers,
                    'json' => $params
                ]);
        } catch (\Exception $e) {
            if ($e->getCode() == 401 && $first) {
                $this->refreshToken();
                $response = $this->request($method, $url, $params, false);
            } else {
                \Log::error('Błąd w komunikacji z Allegro: ', ['message' => $e->getMessage(), 'stack' => $e->getTraceAsString()]);
                return false;
            }
        }
        return $response;
    }

    private function refreshToken()
    {
        $this->reauthClient = new Client([
            'base_uri' => env('ALLEGRO_REFRESH_HOST'),
            'verify' => false,
        ]);
        $resp = $this->reauthClient->post('token?' . http_build_query([
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->token->refresh_token
            ]),
            ['auth' => [
                env("ALLEGRO_CLIENT_ID"), env("ALLEGRO_CLIENT_SECRET")
            ]]
        );
        $parsed = json_decode((string)$resp->getBody());
        $this->token->access_token = $parsed->access_token;
        $this->token->refresh_token = $parsed->refresh_token;
        $this->token->save();
    }

}
