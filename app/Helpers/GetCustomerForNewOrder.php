<?php

namespace App\Helpers;

use App\Entities\Customer;
use App\Helpers\interfaces\iGetUser;
use Exception;
use Illuminate\Support\Facades\Hash;

class GetCustomerForNewOrder implements iGetUser
{
    public function getCustomer($order, $data)
    {
        return $this->getCustomerByLogin($data['customer_login'] ?? '', $data['phone'] ?? '');
    }

    private function getCustomerByLogin(string $login, string $pass)
    {
        if (empty($login)) {
            throw new Exception('missing_customer_login');
        }

        $customer = Customer::where('login', $login)->first();
        $updatePass = empty($customer) || empty($customer->password);

        if ($customer && !$updatePass && !Hash::check($pass, $customer->password)) {
            throw new Exception('wrong_password');
        }

        if (!$customer) {
            $customer = new Customer();
            $customer->login = $login;
        }

        if ($updatePass) {
            $pass = $customer->generatePassword($pass);
            $customer->password = Hash::make($pass);
        }
        
        $customer->save();
        return $customer;
    }

}
