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
    public static function getFirstCustomerWithLogin(string $login): Customer
    {
        return Customer::where('login', $login)->first() ?? Customer::first();
    }
}
