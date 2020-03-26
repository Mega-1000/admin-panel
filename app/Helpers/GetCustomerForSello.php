<?php

namespace App\Helpers;

use App\Entities\Customer;
use App\Helpers\interfaces\iGetUser;

class GetCustomerForSello implements iGetUser
{
    public function getCustomer($order, $data)
    {
        $customer = Customer::where('login', $data['customer_login'])->first();
        if (empty($customer)) {
            $newOrder = new GetCustomerForNewOrder();
            $newOrder->getCustomer($order, $data);
        }
    }
}
