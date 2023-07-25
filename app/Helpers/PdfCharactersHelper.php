<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Entities\Customer;
use App\Entities\Employee;
use App\Entities\Firm;

class PdfCharactersHelper
{
    public static function changePolishCharactersToNonAccented(string $stringToReplace): string
    {
        $replaceCharactersArray = [
            "Ą" => "A",
            "ą" => "a",
            "Ć" => "C",
            "ć" => "c",
            "Ę" => "E",
            "ę" => "e",
            "Ł" => "L",
            "ł" => "l",
            "Ń" => "N",
            "ń" => "n",
            "Ó" => "O",
            "ó" => "o",
            "Ś" => "S",
            "ś" => "s",
            "Ź" => "Z",
            "ź" => "z",
            "Ż" => "Z",
            "ż" => "z",
        ];

        return strtr($stringToReplace, $replaceCharactersArray);
    }
}
