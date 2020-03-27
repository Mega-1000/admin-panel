<?php

namespace App\Helpers;

use App\Entities\Customer;
use App\Helpers\interfaces\iGetUser;
use Illuminate\Support\Facades\Hash;

class GetCustomerForSello implements iGetUser
{
    public function getCustomer($order, $data)
    {
        $customer = Customer::where('login', $data['customer_login'])->first();
        if (empty($customer)) {
            $newOrder = new GetCustomerForNewOrder();
            $newOrder->getCustomer($order, $data);
            $customer = new Customer();
            $pass = $customer->generatePassword($data['phone']);
            $customer->login = $data['customer_login'];
            $customer->password = Hash::make($pass);
            $customer->save();
        }
        return $customer;
    }
}
