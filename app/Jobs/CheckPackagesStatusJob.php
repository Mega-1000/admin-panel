<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Enums\CourierStatus\DpdPackageStatus;
use App\Enums\CourierStatus\GlsPackageStatus;
use App\Enums\CourierStatus\InpostPackageStatus;
use App\Integrations\Pocztex\ElektronicznyNadawca;
use App\Integrations\Pocztex\envelopeStatusType;
use App\Integrations\Pocztex\getEnvelopeContentShort;
use App\Integrations\Pocztex\getEnvelopeStatus;
use App\Integrations\Pocztex\statusType;
use App\Repositories\OrderPackageRepository;
use App\Repositories\OrderRepository;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Integrations\Jas\Jas;
use App\Entities\OrderPackage;
use OutOfRangeException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class CheckPackagesStatusJob
 * @package App\Jobs
 */
class CheckPackagesStatusJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const DpdPackageStatusRegex = '/(Przesyłka doręczona)|(Przesyłka odebrana przez Kuriera)|(Zarejestrowano dane przesyłki)/';
    private const GlsPackageStatusRegex = '/(Paczka doreczona)|(Paczka zarejestrowana w filii GLS)|(Nadawca nadal numer paczce)/';

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    protected $config;

    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->config = config('integrations');
        $this->orderRepository = $orderRepository;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $orders = Order::whereDate('shipment_date', '>', Carbon::today()->subDays(7)->toDateString())->get();

        foreach($orders as $order) {
            foreach ($order->packages as $package) {
                if ($package->status == 'DELIVERED' || empty($package->letter_number)) {
                    continue;
                }
                switch ($package->service_courier_name) {
                    case 'INPOST' :
                    case 'ALLEGRO-INPOST' :
                        $this->checkStatusInInpostPackages($package);
                        break;
                    case 'DPD':
                        $this->checkStatusInDpdPackages($package);
                        break;
                    case 'APACZKA':
                        $this->checkStatusInApaczkaPackages($package);
                        break;
                    case 'POCZTEX':
                        $this->checkStatusInPocztexPackages($package);
                        break;
                    case 'JAS':
                        $this->checkStatusInJasPackages($package);
                        break;
                    case 'GLS':
                        $this->checkStatusInGlsPackages($package);
                        break;
                    default:
                        break;
                }
            }
        }
    }

    /**
     * @param $package
     */
    protected function checkStatusInInpostPackages($package)
    {
        $url = $this->config['inpost']['tracking_url'] . $package->letter_number;
        $params = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->config['inpost']['authorization']
            ],
        ];

        $response = $this->prepareConnectionForTrackingStatus($url, 'GET', $params);
        $result = json_decode((string)$response->getBody(), true);

        if (!isset($result['items'][0])) {
            Log::info('Something went wrong with package:', ['order_id' => $package->order_id, 'package_id' => $package->id]);
            return;
        }

        try {
            $packageStatus = $result['items'][0]['status'];
            switch(true) {
                case in_array($packageStatus, InpostPackageStatus::DELIVERED):
                    $package->status = OrderPackage::DELIVERED;
                    break;
                case in_array($packageStatus, InpostPackageStatus::SENDING):
                    $package->status = OrderPackage::SENDING;
                    break;
                case in_array($packageStatus, InpostPackageStatus::WAITING_FOR_SENDING):
                    $package->status = OrderPackage::WAITING_FOR_SENDING;
                    break;
            }
            $package->save();
        } catch(OutOfRangeException $e) {
            Log::info('Something went wrong with package:', ['order_id' => $package->order_id, 'package_id' => $package->id]);
        }
    }

    protected function checkStatusInDpdPackages(OrderPackage $package): void
    {
        $params = [
            'q' => $package->letter_number,
            'typ' => 1
        ];
        $options = ['form_params' => $params];
        $response = $this->prepareConnectionForTrackingStatus($this->config['dpd']['tracking_url'], 'POST', $options)->getBody()->getContents();

        preg_match(self::DpdPackageStatusRegex, $response, $matches);

        switch($matches[0]) {
            case DpdPackageStatus::DELIVERED:
                $package->status = OrderPackage::DELIVERED;
                break;
            case DpdPackageStatus::SENDING:
                $package->status = OrderPackage::SENDING;
                break;
            case DpdPackageStatus::WAITING_FOR_SENDING:
                $package->status = OrderPackage::WAITING_FOR_SENDING;
                break;
        }

        $package->save();
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
        if ($status->przesylka->status !== statusType::POTWIERDZONA) {
            return;
        }

        $request = new getEnvelopeStatus();
        $request->idEnvelope = $package->sending_number;
        $status = $integration->getEnvelopeStatus($request);
        switch ($status->envelopeStatus) {
            case envelopeStatusType::DOSTARCZONY:
                $package->status = OrderPackage::DELIVERED;
                break;
            case envelopeStatusType::PRZYJETY:
                $package->status = OrderPackage::SENDING;
                break;
        }
        if ($package->isDirty()) {
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
        $response = $this->prepareConnectionForTrackingStatus($url, 'GET', [])->getBody()->getContents();

        preg_match(self::GlsPackageStatusRegex, $response, $matches);

        switch($matches[0]) {
            case GlsPackageStatus::DELIVERED:
                $package->status = OrderPackage::DELIVERED;
                break;
            case GlsPackageStatus::SENDING:
                $package->status = OrderPackage::SENDING;
                break;
            case GlsPackageStatus::WAITING_FOR_SENDING:
                $package->status = OrderPackage::WAITING_FOR_SENDING;
                break;
        }

        $package->save();
    }

    private function prepareConnectionForTrackingStatus(string $url, string $method, array $params): ResponseInterface
    {
        $guzzle = new Client;
        return $guzzle->request($method, $url, $params);
    }
}
