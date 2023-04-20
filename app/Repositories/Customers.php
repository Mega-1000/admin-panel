<?php

namespace App\Repositories;

use App\Entities\Customer;

class Customers
{
    /**
     * Get first customer with login
     *
     * @param String $login
     * @return Customer
     */
    public static function getFirstCustomerWithLogin(String $login): Customer
    {
        return Customer::where('login', $login)->firstOrFail();
    }
}
