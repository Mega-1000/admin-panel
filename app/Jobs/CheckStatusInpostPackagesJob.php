<?php

namespace App\Jobs;

use App\Repositories\OrderPackageRepository;
use App\Integrations\Inpost\Inpost;
use Illuminate\Support\Facades\Log;
use App\Mail\SendLPToTheWarehouseAfterOrderCourierMail;
use Carbon\Carbon;
use App\Entities\OrderPackage;

class CheckStatusInpostPackagesJob extends Job
{
    protected $orderPackageRepository;

    const COURIER = 'INPOST';
    const COURIER2 = 'ALLEGRO-INPOST';

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
    public function handle(OrderPackageRepository $orderPackageRepository) {
        $orderPackages = array();
        $orderPackages[] = OrderPackage::where('delivery_courier_name', self::COURIER)
                ->where('shipment_date', '>', Carbon::today()->subDays(5))
                ->get();
        $orderPackages[] = OrderPackage::where('delivery_courier_name', self::COURIER2)
               ->where('shipment_date', '>', Carbon::today()->subDays(5))
               ->get();
        
        if (empty($orderPackages)) {
            return;
        }
        $integration = new Inpost();
        foreach ($orderPackages as $orderPackage) {
            if (is_null($orderPackage->inpost_url)) {
                continue;
            }
            if ($orderPackage->status !== 'DELIVERED' && $orderPackage->status !== 'SENDING' && $orderPackage->status !== 'WAITING_FOR_CANCELLED' && $orderPackage->status !== 'CANCELLED') {
                $href = $integration->hrefExecute($orderPackage->inpost_url);
                $orderPackage->letter_number = $href->tracking_number;
                $orderPackage->save();
                if ($href->status !== 'confirmed') {
                    continue;
                }
                $integration->getLabel($href->id, $href->tracking_number);
                $orderPackage->status = 'WAITING_FOR_SENDING';
                $orderPackage->save();
                if ($orderPackage->send_protocol == true) {
                    continue;
                }
                $path = storage_path('app/public/inpost/stickers/sticker' . $orderPackage->letter_number . '.pdf');
                if (is_null($path)) {
                    continue;
                }
                \Mailer::create()
                        ->to($orderPackage->order->warehouse->firm->email)
                        ->send(new SendLPToTheWarehouseAfterOrderCourierMail("List przewozowy przesyłki nr: " . $orderPackage->order->id . '/' . $orderPackage->number, $path, $orderPackage->order->id . '/' . $orderPackage->number));
                $orderPackage->send_protocol = true;
                $orderPackage->save();
            }
        }
    }

}
