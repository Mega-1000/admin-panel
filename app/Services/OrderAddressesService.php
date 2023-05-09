<?php

namespace App\Services;

use App\Entities\Customer;
use App\Entities\CustomerAddress;
use App\Entities\OrderAddress;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderAddressesService
{
    /**
     * @param string $type
     * @return void
     * @throws Exception
     */
    private static function checkAddressType(string $type): void
    {
        if (!in_array($type, ['INVOICE_ADDRESS', 'DELIVERY_ADDRESS'])) {
            throw new Exception('invalid_address_type');
        }
    }

    /**
     * @param string $type
     * @param Customer $customer
     *
     * @return Model|HasMany
     * @throws Exception
     */
    private static function getUserAddress(string $type, Customer $customer): ?CustomerAddress
    {
        self::checkAddressType($type);

        return $customer->addresses()->where('type', $type)->first();
    }

    /**
     * @param Customer $customer
     * @param OrderAddress $orderAddress
     *
     * @throws Exception
     */
    public static function updateOrderAddressFromCustomer(OrderAddress $orderAddress, Customer $customer): void
    {
        $customerAddress = self::getUserAddress($orderAddress->type, $customer);
        if ($customerAddress === null) {
            return;
        }

        unset($customerAddress->customer_id);

        $orderAddress->fill($customerAddress->toArray());
        $orderAddress->save();
    }
}
