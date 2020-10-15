<?php

namespace App\Jobs;

use App\Integrations\Pocztex\ElektronicznyNadawca;
use App\Integrations\Pocztex\envelopeStatusType;
use App\Integrations\Pocztex\getEnvelopeContentShort;
use App\Integrations\Pocztex\getEnvelopeStatus;
use App\Integrations\Pocztex\statusType;
use App\Repositories\OrderPackageRepository;
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

/**
 * Class CheckPackagesStatusJob
 * @package App\Jobs
 */
class CheckPackagesStatusJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    protected $config;

    /**
     * @var OrderPackageRepository
     */
    protected $orderPackageRepository;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->config = config('integrations');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $packages = OrderPackage::whereDate('shipment_date', '>', Carbon::today()->subDays(7)->toDateString())->get();
        foreach ($packages as $package) {
            if (empty($package->letter_number)) {
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
                default:
                    break;
            }
        }
    }

    /**
     * @param $package
     */
    protected function checkStatusInInpostPackages($package)
    {
        $status = [
            'dispatched_by_sender',
            'collected_from_sender',
            'taken_by_courier',
            'adopted_at_source_branch',
            'sent_from_source_branch',
            'ready_to_pickup_from_pok',
            'ready_to_pickup_from_pok_registered',
            'adopted_at_sorting_center',
            'sent_from_sorting_center',
            'adopted_at_target_branch',
            'out_for_delivery',
            'ready_to_pickup',
            'pickup_reminder_sent',
            'pickup_time_expired',
            'dispatched_by_sender_to_pok',
            'pickup_reminder_sent_address',
            'taken_by_courier_from_pok',
            'redirect_to_box',
            'stack_parcel_pickup_time_expired',
            'unstack_from_customer_service_point',
            'courier_avizo_in_customer_service_point',
            'out_for_delivery_to_address',
        ];
        $statusDelivered = [
            'delivered',
        ];
        $url = $this->config['inpost']['tracking_url'] . $package->letter_number;

        $guzzle = new \GuzzleHttp\Client;


        $response = $guzzle->get($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->config['inpost']['authorization']
            ],
        ]);

        $result = json_decode((string)$response->getBody(), true);

        if (! isset($result['items'][0])) {
            Log::info('Something went wrong with package:', ['order_id' => $package->order_id, 'package_id' => $package->id]);
            return;
        }
        try {
            if (in_array($result['items'][0]['status'], $statusDelivered)) {
                $package->status = 'DELIVERED';
                $package->save();
            }
            if (in_array($result['items'][0]['status'], $status)) {
                $package->status = 'SENDING';
                $package->save();
            }
        } catch(OutOfRangeException $e) {
            Log::info('Something went wrong with package:', ['order_id' => $package->order_id, 'package_id' => $package->id]);
        }
    }

    /**
     * @param $package
     */
    protected function checkStatusInDpdPackages($package)
    {
        $url = $this->config['dpd']['tracking_url'];

        $guzzle = new \GuzzleHttp\Client(["base_uri" => $url]);
        $params = [
            'q' => $package->letter_number,
            'typ' => 1
        ];
        $options = ['form_params' => $params];
        $response = $guzzle->post('', $options)->getBody()->getContents();

        $result = preg_match('/Przesyłka doręczona/', $response);
        if ($result == '1') {
            $package->status = 'DELIVERED';
            $package->save();
        } else {
            $result = preg_match('/Przesyłka odebrana przez Kuriera/', $response);
            if ($result == '1') {
                $package->status = 'SENDING';
                $package->save();
            }
        }
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

    private function checkStatusInGlsPackages($package)
    {
        $guzzle = new Client();
        $res = $guzzle->get('http://statusy.gls-poland.com.pl/last.php?nr_paczki=' . $package->letter_number);
        $body = (string)$res->getBody();


        switch(true) {
            case str_contains($body, 'Doreczona'):
                $package->status = OrderPackage::DELIVERED;
                break;
            case str_contains($body, 'Paczka w magazynie') ||
                 str_contains($body, 'W doreczeniu') ||
                 str_contains($body, 'Skan kontrolny KK'):
                $package->status = OrderPackage::SENDING;
                break;
            case str_contains($body, 'Dane od KK otrzymane'):
                $package->status = OrderPackage::WAITING_FOR_SENDING;
                break;
        }

        if ($package->isDirty()) {
            $package->save();
        }
    }
}
