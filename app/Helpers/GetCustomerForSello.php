<?php

namespace App\Helpers;

use App\Entities\Customer;
use App\Helpers\interfaces\iGetUser;

class GetCustomerForSello implements iGetUser
{
    public function getCustomer($order, $data)
    {
        return Customer::where('login', $data['customer_login'])->first();
    }
}
