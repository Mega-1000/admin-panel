<?php

namespace App\Http\Livewire\Traits;

use App\Entities\Order;
use App\Entities\OrderAddress;
use App\Helpers\BackPackPackageDivider;
use Exception;

trait WithOrderDataMoving
{
    public ?int $idOfOrderToMove = null;

    public function setOrderToMove(int $id): void
    {
        $this->idOfOrderToMove = $id;

        $this->skipRender();
    }

    /**
     * @param int $id
     * @return void
     * @throws Exception
     */
    public function moveDataToOrder(int $id): void
    {
        if (!$this->idOfOrderToMove) {
            throw new Exception('No order to move');
        }

        $orderToGetData = Order::find($this->idOfOrderToMove);
        $orderToSendData = Order::find($id);

        foreach ($orderToGetData->addresses as $address) {
            if ($address->type == 'DELIVERY_ADDRESS') {
                $deliveryAddress = [
                    'firstname' => $address->firstname,
                    'lastname' => $address->lastname,
                    'firmname' => $address->firmname,
                    'nip' => $address->nip,
                    'phone' => $address->phone,
                    'address' => $address->address,
                    'flat_number' => $address->flat_number,
                    'city' => $address->city,
                    'postal_code' => $address->postal_code,
                    'email' => $address->email
                ];
            } else {
                if ($address->type == 'INVOICE_ADDRESS') {
                    $invoiceAddress = [
                        'firstname' => $address->firstname,
                        'lastname' => $address->lastname,
                        'firmname' => $address->firmname,
                        'nip' => $address->nip,
                        'phone' => $address->phone,
                        'address' => $address->address,
                        'flat_number' => $address->flat_number,
                        'city' => $address->city,
                        'postal_code' => $address->postal_code,
                        'email' => $address->email
                    ];
                }
            }
        }

        foreach ($orderToSendData->addresses as $address) {
            if ($address->type == 'DELIVERY_ADDRESS') {
                OrderAddress::find($address->id)->update($deliveryAddress);
            } else {
                if ($address->type == 'INVOICE_ADDRESS') {
                    OrderAddress::find($address->id)->update($invoiceAddress);
                }
            }
        }

        $this->emit('orderMoved');
    }
}
