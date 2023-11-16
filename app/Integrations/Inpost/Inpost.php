<?php

namespace App\Integrations\Inpost;

use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Class Inpost
 * @package App\Integrations\Inpost
 */
class Inpost
{
    const PACZKOMAT = 'PACZKOMAT';
    /**
     * @var
     */
    protected $data;

    /**
     * @var Repository|mixed
     */
    protected $url;

    /**
     * @var Repository|mixed
     */
    protected $authorization;

    /**
     * Inpost constructor.
     * @param null $data
     * @param null $allegro
     */
    public function __construct($data = null, $allegro = null)
    {
        $this->data = $data;
        $this->url = config('integrations.inpost.url');
        $this->authorization = config('integrations.inpost.authorization');
        $this->org_id = config('integrations.inpost.org_id');
        $this->allegro = $allegro;
    }

    /**
     * @return string
     */
    public function prepareJsonForInpost()
    {
        if ($this->data !== null) {
            $address = [
                'street' => $this->data['delivery_address']['address'],
                'building_number' => $this->data['delivery_address']['flat_number'],
                'city' => $this->data['delivery_address']['city'],
                'post_code' => $this->data['delivery_address']['postal_code'],
                'country_code' => 'PL'
            ];
            $addressSender = [
                'street' => $this->data['pickup_address']['address'],
                'building_number' => $this->data['pickup_address']['flat_number'],
                'city' => $this->data['pickup_address']['city'],
                'post_code' => $this->data['pickup_address']['postal_code'],
                'country_code' => 'PL'
            ];
            if ($this->data['courier_type'] == self::PACZKOMAT && $this->allegro) {
                $sections = [
                    'receiver' => [
                        'email' => $this->data['additional_data']['allegro_mail'],
                        'phone' => $this->data['delivery_address']['phone']
                    ],
                ];
            } else if ($this->data['courier_type'] == self::PACZKOMAT) {
                $sections = [
                    'receiver' => [
                        'email' => $this->data['delivery_address']['email'],
                        'phone' => $this->data['delivery_address']['phone']
                    ],
                    'custom_attributes' => [
                        'target_point' => $this->data['delivery_address']['lastname']
                    ]
                ];
            } else if ($this->allegro) {
                $sections = [
                    'receiver' => [
                        'first_name' => $this->data['delivery_address']['firstname'],
                        'last_name' => $this->data['delivery_address']['lastname'],
                        'email' => $this->data['additional_data']['allegro_mail'],
                        'phone' => $this->data['delivery_address']['phone'],
                        'address' => $address
                    ]
                ];
            } else {
                $sections = [
                    'receiver' => [
                        'first_name' => $this->data['delivery_address']['firstname'],
                        'last_name' => $this->data['delivery_address']['lastname'],
                        'email' => $this->data['delivery_address']['email'],
                        'phone' => $this->data['delivery_address']['phone'],
                        'address' => $address
                    ]
                ];
            }
            $sections += [
                'sender' => [
                    'first_name' => $this->data['pickup_address']['firstname'],
                    'last_name' => $this->data['pickup_address']['lastname'],
                    'email' => $this->data['pickup_address']['email'],
                    'phone' => $this->data['pickup_address']['phone'],
                    'address' => $addressSender
                ]
            ];
            if ($this->data['courier_type'] == self::PACZKOMAT && $this->allegro) {
                $sections += [
                    'custom_attributes' => [
                        'target_point' => $this->data['delivery_address']['lastname'],
                        'sending_method' => 'dispatch_order',
                        'allegro_transaction_id' => $this->data['additional_data']['allegro_transaction_id'],
                        'allegro_user_id' => $this->data['additional_data']['allegro_user_id']
                    ]
                ];
            } else if ($this->allegro) {
                $sections += [
                    'custom_attributes' => [
                        'sending_method' => 'dispatch_order',
                        'allegro_transaction_id' => $this->data['additional_data']['allegro_transaction_id'],
                        'allegro_user_id' => $this->data['additional_data']['allegro_user_id']
                    ]
                ];
            }

            $sections += [
                'comments' => $this->data['notices']
            ];

            $parcels = $this->prepareParcelArray();
            $sections += [
                'parcels' => [$parcels]
            ];

            if ($this->data['cash_on_delivery']) {
                $sections += [
                    'insurance' => [
                        'amount' => $this->data['price_for_cash_on_delivery'],
                        'currency' => 'PLN'
                    ],
                    'cod' => [
                        'amount' => $this->data['price_for_cash_on_delivery'],
                        'currency' => 'PLN'
                    ]
                ];
            }

            if ($this->data['courier_type'] == self::PACZKOMAT && $this->allegro) {
                $sections += [
                    'service' => 'inpost_locker_allegro'
                ];
            } else if ($this->data['courier_type'] == self::PACZKOMAT) {
                $sections += [
                    'service' => 'inpost_locker_standard'
                ];
            } else if ($this->allegro) {
                $sections += [
                    'service' => 'inpost_courier_allegro'
                ];
            } else {
                $sections += [
                    'service' => 'inpost_courier_standard',
                    'additional_services' => [
                        'email',
                        'sms'
                    ]
                ];
            }

            $sections += [
                'reference' => $this->data['notices']
            ];

            return json_encode($sections);
        } else {
            return null;
        }
    }

