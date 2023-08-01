<?php

namespace App\Helpers;

class StringHelper 
{
    public static function hasThreeLettersInARow(string $string): bool 
    {
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

    public static function addCharactersInReverseOrder(string $string, array $characters): string
    {
        $charactersString = strrev(implode('', $characters));        

        return $charactersString . $string;
    }

    public static function separateLastWord(string $string): array
    {
        $words = explode(' ', $string);
        $lastWord = array_pop($words);
        $firstWords = implode(' ', $words);
        return [$firstWords, $lastWord];
    }

    public static function removeMultipleSpaces(string $string): string
    {
        $words = explode(' ', $string);
        $words = array_filter($words, function($word) {
            return $word !== '';
        });
        return implode(' ', $words);
    }
}
