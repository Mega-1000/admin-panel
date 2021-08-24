<?php

namespace App\Observers\Entities;

use App\Entities\Order;
use App\Entities\OrderAddress;
use App\Jobs\AddLabelJob;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Jobs\RemoveLabelJob;
use App\Services\OrderAddressService;
use App\Services\OrderPaymentService;
use Illuminate\Support\Facades\Route;

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

    public function updated(OrderAddress $orderAddress)
    {
        $this->removingMissingDeliveryAddressLabelHandler($orderAddress);
        $this->addLabelIfManualCheckIsRequired($orderAddress);
        
        $this->addHistoryLog($orderAddress);
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

    protected function addLabelIfManualCheckIsRequired(OrderAddress $orderAddress): void
    {
        if (app(OrderPaymentService::class)->hasAnyPayment($orderAddress->order) &&
            !(new OrderAddressService())->addressIsValid($orderAddress)) {
            dispatch_now(new AddLabelJob($orderAddress->order->id, [184]));
        } else {
            dispatch_now(new RemoveLabelJob($orderAddress->order->id, [184]));
        }
    }
    
    protected function addHistoryLog(OrderAddress $orderAddress): void {
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
