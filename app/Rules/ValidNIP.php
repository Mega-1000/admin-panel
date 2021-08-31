<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidNIP implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
	    $nipWithoutDashes = preg_replace("/-/", "", $value);
	    $reg = '/^[0-9]{10}$/';
	    if (preg_match($reg, $nipWithoutDashes) == false) {
		    return false;
	    } else {
		    $digits = str_split($nipWithoutDashes);
		    $checksum = (6 * intval($digits[0]) + 5 * intval($digits[1]) +
				    7 * intval($digits[2]) + 2 * intval($digits[3]) + 3 * intval($digits[4]) +
				    4 * intval($digits[5]) + 5 * intval($digits[6]) + 6 * intval($digits[7]) +
				    7 * intval($digits[8])) % 11;
		
		    return (intval($digits[9]) == $checksum);
	    }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.custom.NIP.ValidNip');
    }
}
