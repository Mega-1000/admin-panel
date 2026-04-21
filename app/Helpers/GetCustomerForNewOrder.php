<?php

namespace App\Helpers;

use App\Entities\Customer;
use App\Helpers\interfaces\iGetUser;
use Exception;
use Illuminate\Support\Facades\Hash;

class GetCustomerForNewOrder implements iGetUser
{
    /**
     * @throws Exception
     */
    public function getCustomer($order, $data): Customer
    {
        return $this->getCustomerByLogin($data['customer_login'] ?? '', $data['phone'] ?? '');
    }

    /**
     * @throws Exception
     */
    private function getCustomerByLogin(string $login, string $pass): Customer
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
            $customer->password = $customer->generatePassword($pass);
        }

        $customer->save();
        return $customer;
    }

}
