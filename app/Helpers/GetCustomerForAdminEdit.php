<?php

namespace App\Helpers;

use App\Entities\Customer;
use App\Helpers\interfaces\iGetUser;

class GetCustomerForAdminEdit implements iGetUser
{
    public function getCustomer($order, $data)
    {
        $customer = $order->customer;
        if ($data['update_email'] && !empty($data['customer_login'])) {
            $existingCustomer = Customer::where('login', $data['customer_login'])->first();
            if ($existingCustomer) {
                $customer = $existingCustomer;
            } else {
                $customer->login = $data['customer_login'];
                $customer->save();
            }
        }
        return $customer;
    }
}
