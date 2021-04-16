<?php namespace App\Services;

use App\Entities\OrderAddress;
use Illuminate\Support\Facades\Validator;

class OrderAddressService
{

    public function addressIsValid(OrderAddress $address): bool
    {
        $addressArray = $address->toArray();
        $validator = Validator::make($addressArray, [
            'firstname' => 'required_with:lastname',
            'lastname' => 'required_with:firstname',
            'email' => 'email',
            'firmname' => 'required_with:nip',
            'nip' => 'required_with:firmname',
            'postal_code' => 'regex:/^[0-9]{2}-?[0-9]{3}$/Du',
            'phone' => 'regex:/^[0-9]{9}\b/'
        ]);

        $nipIsValid = array_key_exists('nip', $addressArray) && $addressArray['nip'] &&
            $this->validateNIP($addressArray['nip']);

        return !$validator->fails() && !$this->namesAndNipCombined($address) && $nipIsValid;
    }


    protected function validateNIP($nip): bool
    {
        $nipWithoutDashes = preg_replace("/-/", "", $nip);
        $reg = '/^[0-9]{10}$/';
        if (preg_match($reg, $nipWithoutDashes) == false)
            return false;
        else {
            $digits = str_split($nipWithoutDashes);
            $checksum = (6 * intval($digits[0]) + 5 * intval($digits[1]) +
                    7 * intval($digits[2]) + 2 * intval($digits[3]) + 3 * intval($digits[4]) +
                    4 * intval($digits[5]) + 5 * intval($digits[6]) + 6 * intval($digits[7]) +
                    7 * intval($digits[8])) % 11;

            return (intval($digits[9]) == $checksum);
        }
    }

    protected function namesAndNipCombined(OrderAddress $address): bool
    {
        if ($address->nip != null) {
            return ($address->firstname != null || $address->lastname != null);
        }

        return false;
    }

}
