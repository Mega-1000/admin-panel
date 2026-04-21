<?php

namespace App\Services;

use App\Entities\Customer;

class AddressService
{
    /**
     * Update the address of the given type for the given user.
     *
     * @param Customer $user
     * @param  string  $type
     * @param  array  $data
     * @return void
     */
    private function updateAddressForUser($user, $type, $data): void
    {
        $address = $user->{$type . 'Address'}();
        $address->update($data[$type . 'Address']);
    }

    /**
     * Update the addresses for the given user.
     *
     * @param Customer $user
     * @param  array  $data
     * @return void
     */
    public function updateAddressesForUser(Customer $user, array $data): void
    {
        $this->updateAddressForUser($user, 'standard', $data);
        $this->updateAddressForUser($user, 'invoice', $data);
        $this->updateAddressForUser($user, 'delivery', $data);
    }
}
