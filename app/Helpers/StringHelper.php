<?php

namespace App\Helpers;

class StringHelper {
    public static function hasThreeLettersInARow(string $string): bool {
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

    public static function addFirstCharactersInReverseOrder(string $initialString, array $chars, int $count, int $idx): string {
        $result = $initialString;
        for ($i = $count; $i > 0; $i++) {
            $result = $chars[$idx - $i] . $result;
        }
        return strrev($result);
    }
}
