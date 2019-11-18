<?php

namespace App\Jobs;

use App\Repositories\OrderPackageRepository;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendLPWithReminderSendingToWarehouseJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $orderPackages = $orderPackageRepository->findWhere(['shipment_date', '=', Carbon::today()])->all();
        foreach ($orderPackages as $orderPackage) {
            $pathSecond = null;
            if ($orderPackage->delivery_courier_name === 'INPOST') {
                $path = storage_path('app/public/inpost/stickers/sticker' . $orderPackage->letter_number . '.pdf');
            } elseif ($orderPackage->delivery_courier_name === 'DPD') {
                $path = storage_path('app/public/dpd/protocols/protocol' . $orderPackage->letter_number . '.pdf');
                $pathSecond = storage_path('app/public/dpd/stickers/sticker' . $orderPackage->letter_number . '.pdf');
            } elseif ($orderPackage->delivery_courier_name === 'JAS') {
                $path = storage_path('app/public/jas/protocols/protocol' . $orderPackage->letter_number . '.pdf');
            } elseif ($orderPackage->delivery_courier_name === 'POCZTEX') {
                $path = storage_path('app/public/pocztex/protocols/protocol' . $orderPackage->sending_number . '.pdf');
            } elseif ($orderPackage->delivery_courier_name === 'GIELDA' || $orderPackage->delivery_courier_name === 'ODBIOR_OSOBISTY') {
                break;
            }
            if ($path !== null) {
                dispatch(new OrderStatusChangedToDispatchNotificationJob($orderPackage->order->id, null,
                    $path, null, $pathSecond));
            }
        }
    }
}