    /**
     * @return array
     */
    protected function prepareParcelArray(): array
    {
        $parcels = [];
        $template = false;
        if ($this->data['courier_type'] == self::PACZKOMAT) {
            $template = $this->prepareTemplateForLocker();
        }
        if ($template) {
            $parcels['template'] = $template;
        }
        $parcels ['dimensions'] = [
            'length' => $this->data['length'],
            'width' => $this->data['width'],
            'height' => $this->data['height'],
            'unit' => 'mm',
        ];

        $parcels ['weight'] = [
            'amount' => $this->data['weight'],
            'unit' => 'kg'
        ];
        return $parcels;
    }

    /**
     * @return string
     */
    protected function prepareTemplateForLocker(): string
    {
        $symbol = explode('_', $this->data['symbol']);
        return match (strtolower($symbol[1])) {
            'pa' => "small",
            'pb' => "medium",
            'pc' => "large",
            default => false,
        };
    }

    public function createSimplePackage($json)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . '/v1/organizations/' . $this->org_id . '/shipments');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $this->authorization));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if (config('app.env') == 'local') {
            $PROXY_HOST = "127.0.0.1";
            $PROXY_PORT = "2180";
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_PROXY_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_PROXY, $PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, $PROXY_PORT);
        }
        $output = curl_exec($ch);
        $curl_error = curl_error($ch);
        if ($curl_error) {
            Log::notice(
                'Error in INPOST integration - method createSimplePackage',
                ['courier' => 'INPOST', 'class' => get_class($this), 'line' => __LINE__]
            );
        }
        curl_close($ch);

        $result = json_decode($output);

        return $result;
    }

    public function getLabel($id, $trackingNumber): bool
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . '/v1/organizations/' . $this->org_id . '/shipments/labels');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"format": "pdf","shipment_ids": [' . $id . ']}');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $this->authorization));
        $output = curl_exec($ch);
        $curl_error = curl_error($ch);
        if ($curl_error) {
            Log::notice(
                'Error in INPOST integration - method getLabel',
                ['courier' => 'INPOST', 'class' => get_class($this), 'line' => __LINE__]
            );
        }
        curl_close($ch);

        $filePath = '/storage/inpost/stickers/sticker' . $trackingNumber . '.pdf';

        return Storage::disk('public')->put($filePath, $output);
    }

    public function hrefExecute($href)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $href);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $this->authorization));
        $output = curl_exec($ch);

        $curl_error = curl_error($ch);
        if ($curl_error) {
            Log::notice(
                'Error in INPOST integration - method hrefExecute',
                ['error' => $curl_error, 'courier' => 'INPOST', 'class' => get_class($this), 'line' => __LINE__]
            );
        }
        curl_close($ch);

        $result = json_decode($output);

        return $result;
    }
}
