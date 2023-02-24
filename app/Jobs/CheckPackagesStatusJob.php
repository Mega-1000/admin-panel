<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Entities\Label;
use App\Entities\Order;
use App\Entities\OrderPackage;
use App\Enums\CourierName;
use App\Enums\CourierStatus\DpdPackageStatus;
use App\Enums\CourierStatus\GlsPackageStatus;
use App\Enums\CourierStatus\InpostPackageStatus;
use App\Enums\PackageStatus;
use App\Exceptions\SoapException;
use App\Exceptions\SoapParamsException;
use App\Integrations\Jas\Jas;
use App\Integrations\Pocztex\ElektronicznyNadawca;
use App\Integrations\Pocztex\envelopeStatusType;
use App\Integrations\Pocztex\getEnvelopeContentShort;
use App\Integrations\Pocztex\getEnvelopeStatus;
use App\Integrations\Pocztex\statusType;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use OutOfRangeException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

/**
 * Class CheckPackagesStatusJob
 *
 * @package App\Jobs
 */
class CheckPackagesStatusJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $config;

    protected Client $httpClient;

    protected ?int $userId;

    public function __construct()
    {
        $this->userId = Auth::user()?->id;
        $this->config = config('integrations');
        $options = [];
        if (config('app.env') == 'local') {
            $options['proxy'] = 'http://localhost:2180';
        }
        $this->httpClient = new Client($options);
    }

    public function handle(): void
    {
        if(Auth::user() === null && $this->userId !== null) {
            Auth::loginUsingId($this->userId);
        }

        $orders = Order::whereHas('packages', function ($query) {
            $query->whereIn('status', ['SENDING', 'WAITING_FOR_SENDING'])->whereDate('shipment_date', '>', Carbon::today()->subDays(30)->toDateString());
        })->get();

        foreach ($orders as $order) {
            try {
                foreach ($order->packages as $package) {
                    if ($package->status == PackageStatus::DELIVERED ||
                        $package->status == PackageStatus::WAITING_FOR_CANCELLED ||
                        $package->status == PackageStatus::CANCELLED ||
                        $package->status == PackageStatus::REJECT_CANCELLED ||
                        empty($package->letter_number)) {
                        continue;
                    }

                    switch ($package->service_courier_name) {
                        case CourierName::INPOST :
                        case CourierName::ALLEGRO_INPOST :
                            $this->checkStatusInInpostPackages($package);
                            break;
                        case CourierName::DPD:
                            $this->checkStatusInDpdPackages($package);
                            break;
                        case CourierName::APACZKA:
                            $this->checkStatusInApaczkaPackages($package);
                            break;
                        case CourierName::POCZTEX:
                            $this->checkStatusInPocztexPackages($package);
                            break;
                        case CourierName::JAS:
                            $this->checkStatusInJasPackages($package);
                            break;
                        case CourierName::GLS:
                            $this->checkStatusInGlsPackages($package);
                            break;
                        case CourierName::DB_SCHENKER:
                            $this->checkStatusInSchenkerPackages($package);
                            break;
                        default:
                            break;
                    }
                }

                if (!$order->packages()->whereIn('status', [PackageStatus::SENDING, PackageStatus::WAITING_FOR_SENDING])->count()) {
                    dispatch(new RemoveLabelJob($order, [Label::BLUE_BATTERY_LABEL_ID]));
                }
            } catch (Throwable $ex) {
                Log::error($ex->getMessage());
                continue;
            }
        }
    }

    protected function checkStatusInInpostPackages(OrderPackage $package): void
    {
        $url = $this->config['inpost']['tracking_url'] . $package->letter_number;
        $params = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->config['inpost']['authorization']
            ],
        ];

        $response = $this->prepareConnectionForTrackingStatus($url, Request::METHOD_GET, $params);
        $result = json_decode((string)$response->getBody(), true);

        if (!isset($result['items'][0])) {
            Log::info('Something went wrong with package:', ['order_id' => $package->order_id, 'package_id' => $package->id]);
            return;
        }

        try {
            $packageStatus = $result['items'][0]['status'];
            switch (true) {
                case in_array($packageStatus, InpostPackageStatus::DELIVERED):
                    $package->status = PackageStatus::DELIVERED;
                    break;
                case in_array($packageStatus, InpostPackageStatus::SENDING):
                    $package->status = PackageStatus::SENDING;
                    break;
                case in_array($packageStatus, InpostPackageStatus::WAITING_FOR_SENDING):
                    $package->status = PackageStatus::WAITING_FOR_SENDING;
                    break;
            }
            $package->save();
        } catch (OutOfRangeException $e) {
            Log::info('Something went wrong with package:', ['order_id' => $package->order_id, 'package_id' => $package->id]);
        }
    }

    private function prepareConnectionForTrackingStatus(string $url, string $method, array $params): ResponseInterface
    {
        $curlSettings = ['curl' => [
            CURLOPT_SSL_CIPHER_LIST => 'DEFAULT@SECLEVEL=1',
            CURLOPT_USERAGENT => 'Mozilla Chrome Safari'
        ]];
        $params = array_merge($params, $curlSettings);

        return $this->httpClient->request($method, $url, $params);
    }

    protected function checkStatusInDpdPackages(OrderPackage $package): void
    {
        $params = [
            'q' => $package->letter_number,
            'typ' => 1
        ];
        $options = ['form_params' => $params];
        $response = $this->prepareConnectionForTrackingStatus($this->config['dpd']['tracking_url'], Request::METHOD_POST, $options)->getBody()->getContents();

        $packageStatusRegex = '/(' . DpdPackageStatus::getDescription(DpdPackageStatus::DELIVERED)
            . ')|(' . DpdPackageStatus::getDescription(DpdPackageStatus::SENDING)
            . ')|(' . DpdPackageStatus::getDescription(DpdPackageStatus::INDELIVERY)
            . ')|(' . DpdPackageStatus::getDescription(DpdPackageStatus::INWAREHOUSE)
            . ')|(' . DpdPackageStatus::getDescription(DpdPackageStatus::WAITING_FOR_SENDING) . ')/';

        preg_match($packageStatusRegex, $response, $matches);

        if (!empty($matches)) {
            switch ($matches[0]) {
                case DpdPackageStatus::getDescription(DpdPackageStatus::DELIVERED):
                    $package->status = PackageStatus::DELIVERED;
                    break;
                case DpdPackageStatus::getDescription(DpdPackageStatus::SENDING):
                case DpdPackageStatus::getDescription(DpdPackageStatus::INDELIVERY):
                case DpdPackageStatus::getDescription(DpdPackageStatus::INWAREHOUSE):
                    $package->status = PackageStatus::SENDING;
                    break;
                case DpdPackageStatus::getDescription(DpdPackageStatus::WAITING_FOR_SENDING):
                    $package->status = PackageStatus::WAITING_FOR_SENDING;
                    break;
            }

            $package->save();
        }
    }

    //@todo apaczka checking status

    /**
     * @param $package
     */
    protected function checkStatusInApaczkaPackages($package)
    {

    }

    /**
     * @param $package
     */
    protected function checkStatusInPocztexPackages($package)
    {
        $integration = new ElektronicznyNadawca();
        $request = new getEnvelopeContentShort();
        $request->idEnvelope = $package->sending_number;
        $status = $integration->getEnvelopeContentShort($request);

        if (!$status || !isset($status->przesylka) || $status->przesylka->status !== statusType::POTWIERDZONA) {
            Log::notice('Błąd ze statusem przesyłki ' . $package->order_id,
                (array)$status->przesylka
            );
            return;
        }

        $request = new getEnvelopeStatus();
        $request->idEnvelope = $package->sending_number;
        $status = $integration->getEnvelopeStatus($request);

        switch ($status->envelopeStatus) {
            case envelopeStatusType::PRZYJETY:
                $package->status = PackageStatus::DELIVERED;
                break;
            case envelopeStatusType::WYSLANY:
                $package->status = PackageStatus::SENDING;
                break;
        }
        if ($package->isDirty()) {
            $package->save();
        }
    }

    /**
     * @param $package
     */
    protected function checkStatusInJasPackages($package)
    {
        $integration = new Jas($this->config['jas']);

        $userId = $integration->login();
        if ($userId === false) {
            return;
        }
        $status = $integration->getPackageStatus($userId, $package->letter_number);

        if (preg_match('/DOSTARCZONO/', $status, $matches) || preg_match('/ZAKOŃCZONO/', $status,
                $matches) || $status == 'DOSTARCZONO' || $status == 'ZAKOŃCZONO') {
            $package->status = 'DELIVERED';
            $package->save();
        } else {
            if (preg_match('/TRANSPORT/', $status, $matches) || preg_match('/MAGAZYN/', $status,
                    $matches) || $status == 'TRANSPORT' || $status == 'MAGAZYN') {
                $package->status = 'SENDING';
                $package->save();
            }
        }
    }

    private function checkStatusInGlsPackages(OrderPackage $package): void
    {
        $url = $this->config['gls']['tracking_url'] . $package->letter_number;
        try {
            $response = json_decode($this->prepareConnectionForTrackingStatus($url, Request::METHOD_GET, [])->getBody()->getContents());

            if (empty($response->tuStatus)) {
                Log::notice('Wystąpił problem przy sprawdzaniu statusu paczki: ' . $package->letter_number);
                return;
            }

            $packageStatus = $response->tuStatus[0]->progressBar->statusInfo;

            switch ($packageStatus) {
                case GlsPackageStatus::DELIVERED:
                    $package->status = PackageStatus::DELIVERED;
                    break;
                case GlsPackageStatus::INTRANSIT:
                case GlsPackageStatus::INWAREHOUSE:
                case GlsPackageStatus::INDELIVERY:
                    $package->status = PackageStatus::SENDING;
                    break;
                case GlsPackageStatus::PREADVICE:
                    $package->status = PackageStatus::WAITING_FOR_SENDING;
                    break;
            }

            $package->save();
        } catch (RequestException $e) {
            Log::error('Wystąpił problem przy sprawdzaniu statusu paczki: ' . $package->letter_number . ' Błąd: ' . $e->getMessage());
        }
    }

    /**
     * @throws SoapParamsException
     * @throws SoapException
     */
    private function checkStatusInSchenkerPackages(OrderPackage $orderPackage)
    {
//        if ($orderPackage->status !== PackageStatus::WAITING_FOR_SENDING) {
////            $trackingInformationRequest = new GetTrackingRequestDTO($orderPackage->letter_number);
////            $getTrackingInformationResponse = SchenkerService::getGetTrackingInformation($trackingInformationRequest);
//        }

    }
}
