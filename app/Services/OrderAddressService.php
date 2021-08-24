<?php namespace App\Services;

use App\Entities\OrderAddress;
use App\Rules\ValidNIP;
use Illuminate\Support\Facades\Validator;

class OrderAddressService
{
	public function addressIsValid(OrderAddress $address): bool
	{
		$addressArray = $address->toArray();
		$rules = $this->getRules($address);
		
		$validator = Validator::make($addressArray, $rules);
		
		return !$validator->fails();
	}
	
	public function preSaveCleanup(OrderAddress $address)
	{
		foreach ($address->getFillable() as $field) {
			$address->$field = $address->$field !== null ?? trim($address->$field);
			if ($address->type == OrderAddress::TYPE_INVOICE) {
				if ($field == 'firmname' && $address->$field) {
					$address->firstname = '';
					$address->lastname = '';
				}
			}
		}
		$this->reformatPhoneNumber($address);
		$this->reformatPostalCode($address);
	}
	
	protected function reformatPhoneNumber(OrderAddress $address)
	{
		$phoneString = (string)$address->phone;
		if ($phoneString && $phoneString[0] == 0) {
			$address->phone = substr($phoneString, 1);
		}
	}
	
	protected function reformatPostalCode(OrderAddress $address)
	{
		$postalCodeString = (string)$address->postal_code;
		if (preg_match('/^[0-9]{5}$/', $postalCodeString)) {
			$address->postal_code = substr_replace($postalCodeString, '-', 2, 0);
		}
	}
	
	protected function getRules(OrderAddress $address): array
	{
		$rules = [
			'email' => ['required', 'email'],
			'address' => ['required'],
			'flat_number' => ['required', 'string', 'max:10'],
			'city' => ['required'],
			'postal_code' => ['required', 'regex:/^[0-9]{2}-?[0-9]{3}$\b/'],
			'phone' => ['required', 'regex:/^[0-9]{9}$\b/']
		];
		
		if ($address->type == OrderAddress::TYPE_DELIVERY) {
			$rules['firstname'] = ['required'];
			$rules['lastname'] = ['required'];
			$rules['firmname'] = ['string'];
			$rules['nip'] = ['string', 'size:0'];
		} elseif ($address->type == OrderAddress::TYPE_INVOICE) {
			$rules['firstname'] = ['required_without_all:firmname'];
			$rules['lastname'] = ['required_without_all:firmname'];
			$rules['firmname'] = ['required_with_all:nip', 'required_without:firstname,lastname'];
			$rules['nip'] = ['required_with_all:firmname', 'required_without:firstname,lastname', new ValidNIP()];
		}
		
		return $rules;
	}
}
