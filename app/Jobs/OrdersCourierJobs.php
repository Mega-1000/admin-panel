<?php

namespace App\Jobs;

use App\Integrations\Inpost\Inpost;
use App\Integrations\Jas\Jas;
use App\Integrations\Pocztex\addShipment;
use App\Integrations\Pocztex\adresType;
use App\Integrations\Pocztex\clearEnvelope;
use App\Integrations\Pocztex\paletaType;
use App\Integrations\Pocztex\platnikType;
use App\Integrations\Pocztex\pobranieType;
use App\Integrations\Pocztex\przesylkaPaletowaType;
use App\Integrations\Pocztex\rodzajPaletyType;
use App\Integrations\Pocztex\sendEnvelope;
use App\Integrations\Pocztex\sposobPobraniaType;
use App\Mail\SendLPToTheWarehouseAfterOrderCourierMail;
use App\Repositories\OrderPackageRepository;
use Illuminate\Support\Facades\Log;
use App\Integrations\DPD\DPDService;
use App\Integrations\Apaczka\apaczkaApi;
use App\Integrations\Apaczka\ApaczkaOrder;
use App\Integrations\Apaczka\ApaczkaOrderShipment;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Mockery\Exception;

/**
 * Class OrdersCourierJobs
 * @package App\Jobs
 */
class OrdersCourierJobs extends Job
{
    /**
     *
     */
    const ERRORS = [
        'INVALID_COURIER' => 1,
        'INVALID_AUTH_DATA_COURIER' => 2,
        'INVALID_FORWARDING_DELIVERY' => 3,
        'PROBLEM_IN_PLACE_ORDER' => 4,
        'PROBLEM_WITH_DOWNLOAD_WAYBILL' => 5,
        'PROBLEM_WITH_DPD_INTEGRATION' => 6,
        'PROBLEM_WITH_APACZKA_INTEGRATION' => 7,
        'PROBLEM_WITH_INPOST_INTEGRATION' => 8,
        'PROBLEM_WITH_JAS_INTEGRATION' => 9
    ];

    /**
     * @var
     */
    protected $data;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    protected $config;

    /**
     * @var
     */
    protected $courierName;

