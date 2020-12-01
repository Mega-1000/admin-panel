<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Entities\Customer;
use App\Entities\Employee;
use App\Entities\Firm;

class PdfCharactersHelper
{
    public static function changePolishCharactersToNonAccented($stringToReplace)
    {
        $replaceCharactersArray = array('ę' => 'e', 'ć' => 'c', 'ą' => 'a', 'ń' => 'n', 'ł' => 'l', 'ś' => 's', 'Ł' => 'L', 'Ż' => 'Z');

        return strtr($stringToReplace, $replaceCharactersArray);
    }
}
