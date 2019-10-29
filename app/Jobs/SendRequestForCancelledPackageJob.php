<?php

namespace App\Jobs;

use App\Repositories\FirmRepository;
use App\Repositories\OrderPackageRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Mail\SendRequestForCancelledPackageMail;

class SendRequestForCancelledPackageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(OrderPackageRepository $orderPackageRepository, FirmRepository $firmRepository)
    {
        $package = $orderPackageRepository->find($this->id);
        switch ($package->service_courier_name) {
            case 'INPOST':
                $firm = $firmRepository->findByField('symbol', 'INPOST')->first();
                $email = $firm->email;
                break;
            case 'DPD':
                $firm = $firmRepository->findByField('symbol', 'DPD')->first();
                $email = $firm->email;
                break;
            case 'APACZKA':
                $firm = $firmRepository->findByField('symbol', 'APACZKA')->first();
                $email = $firm->email;
                break;
            case 'POCZTEX':
                $firm = $firmRepository->findByField('symbol', 'POCZTEX')->first();
                $email = $firm->email;
                break;
            case 'JAS':
                $firm = $firmRepository->findByField('symbol', 'JASBFG')->first();
                $email = $firm->email;
                break;
            default:
                Log::info(
                    'Problem in request for cancelled package',
                    ['service_courier_name' => 'is empty', 'class' => get_class($this), 'line' => __LINE__]
                );
                die();
        }

        $url = env('APP_URL') . '/api/order-shipping-cancelled/' . $package->id;

        \Mailer::create()
            ->to($email)
            ->send(new SendRequestForCancelledPackageMail("Prośba o anulację nadania paczki", $package->sending_number,
                $url));
    }
}
