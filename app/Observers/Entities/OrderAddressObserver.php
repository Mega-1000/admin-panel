<?php

namespace App\Observers\Entities;

use App\Entities\Order;
use App\Entities\OrderAddress;
use App\Helpers\LabelsHelper;
use App\Jobs\AddLabelJob;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Jobs\RemoveLabelJob;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use App\Services\OrderAddressService;
use App\Services\OrderPaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class OrderAddressObserver
{
    public function creating(OrderAddress $address)
    {
        app(OrderAddressService::class)->preSaveCleanup($address);
    }

    public function updating(OrderAddress $address)
    {
        app(OrderAddressService::class)->preSaveCleanup($address);
    }

    public function created(OrderAddress $orderAddress)
    {
        $this->removingMissingDeliveryAddressLabelHandler($orderAddress);
    }

    protected function removingMissingDeliveryAddressLabelHandler(OrderAddress $orderAddress)
    {
        $hasMissingDeliveryAddressLabel = $orderAddress->order->labels()->where('label_id', 75)->get();    //brak danych do dostawy
        if (count($hasMissingDeliveryAddressLabel) > 0) {
            if ($orderAddress->type == "DELIVERY_ADDRESS") {
                if ($orderAddress->order->isDeliveryDataComplete()) {
                    dispatch_now(new DispatchLabelEventByNameJob($orderAddress->order, "added-delivery-address"));
                }
            }
        }
    }

    public function updated(OrderAddress $orderAddress)
    {
        $this->removingMissingDeliveryAddressLabelHandler($orderAddress);
        $this->addLabelIfManualCheckIsRequired($orderAddress);

        if ($orderAddress->wasChanged() && $orderAddress->order->proforma_filename && Storage::disk('local')->exists($orderAddress->order->proformStoragePath)) {
            Storage::disk('local')->delete($orderAddress->order->proformStoragePath);
            $orderAddress->order->proforma_filename = '';
            $orderAddress->order->save();
        }

        $this->addHistoryLog($orderAddress);
    }

    protected function addLabelIfManualCheckIsRequired(OrderAddress $orderAddress): void
    {
        $loopPresentationArray = [];
        if (app(OrderPaymentService::class)->hasAnyPayment($orderAddress->order) &&
            !(new OrderAddressService())->addressIsValid($orderAddress)) {
            AddLabelService::addLabels($orderAddress->order, [LabelsHelper::INVALID_ORDER_ADDRESS], $loopPresentationArray, [], Auth::user()->id);
            return;
        }
        RemoveLabelService::removeLabels($orderAddress->order, [LabelsHelper::INVALID_ORDER_ADDRESS], $loopPresentationArray, [], Auth::user()->id);
    }

    protected function addHistoryLog(OrderAddress $orderAddress): void
    {
        $type = 'api';
        if (Route::currentRouteName() != 'api.orders.update-order-delivery-and-invoice-addresses') {
            return;
        }

        $original = $orderAddress->getOriginal();

        $changes = $orderAddress->getChanges();

        $original = array_intersect_key($original, $changes);
        $changeLog = [];

        foreach ($changes as $field => $new_value) {
            if ($field == 'updated_at' || $new_value == $original[$field]) {
                continue;
            }

            $changeLog[] = __("customers.table.{$field}") . ": z `{$original[$field]}` na `{$new_value}`";
        }

        if ($changeLog) {
            $messageTitle = sprintf(__('order_addresses.message.title.' . $type),
                __('customers.form.buttons.' . strtolower($orderAddress->type))
            );

            $orderAddress->order->labels_log .= Order::formatMessage(null, $messageTitle . implode(', ', $changeLog));
            $orderAddress->order->save();
        }
    }
}
