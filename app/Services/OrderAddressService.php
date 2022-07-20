<?php namespace App\Services;

use App\Entities\Label;
use App\Entities\Order;
use App\Entities\OrderAddress;
use App\Helpers\Helper;
use App\Jobs\AddLabelJob;
use App\Rules\ValidNIP;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderAddressService
{
	protected $errors = false;

	public function errors() {
		return $this->errors;
	}

	public function addressIsValid(OrderAddress $address): bool
	{
		$addressArray = $address->toArray();
		$rules = $this->getRules($address);

		$validator = Validator::make($addressArray, $rules);

		$this->errors = $validator->errors();

		return !$validator->fails();
	}

	public function preSaveCleanup(OrderAddress $address)
	{
	    $address->address = preg_replace('/ul\./i', '', $address->address);

		foreach ($address->getFillable() as $field) {
			$address->$field = is_null($address->$field) ? $address->$field : trim($address->$field);

			if ($address->type == OrderAddress::TYPE_INVOICE) {
				if ($field == 'firmname' && $address->$field) {
					$address->firstname = '';
					$address->lastname = '';
				}
			}
		}

		$this->reformatPostalCode($address);
        $this->reformatNIP($address);
        $this->reformatFirstLastName($address);
	}

    /**
     * @param OrderAddress $address
     *
     */
    protected function reformatFirstLastName(OrderAddress $address)
    {
        if ($address->firstname == 'Paczkomat') {
            return;
        }

        $address->firstname = Helper::clearSpecialChars($address->firstname);
        $address->lastname = Helper::clearSpecialChars($address->lastname, false);
    }

    /**
     * @param OrderAddress $address
     *
     */
	protected function reformatPhoneNumber(OrderAddress $address)
	{
        list($code, $phone) = Helper::prepareCodeAndPhone((string)$address->phone);

        $address->phone_code = $address->phone_code . ($code ? '-' . $code : '');
        $address->phone_code = preg_replace('/[^0-9\+\-]+/', '', $address->phone_code);
        $address->phone = $phone;
	}

    /**
     * @param OrderAddress $address
     *
     */
	protected function reformatPostalCode(OrderAddress $address)
	{
        $postalCodeString = (string)$address->postal_code;

        if ($address->country_id == 1 && preg_match('/^[0-9]{5}$/', $postalCodeString)) {
            $address->postal_code = substr_replace($postalCodeString, '-', 2, 0);
        }

        $address->postal_code = $postalCodeString;
	}

    protected function reformatNIP(OrderAddress $address)
    {
        $nipString = (string)$address->nip;

        if ($address->country_id == 1) {
            $nipString = preg_replace('/[^0-9]+/', '', $nipString);
        }

        $address->nip = $nipString;
    }

	protected function getRules(OrderAddress $address): array
	{
		$rules = [
			'email' => ['required', 'email'],
			'address' => ['required'],
			'flat_number' => ['required', 'string', 'max:10'],
			'city' => ['required'],
			'postal_code' => ['required'],
			'phone' => ['required']
		];

		if ($address->type == OrderAddress::TYPE_DELIVERY) {
			$rules['firstname'] = ['required'];
			$rules['lastname'] = ['required'];
			$rules['firmname'] = ['sometimes', 'nullable', 'string'];
		} elseif ($address->type == OrderAddress::TYPE_INVOICE) {
			$rules['firstname'] = ['required_without_all:firmname'];
			$rules['lastname'] = ['required_without_all:firmname'];
			$rules['firmname'] = ['required_with_all:nip', 'required_without:firstname,lastname'];
			$rules['nip'] = ['required_with_all:firmname', 'required_without:firstname,lastname'];
			if ($address->firmname || $address->nip) {
				$rules['nip'][] = new ValidNIP($address->country_id == 1);
			}
		}

		return $rules;
	}
}