    /**
     * @var OrderPackageRepository
     */
    protected $orderPackageRepository;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->config = config('integrations');
        $this->courierName = $this->data['courier_name'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(OrderPackageRepository $orderPackageRepository)
    {
        $this->orderPackageRepository = $orderPackageRepository;
        if ($this->data['delivery_address']['email'] === null) {
            $this->data['delivery_address']['email'] = $this->orderPackageRepository->order->customer->login;
        }
        switch ($this->courierName) {
            case 'DPD':
                $result = $this->createPackageForDpd();
                break;
            case 'INPOST':
                $result = $this->createPackageForInpost();
                break;
            case 'APACZKA':
                $result = $this->createPackageForApaczka();
                break;
            case 'POCZTEX':
                $result = $this->createPackageForPocztex();
                break;
            case 'JAS':
                $result = $this->createPackageForJas();
                break;
            default:
                Log::notice(
                    'Wrong courier',
                    ['courier' => $this->courierName, 'class' => get_class($this), 'line' => __LINE__]
                );
                die;
        }

        $this->orderPackageRepository->update([
            'sending_number' => $result['sending_number'],
            'letter_number' => $result['letter_number'],
            'status' => 'WAITING_FOR_SENDING'
        ], $this->data['additional_data']['order_package_id']);
        $package = $this->orderPackageRepository->find($this->data['additional_data']['order_package_id']);
        if ($package->delivery_courier_name !== 'INPOST') {
            if ($package->delivery_courier_name === 'DPD') {
                $path = storage_path('app/public/dpd/protocols/protocol' . $package->letter_number . '.pdf');
            } elseif ($package->delivery_courier_name === 'JAS') {
                $path = storage_path('app/public/jas/protocols/protocol' . $package->letter_number . '.pdf');
            } else {
                if ($package->delivery_courier_name === 'POCZTEX') {
                    $path = storage_path('app/public/pocztex/protocols/protocol' . $package->sending_number . '.pdf');
                }
            }

            if ($path !== null) {
                \Mailer::create()
                    ->to($package->order->warehouse->firm->email)
                    ->send(new SendLPToTheWarehouseAfterOrderCourierMail("List przewozowy przesyłki nr: " . $package->order->id . '/' . $package->number,
                        $path, $package->order->id . '/' . $package->number));
            }
        }
    }

    /**
     * @return array
     *
     */
    public function createPackageForDpd()
    {
        try {
            $sender = [
                'fid' => $this->config['dpd']['fid'],
                'name' => $this->data['pickup_address']['firstname'] . ' ' . $this->data['pickup_address']['lastname'],
                'company' => $this->data['pickup_address']['firmname'],
                'address' => $this->data['pickup_address']['address'],
                'city' => $this->data['pickup_address']['city'],
                'postalCode' => str_replace('-', '', $this->data['pickup_address']['postal_code']),
                'countryCode' => 'PL',
                'email' => $this->data['pickup_address']['email'],
                'phone' => $this->data['pickup_address']['phone']
            ];

            $dpd = new DPDService($this->config['dpd']['fid'], $this->config['dpd']['username'],
                $this->config['dpd']['password'], $this->config['dpd']['wsdl']);
            $dpd->setConfig('lang_code', $this->config['dpd']['lang_code']);
            $dpd->setConfig('debug', $this->config['dpd']['debug']);
            $dpd->setConfig('log_errors', $this->config['dpd']['log_errors']);
            $dpd->setConfig('log_path', $this->config['dpd']['log_path']);
            $dpd->setConfig('timezone', $this->config['dpd']['timezone']);
            $dpd->setSender($sender);

            $parcels = [
                0 => [
                    'content' => $this->data['content'],
                    'sizex' => $this->data['width'],
                    'sizey' => $this->data['length'],
                    'sizez' => $this->data['height'],
                    'weight' => $this->data['weight'],
                    'customerNotes' => $this->data['notices']
                ],
            ];

            $receiver = [
                'name' => $this->data['delivery_address']['firstname'] . ' ' . $this->data['delivery_address']['lastname'],
                'company' => isset($this->data['delivery_address']['firmname']) ? $this->data['delivery_address']['firmname'] : null,
                'address' => $this->data['delivery_address']['address'] . ' ' . $this->data['delivery_address']['flat_number'],
                'city' => $this->data['delivery_address']['city'],
                'postalCode' => str_replace('-', '', $this->data['delivery_address']['postal_code']),
                'countryCode' => 'PL',
                'email' => $this->data['delivery_address']['email'],
                'phone' => $this->data['delivery_address']['phone']
            ];

            $result = $dpd->sendPackage($parcels, $receiver, 'SENDER');

            if ($result->success == false) {
                Session::put('message', $result);
                Log::info(
                    'Problem with send package in DPD',
                    [
                        'courier' => $this->courierName,
                        'result' => $result->success,
                        'class' => get_class($this),
                        'line' => __LINE__
                    ]
                );
                die();
            }

            $pickupAddress = [
                'fid' => $this->config['dpd']['fid'],
                'name' => $this->data['pickup_address']['firstname'] . ' ' . $this->data['pickup_address']['lastname'],
                'company' => $this->data['pickup_address']['firmname'],
                'address' => $this->data['pickup_address']['address'] . ' ' . $this->data['pickup_address']['flat_number'],
                'city' => $this->data['pickup_address']['city'],
                'postalCode' => str_replace('-', '', $this->data['pickup_address']['postal_code']),
                'countryCode' => 'PL',
                'email' => $this->data['pickup_address']['email'],
                'phone' => $this->data['pickup_address']['phone']
            ];

            $speedlabel = $dpd->generateSpeedLabelsByPackageIds([$result->packageId], $pickupAddress);

            Storage::disk('local')->put('public/dpd/stickers/sticker' . $result->parcels[0]->Waybill . '.pdf',
                $speedlabel->filedata);

            $protocol = $dpd->generateProtocolByPackageIds([$result->packageId], $pickupAddress);
            Storage::disk('local')->put('public/dpd/protocols/protocol' . $result->parcels[0]->Waybill . '.pdf',
                $protocol->filedata);

            $date = Carbon::parse($this->data['pickup_address']['parcel_date'])->format('Y-m-d');
            $pickupDate = $date;
            $pickupTimeFrom = '10:00';
            $pickupTimeTo = '16:00';

            $contactInfo = [
                'name' => $this->data['pickup_address']['firstname'] . ' ' . $this->data['pickup_address']['lastname'],
                'company' => $this->data['pickup_address']['firmname'],
                'address' => $this->data['pickup_address']['address'],
                'email' => $this->data['pickup_address']['email'],
            ];

            $response = $dpd->pickupRequest([$protocol->documentId], $pickupDate, $pickupTimeFrom, $pickupTimeTo, $contactInfo,
                $pickupAddress, $parcels, $receiver);
            return [
                'status' => 200,
                'error_code' => 0,
                'sending_number' => $response->return->orderNumber,
                'letter_number' => $result->parcels[0]->Waybill
            ];
        } catch (Exception $exception) {
            Log::info(
                'Problem in DPD integration',
                ['courier' => $this->courierName, 'class' => get_class($this), 'line' => __LINE__]
            );
            Session::put('message', $exception->getMessage());
            return ['status' => '500', 'error_code' => self::ERRORS['PROBLEM_WITH_DPD_INTEGRATION']];
        }

    }

    /**
     * @return array
     */
    public function createPackageForInpost()
    {
        try {
            $integration = new Inpost($this->data);
            $json = $integration->prepareJsonForInpost();
            $package = $integration->createSimplePackage($json);
            if ($package->status == '400') {
                Session::put('message', $package);
                Log::info(
                    'Problem in INPOST integration with validation',
                    ['courier' => $package, 'class' => get_class($this), 'line' => __LINE__]
                );
                die();
            }

            $this->orderPackageRepository->update([
                'inpost_url' => $package->href,
            ], $this->data['additional_data']['order_package_id']);
            $href = $integration->hrefExecute($package->href);

            return [
                'status' => 200,
                'error_code' => 0,
                'sending_number' => $href->id,
                'letter_number' => null,
            ];
        } catch (Exception $exception) {
            Session::put('message', $exception->getMessage());
            Log::info(
                'Problem in INPOST integration',
                ['courier' => $this->courierName, 'class' => get_class($this), 'line' => __LINE__]
            );
            return ['status' => '500', 'error_code' => self::ERRORS['PROBLEM_WITH_DPD_INTEGRATION']];
        }
    }


    /**
     * @return array
     */
    public function createPackageForApaczka()
    {
        try {
            try {
                $apaczka = new apaczkaApi($this->config['apaczka']['login'], $this->config['apaczka']['password'],
                    $this->config['apaczka']['apiKey']);
                $apaczka->setProductionMode();
                $apaczka->validateAuthData();
            } catch (Exception $exception) {
                Log::notice(
                    'validateAuthData ERROR',
                    ['courier' => $this->courierName, 'class' => get_class($this), 'line' => __LINE__]
                );
                return ['status' => '500', 'error_code' => self::ERRORS['INVALID_AUTH_DATA_COURIER']];
            }
            $forwardingDelivery = $this->data['additional_data']['forwarding_delivery'];
            switch ($forwardingDelivery) {
                case 'DPD_CLASSIC':
                    $carrierType = 'DPD_CLASSIC';
                    break;
                case 'DHLSTD':
                    $carrierType = 'DHLSTD';
                    break;
                case 'DHL12':
                    $carrierType = 'DHL12';
                    break;
                case 'DHL09':
                    $carrierType = 'DHL09';
                    break;
                case 'DHL1722':
                    $carrierType = 'DHL1722';
                    break;
                case 'KEX_EXPRESS':
                    $carrierType = 'KEX_EXPRESS';
                    break;
                case 'FEDEX':
                    $carrierType = 'FEDEX';
                    break;
                case 'POCZTA_POLSKA_E24':
                    $carrierType = 'POCZTA_POLSKA_E24';
                    break;
                case 'TNT':
                    $carrierType = 'TNT';
                    break;
                case 'INPOST':
                    $carrierType = 'INPOST';
                    break;
                case 'PACZKOMAT':
                    $carrierType = 'PACZKOMAT';
                    break;
                case 'POCZTEX':
                    $carrierType = 'POCZTEX';
                    break;
                default:
                    Log::notice(
                        'Wrong courier',
                        ['courier' => $this->courierName, 'class' => get_class($this), 'line' => __LINE__]
                    );
                    return ['status' => '500', 'error_code' => self::ERRORS['INVALID_FORWARDING_DELIVERY']];
            }

            $order = new ApaczkaOrder();

            $order->notificationDelivered = $order->createNotification(false, false, true, false);
            $order->notificationException = $order->createNotification(false, false, true, false);
            $order->notificationNew = $order->createNotification(false, false, true, false);
            $order->notificationSent = $order->createNotification(false, false, true, false);

            $order->setServiceCode($carrierType);
            $order->referenceNumber = $this->data['order_id'];
            $order->contents = $this->data['content'];

            $order->setReceiverAddress(
                $this->data['delivery_address']['firstname'],
                $this->data['delivery_address']['lastname'],
                $this->data['delivery_address']['address'],
                $this->data['delivery_address']['flat_number'],
                $this->data['delivery_address']['city'],
                0,
                $this->data['delivery_address']['postal_code'],
                '',
                $this->data['delivery_address']['email'],
                $this->data['delivery_address']['phone']
            );
            if ($this->data['cash_on_delivery'] === true) {
                $order->setPobranie($this->data['number_account_for_cash_on_delivery'],
                    $this->data['price_for_cash_on_delivery']);
            }

            $order->setReferenceNumber($this->data['additional_data']['order_package_id'] . ' : ' . $this->data['notices']);
            $width = $this->data['width'];
            $length = $this->data['length'];
            $height = $this->data['height'];
            $weight = $this->data['weight'];

            $orderShipment = new ApaczkaOrderShipment();
            $orderShipment->createShipment('PACZ', $width, $length, $height, $weight);

            $date = Carbon::now();

            $order->addShipment($orderShipment);
            if (isset($this->data['pickup_address'])) {
                $order->setSenderAddress(
                    $this->data['pickup_address']['firmname'],
                    $this->data['pickup_address']['firstname'] . ' ' . $this->data['pickup_address']['lastname'],
                    $this->data['pickup_address']['address'],
                    $this->data['pickup_address']['flat_number'],
                    $this->data['pickup_address']['city'],
                    0,
                    $this->data['pickup_address']['postal_code'],
                    '',
                    $this->data['pickup_address']['email'],
                    $this->data['pickup_address']['phone']
                );
                if ($this->data['courier_type'] === 'ODBIOR_OSOBISTY') {
                    $pickup = 'SELF'; 
                } else {
                    $pickup = 'COURIER';
                }
                $order->setPickup(
                    $pickup,
                    6,
                    17,
                    $this->data['pickup_address']['parcel_date']
                );
            }

            $result = $apaczka->placeOrder($order);
            if ($result !== false && $result->return->order) {
                $orderId = $result->return->order->id;
            } else {
                Log::notice(
                    $result->return->result->messages->Message->description,
                    ['courier' => $this->courierName, 'class' => get_class($this), 'line' => __LINE__]
                );
                return ['status' => '500', 'error_code' => self::ERRORS['PROBLEM_IN_PLACE_ORDER']];
            }
            $waybill = $apaczka->getWaybillDocument($orderId);

            if ($waybill) {
                Storage::disk('local')->put('public/apaczka/stickers/sticker' . $orderId . '.pdf',
                    $waybill->return->waybillDocument);
            } else {
                Log::notice(
                    $waybill->return->result->messages,
                    ['courier' => $this->courierName, 'class' => get_class($this), 'line' => __LINE__]
                );
                return ['status' => '500', 'error_code' => self::ERRORS['PROBLEM_WITH_DOWNLOAD_WAYBILL']];
            }

            $result = $apaczka->getCollectiveTurnInCopyDocument($orderId);
            Storage::disk('local')->put('public/apaczka/protocols/protocol' . $date . '-' . $orderId . '.pdf',
                $result->return->turnInCopyDocument);
            dispatch_now(new CheckStatusInpostPackagesJob());
            return [
                'status' => 200,
                'error_code' => 0,
                'sending_number' => $orderId,
                'letter_number' => $waybill->return->waybillDocument
            ];
        } catch (Exception $exception) {
            Log::info(
                'Problem in Apaczka integration',
                ['courier' => $this->courierName, 'class' => get_class($this), 'line' => __LINE__]
            );
            return ['status' => '500', 'error_code' => self::ERRORS['PROBLEM_WITH_APACZKA_INTEGRATION']];
        }
    }


    /**
     * @return array
     */
    public function createPackageForPocztex()
    {

        $xml = '<tns:addShipment xmlns:tns="http://e-nadawca.poczta-polska.pl">';
        $integration = new \App\Integrations\Pocztex\ElektronicznyNadawca();
        $integration->clearEnvelope(new clearEnvelope());
        $package = new przesylkaPaletowaType();
        $xml .= '<przesylki';
        $xml .= ' zawartosc = "' . $this->data['content'] . '"';
        $shipment = new addShipment();

        $address = new adresType();

        $address->nazwa = $this->data['delivery_address']['firstname'];
        $address->nazwa2 = $this->data['delivery_address']['lastname'];
        $address->ulica = $this->data['delivery_address']['address'];
        $address->numerDomu = $this->data['delivery_address']['flat_number'];
        $address->miejscowosc = $this->data['delivery_address']['city'];
        $address->kodPocztowy = $this->data['delivery_address']['postal_code'];
        $address->telefon = $this->data['delivery_address']['phone'];
        $address->email = $this->data['delivery_address']['email'];
        $address->osobaKontaktowa = $this->data['delivery_address']['firstname'] . ' ' . $this->data['delivery_address']['lastname'];
        $xml .= '<adres';
        foreach ($address as $key => $value) {
            $xml .= ' ' . $key . ' = "' . $value . '';
        }
        $xml .= '/>';
        $xml .= '</przesylki>';
        $soapXML = new \SoapVar($xml, XSD_STRING);
        //   $integration->__call('addShipment', $soapXML);
        //     Log::debug($integration->__getLastRequest());

        $package->adres = $address;
        //      $package->miejsceDoreczenia = $address;
        $pickupAddress = new adresType();

        $pickupAddress->nazwa = $this->data['pickup_address']['firstname'];
        $pickupAddress->nazwa2 = $this->data['pickup_address']['lastname'];
        $pickupAddress->ulica = $this->data['pickup_address']['address'];
        $pickupAddress->numerDomu = $this->data['pickup_address']['flat_number'];
        $pickupAddress->miejscowosc = $this->data['pickup_address']['city'];
        $pickupAddress->kodPocztowy = $this->data['pickup_address']['postal_code'];
        $pickupAddress->telefon = $this->data['pickup_address']['phone'];
        $pickupAddress->email = $this->data['pickup_address']['email'];
        $pickupAddress->kraj = 'Polska';
        $pickupAddress->osobaKontaktowa = $this->data['pickup_address']['firstname'] . ' ' . $this->data['pickup_address']['lastname'];
        $package->adres = $pickupAddress;
        $package->miejsceOdbioru = $pickupAddress;

        $package->opis = $this->data['notices'];
        $package->zawartosc = $this->data['content'];
        $package->masa = $this->data['weight'];
        $package->dataZaladunku = $this->data['pickup_address']['parcel_date'];
        $package->dataDostawy = "2019-02-06";

        $package->wartosc = $this->data['amount'] * 1000;

        $package->powiadomienieNadawcy = $this->data['pickup_address']['phone'];
        $palette = new paletaType();

        switch ($this->data['additional_data']['package_type']) {
            case 'EUR':
                $palette->rodzajPalety = rodzajPaletyType::EUR;
                break;
            case 'POLPALETA':
                $palette->rodzajPalety = rodzajPaletyType::POLPALETA;
                break;
            case 'INNA':
                $palette->rodzajPalety = rodzajPaletyType::INNA;
                break;
        }


        $palette->szerokosc = $this->data['width'];
        $palette->dlugosc = $this->data['length'];
        $palette->wysokosc = $this->data['height'];
        $package->paleta = $palette;


        $package->platnik = new platnikType();
        $package->platnik->uiszczaOplate = "NADAWCA";

        if ($this->data['cash_on_delivery'] == true) {
            $package->pobranie = new pobranieType();
            $package->pobranie->kwotaPobrania = $this->data['price_for_cash_on_delivery'] * 100;
            $package->pobranie->nrb = $this->data['number_account_for_cash_on_delivery'];
            $package->pobranie->sposobPobrania = sposobPobraniaType::RACHUNEK_BANKOWY;
            $package->pobranie->tytulem = 'Zamowienie nr: ' . $this->data['order_id'];
        }

        $package->guid = $this->getGuid();

        $tag['guid'] = $this->getGuid();
        $tag['_'] = '';

        $dom = new \DOMDocument("1.0", "utf-8");
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $param = $dom->createElement('przesylki');
        $schema = $dom->createAttribute('xmlns:xsi');
        $schema->value = 'http://www.w3.org/2001/XMLSchema-instance';
        $type = $dom->createAttribute('xsi:type');
        $type->value = 'ns1:przesylkaPaletowaType';
        $masa = $dom->createAttribute('masa');
        $masa->value = $this->data['weight'] . '000';
        $wartosc = $dom->createAttribute('wartosc');
        $wartosc->value = $this->data['amount'] * 100;
        $opis = $dom->createAttribute('opis');
        $opis->value = $this->data['notices'];
        $zawartosc = $dom->createAttribute('zawartosc');
        $zawartosc->value = $this->data['content'];
        $powiadomienieNadawcy = $dom->createAttribute('powiadomienieNadawcy');
        $powiadomienieNadawcy->value = $this->data['pickup_address']['phone'];
        $dataZaladunku = $dom->createAttribute('dataZaladunku');
        $dataZaladunku->value = $this->data['pickup_address']['parcel_date'];
        $dataDostawy = $dom->createAttribute('dataDostawy');
        $dataDostawy->value = "2019-02-10";
        $guid = $dom->createAttribute('guid');
        $guid->value = $this->getGuid();
        $paletaElement = $dom->createElement('paleta');
        $rodzajPalety = $dom->createAttribute('rodzajPalety');
        $rodzajPalety->value = $this->data['additional_data']['package_type'];
        $szerokosc = $dom->createAttribute('szerokosc');
        $szerokosc->value = $this->data['width'];
        $wysokosc = $dom->createAttribute('wysokosc');
        $wysokosc->value = $this->data['height'];
        $dlugosc = $dom->createAttribute('dlugosc');
        $dlugosc->value = $this->data['length'];
        $platnik = $dom->createElement('platnik');
        $uiszczaOplate = $dom->createAttribute('uiszczaOplate');
        $uiszczaOplate->value = "NADAWCA";
        if ($this->data['cash_on_delivery'] == true) {
            $pobranie = $dom->createElement('pobranie');
            $kwotaPobrania = $dom->createAttribute('kwotaPobrania');
            $kwotaPobrania->value = $this->data['price_for_cash_on_delivery'] * 100;
            $nrb = $dom->createAttribute('nrb');
            $nrb->value = $this->data['number_account_for_cash_on_delivery'];
            $sposobPobrania = $dom->createAttribute('sposobPobrania');
            $sposobPobrania->value = 'RACHUNEK_BANKOWY';
            $tytulem = $dom->createAttribute('tytulem');
            $tytulem->value = 'Zamowienie nr: ' . $this->data['order_id'];
            $pobranie->appendChild($kwotaPobrania);
            $pobranie->appendChild($nrb);
            $pobranie->appendChild($sposobPobrania);
            $pobranie->appendChild($tytulem);
            $param->appendChild($pobranie);
        }
        $addressElement = $dom->createElement('miejsceDoreczenia');
        $nazwa = $dom->createAttribute('nazwa');
        $nazwa->value = $this->data['delivery_address']['firstname'];
        $nazwa2 = $dom->createAttribute('nazwa2');
        $nazwa2->value = $this->data['delivery_address']['lastname'];
        $ulica = $dom->createAttribute('ulica');
        $ulica->value = $this->data['delivery_address']['address'];
        $numerDomu = $dom->createAttribute('numerDomu');
        $numerDomu->value = $this->data['delivery_address']['flat_number'];
        $miejscowosc = $dom->createAttribute('miejscowosc');
        $miejscowosc->value = $this->data['delivery_address']['city'];
        $kodPocztowy = $dom->createAttribute('kodPocztowy');
        $kodPocztowy->value = $this->data['delivery_address']['postal_code'];
        $telefon = $dom->createAttribute('telefon');
        $telefon->value = $this->data['delivery_address']['phone'];
        $email = $dom->createAttribute('email');
        $email->value = $this->data['delivery_address']['email'];
        $kraj = $dom->createAttribute('kraj');
        $kraj->value = 'Polska';
        $osobaKontaktowa = $dom->createAttribute('osobaKontaktowa');
        $osobaKontaktowa->value = $this->data['pickup_address']['firstname'] . ' ' . $this->data['pickup_address']['lastname'];
        $osobaKontaktowa2 = $dom->createAttribute('osobaKontaktowa');
        $osobaKontaktowa2->value = $this->data['delivery_address']['firstname'] . ' ' . $this->data['delivery_address']['lastname'];

        $addressElement->appendChild($nazwa);
        $addressElement->appendChild($nazwa2);
        $addressElement->appendChild($ulica);
        $addressElement->appendChild($numerDomu);
        $addressElement->appendChild($miejscowosc);
        $addressElement->appendChild($kodPocztowy);
        $addressElement->appendChild($telefon);
        $addressElement->appendChild($email);
        $addressElement->appendChild($kraj);
        $addressElement->appendChild($osobaKontaktowa);

        $addressElement2 = $dom->createElement('adres');
        $addressElement2->appendChild($nazwa);
        $addressElement2->appendChild($nazwa2);
        $addressElement2->appendChild($ulica);
        $addressElement2->appendChild($numerDomu);
        $addressElement2->appendChild($miejscowosc);
        $addressElement2->appendChild($kodPocztowy);
        $addressElement2->appendChild($telefon);
        $addressElement2->appendChild($email);
        $addressElement2->appendChild($kraj);
        $addressElement2->appendChild($osobaKontaktowa2);


        $miejsceOdbioruElement = $dom->createElement('miejsceOdbioru');
        $nazwa = $dom->createAttribute('nazwa');
        $nazwa->value = $this->data['pickup_address']['firstname'];
        $nazwa2 = $dom->createAttribute('nazwa2');
        $nazwa2->value = $this->data['pickup_address']['lastname'];
        $ulica = $dom->createAttribute('ulica');
        $ulica->value = $this->data['pickup_address']['address'];
        $numerDomu = $dom->createAttribute('numerDomu');
        $numerDomu->value = $this->data['pickup_address']['flat_number'];
        $miejscowosc = $dom->createAttribute('miejscowosc');
        $miejscowosc->value = $this->data['pickup_address']['city'];
        $kodPocztowy = $dom->createAttribute('kodPocztowy');
        $kodPocztowy->value = $this->data['pickup_address']['postal_code'];
        $telefon = $dom->createAttribute('telefon');
        $telefon->value = $this->data['pickup_address']['phone'];
        $email = $dom->createAttribute('email');
        $email->value = $this->data['pickup_address']['email'];
        $kraj = $dom->createAttribute('kraj');
        $kraj->value = 'Polska';
        $osobaKontaktowa = $dom->createAttribute('osobaKontaktowa');
        $osobaKontaktowa->value = $this->data['pickup_address']['firstname'] . ' ' . $this->data['pickup_address']['lastname'];


        $miejsceOdbioruElement->appendChild($nazwa);
        $miejsceOdbioruElement->appendChild($nazwa2);
        $miejsceOdbioruElement->appendChild($ulica);
        $miejsceOdbioruElement->appendChild($numerDomu);
        $miejsceOdbioruElement->appendChild($miejscowosc);
        $miejsceOdbioruElement->appendChild($kodPocztowy);
        $miejsceOdbioruElement->appendChild($telefon);
        $miejsceOdbioruElement->appendChild($email);
        $miejsceOdbioruElement->appendChild($kraj);
        $miejsceOdbioruElement->appendChild($osobaKontaktowa);

        $paletaElement->appendChild($rodzajPalety);
        $paletaElement->appendChild($szerokosc);
        $paletaElement->appendChild($wysokosc);
        $paletaElement->appendChild($dlugosc);
        $platnik->appendChild($uiszczaOplate);
        $param->appendChild($schema);
        $param->appendChild($powiadomienieNadawcy);
        $param->appendChild($dataDostawy);
        $param->appendChild($dataZaladunku);
        $param->appendChild($zawartosc);
        $param->appendChild($opis);
        $param->appendChild($wartosc);
        $param->appendChild($masa);
        $param->appendChild($guid);
        $param->appendChild($type);
        //     $param->appendChild($addressElement);
        $param->appendChild($miejsceOdbioruElement);
        $param->appendChild($addressElement2);
        $param->appendChild($paletaElement);
        $param->appendChild($platnik);
        $dom->appendChild($param);
        $shipment->przesylki[] = new \SoapVar($dom->saveXML($dom->documentElement), XSD_ANYXML);

        //    $shipment->przesylki[] = new \SoapVar($tag, SOAP_ENC_OBJECT, '');
        $sendingNumber = $integration->addShipment($shipment);

        $eSender = new \App\Integrations\Pocztex\ElektronicznyNadawca();
        $send = new sendEnvelope();
        $idSend = $eSender->sendEnvelope($send);
        $param = new \App\Integrations\Pocztex\getAddresLabelCompact();
        $param->idEnvelope = $idSend->idEnvelope;
        $retval = $eSender->getAddresLabelCompact($param);
        if ($idSend->idEnvelope !== false) {
            Storage::disk('local')->put('public/pocztex/protocols/protocol' . $idSend->idEnvelope . '.pdf',
                $retval->pdfContent);
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile(storage_path() . '/app/public/pocztex/protocols/protocol' . $idSend->idEnvelope . '.pdf');
            $text = $pdf->getText();
            preg_match('/(?:(?<!\d)\d{20}(?!\d))/', $text, $matches);
            $letter_number = $matches[0];
        } else {
            Session::put('message', $idSend);
            Session::put('message', $retval);
            Log::info(
                'Problem in Pocztex integration',
                ['courier' => $this->courierName, 'class' => get_class($this), 'line' => __LINE__]
            );
            die();
        }
        return [
            'status' => 200,
            'error_code' => 0,
            'sending_number' => $idSend->idEnvelope,
            'letter_number' => $letter_number,
        ];
    }


    public static function generateValidXmlFromObj($obj, $node_block = 'nodes', $node_name = 'node')
    {
        $arr = get_object_vars($obj);
        return self::generateValidXmlFromArray($arr, $node_block, $node_name);
    }

    public static function generateValidXmlFromArray($array, $node_block = 'nodes', $node_name = 'node')
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>';

        $xml .= '<' . $node_block . '>';
        $xml .= self::generateXmlFromArray($array, $node_name);
        $xml .= '</' . $node_block . '>';

        return $xml;
    }

