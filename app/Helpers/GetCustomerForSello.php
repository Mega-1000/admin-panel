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
        $updatePass = empty($customer) || empty($customer->password);

        if (empty($customer)) {
            $customer = new Customer();
            $customer->login = $data['customer_login'];
        }

        if ($updatePass) {
            $customer->password = $customer->generatePassword($data['phone']);
        }
        if (empty($customer->nick_allegro)) {
            $customer->nick_allegro = $data['nick_allegro'] ?? '';
        }

        $customer->save();

        return $customer;
    }
}
