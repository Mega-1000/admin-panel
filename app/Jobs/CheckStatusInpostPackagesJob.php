<?php

namespace App\Jobs;

use App\Repositories\OrderPackageRepository;
use App\Integrations\Inpost\Inpost;
use Illuminate\Support\Facades\Log;
use App\Mail\SendLPToTheWarehouseAfterOrderCourierMail;
use Carbon\Carbon;

class CheckStatusInpostPackagesJob extends Job
{
    protected $orderPackageRepository;

    const COURIER = 'INPOST';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(OrderPackageRepository $orderPackageRepository)
    {
        $this->orderPackageRepository = $orderPackageRepository;

        $orderPackages = $this->orderPackageRepository->findWhere([
            ['delivery_courier_name', '=', self::COURIER],
            ['shipment_date', '>', Carbon::today()->subDays(5)]
        ])->all();
        if (!empty($orderPackages)) {
            $integration = new Inpost();
            foreach ($orderPackages as $orderPackage) {
                if ($orderPackage->inpost_url !== null) {
                    if ($orderPackage->status !== 'DELIVERED' && $orderPackage->status !== 'SENDING') {
                        $href = $integration->hrefExecute($orderPackage->inpost_url);
                        $this->orderPackageRepository->update(['letter_number' => $href->tracking_number],
                            $orderPackage->id);
                        if ($href->status === 'confirmed') {
                            $integration->getLabel($href->id, $href->tracking_number);
                            $this->orderPackageRepository->update(['status' => 'WAITING_FOR_SENDING'],
                                $orderPackage->id);
                            if($orderPackage->send_protocol == false) {
                                $path = storage_path('app/public/inpost/stickers/sticker' . $orderPackage->letter_number . '.pdf');
                                if ($path !== null) {
                                    \Mailer::create()
                                        ->to($orderPackage->order->warehouse->firm->email)
                                        ->send(new SendLPToTheWarehouseAfterOrderCourierMail("List przewozowy przesyłki nr: " . $orderPackage->order->id . '/' . $orderPackage->number,
                                            $path, $orderPackage->order->id . '/' . $orderPackage->number));
                                    $this->orderPackageRepository->update(['send_protocol' => true],
                                        $orderPackage->id);
                                }
                            }

                        }
                    }
                }
            }
        }
    }
}