    private static function generateXmlFromArray($array, $node_name)
    {
        $xml = '';

        if (is_array($array) || is_object($array)) {
            foreach ($array as $key => $value) {
                if (is_numeric($key)) {
                    $key = $node_name;
                }

                $xml .= '<' . $key . '>' . self::generateXmlFromArray($value, $node_name) . '</' . $key . '>';
            }
        } else {
            $xml = htmlspecialchars($array, ENT_QUOTES);
        }

        return $xml;
    }

    public function getGuid()
    {
        mt_srand((double)microtime() * 10000);
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $retval = substr($charid, 0, 32);
        return $retval;
    }

    public function createPackageForJas()
    {
        $integration = new Jas($this->config['jas'], $this->data);

        $userId = $integration->login();

        $contractorId = $integration->createNewContractor($userId, 'delivery');

        $warehouseId = $integration->createNewContractor($userId, 'pickup');

        $wayBillId = $integration->createWaybill($userId, $contractorId, $warehouseId);

        $integration->createNewCargo($userId, $wayBillId);

        $letterNumber = $integration->approveWayBill($userId, $wayBillId);

        if ((int)$letterNumber > 0) {
            $integration->getPdfLabels($userId, $wayBillId);
            $integration->getPdfLP($userId, $wayBillId);
            return [
                'status' => 200,
                'error_code' => 0,
                'sending_number' => $wayBillId,
                'letter_number' => $letterNumber,
            ];
        }
    }
}
