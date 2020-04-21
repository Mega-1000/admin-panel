<?php

namespace App\Jobs;

use App\Repositories\OrderPackageRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Integrations\Jas\Jas;

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
    public function __construct(OrderPackageRepository $orderPackageRepository)
    {
        $this->config = config('integrations');
        $this->orderPackageRepository = $orderPackageRepository;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $packages = $this->orderPackageRepository->findWhere([['shipment_date', '>', Carbon::today()->subDays(7)]]);
        foreach ($packages as $package) {
            if ($package->letter_number !== null) {
                switch ($package->delivery_courier_name) {
                    case 'INPOST' :
                        $this->checkStatusInInpostPackages($package);
                        break;
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

        if (in_array($result['items'][0]['status'], $statusDelivered)) {
            $package->status = 'DELIVERED';
            $package->update();

        }
        if (in_array($result['items'][0]['status'], $status)) {
            $package->status = 'SENDING';
            $package->update();

        }
    }

    /**
     * @param $package
     */
    protected function checkStatusInDpdPackages($package)
    {
        $url = $this->config['dpd']['tracking_url'];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"kurier\"\r\n\r\ndpd\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"nr\"\r\n\r\n" . $package->letter_number . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
            CURLOPT_HTTPHEADER => array(
                "Postman-Token: 23ef4480-e85e-4dfe-8863-924e7474ff7d",
                "cache-control: no-cache",
                "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            Log::info("cURL Error #:" . $err);
        } else {
            $result = preg_match('/Przesyłka odebrana przez Kuriera/', $response);
            if ($result == '1') {
                $package->status = 'SENDING';
                $package->update();
            }
        }
    }

    /**
     * @param $package
     */
    protected function checkStatusInPocztexPackages($package)
    {
        $url = $this->config['pocztex']['tracking_url'] . $package->letter_number;

        $guzzle = new \GuzzleHttp\Client;

        $response = $guzzle->get($url, [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; …) Gecko/20100101 Firefox/65.0'
            ]
        ]);
        $result = json_decode((string)$response->getBody(), true);

        if (isset($result[0]['zdarzenia'])) {
            foreach ($result[0]['zdarzenia'] as $item) {
                if ($item['nazwa'] == 'Wysłanie z ładunkiem' || $item['nazwa'] == 'Nadanie') {
                    $package->status = 'SENDING';
                    $package->update();
                } else {
                    if ($item['nazwa'] == 'Doręczenie') {
                        $package->status = 'DELIVERED';
                        $package->update();
                    }
                }
            }
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
            $package->update();
        } else {
            if (preg_match('/TRANSPORT/', $status, $matches) || preg_match('/MAGAZYN/', $status,
                    $matches) || $status == 'TRANSPORT' || $status == 'MAGAZYN') {
                $package->status = 'SENDING';
                $package->update();
            }
        }
    }
}
