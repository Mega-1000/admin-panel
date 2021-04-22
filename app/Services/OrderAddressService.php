<?php namespace App\Services;

use App\Entities\OrderAddress;
use Illuminate\Support\Facades\Validator;

class OrderAddressService
{

    const TYPE_DELIVERY = 'DELIVERY_ADDRESS';
    const TYPE_INVOICE = 'INVOICE_ADDRESS';

    public function addressIsValid(OrderAddress $address): bool
    {
        $addressArray = $address->toArray();
        $rules = [
            'firstname' => 'required_with:lastname',
            'lastname' => 'required_with:firstname',
            'email' => 'required|email',
            'address' => 'required',
            'city' => 'required',
            'flat_number' => 'required',
            'postal_code' => 'required|regex:/^[0-9]{2}-?[0-9]{3}$/Du',
            'phone' => 'regex:/^[0-9]{9}\b/'
        ];
        if ($address->type == self::TYPE_INVOICE) {
            $rules['firmname'] = 'required_with:nip';
            $rules['nip'] = 'required_with:firmname';
        }

        $validator = Validator::make($addressArray, $rules);

        if (array_key_exists('nip', $addressArray) && $addressArray['nip'] != null) {
            $nipIsValid = $this->validateNIP($addressArray['nip']);
        } else {
            $nipIsValid = true;
        }

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
        if ($address->type == self::TYPE_INVOICE && $address->nip && ($address->first_name || $address->last_name)) {
            return true;
        }

        return false;
    }

}
