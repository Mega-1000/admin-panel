<?php

namespace App\Helpers;

class StringHelper {
    public static function hasThreeLettersInARow(string $string) {
        $letters = str_split($string);
        $count = 0;
        foreach ($letters as $letter) {
            if (ctype_alpha($letter)) {
                $count++;
            } else {
                $count = 0;
            }
            if ($count === 3) {
                return true;
            }
        }
        return false;
    }
}
