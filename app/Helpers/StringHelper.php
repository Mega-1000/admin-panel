<?php

namespace App\Helpers;

class StringHelper
{
    /**
     * cares about polish characters
     * @param string $string
     * @return bool
     */
    public static function isAlpha(string $string): bool
    {
        return ctype_alpha(PdfCharactersHelper::changePolishCharactersToNonAccented($string));
    }

    /**
     * cares about polish characters
     * @param string $string
     * @return bool
     */
    public static function hasThreeLettersInARow(string $string): bool 
    {
        $letters = str_split($string);
        $count = 0;
        foreach ($letters as $letter) {
            if (self::isAlpha($letter)) {
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

    /**
     * @param string $string
     * @param string[] $characters
     * @return string
     */
    public static function addCharactersInReverseOrder(string $string, array $characters): string
    {
        $charactersString = strrev(implode('', $characters));        

        return $charactersString . $string;
    }

    /**
     * @param string $string
     * @return array
     */
    public static function separateLastWord(string $string): array
    {
        $words = explode(' ', $string);
        $lastWord = array_pop($words);
        $firstWords = implode(' ', $words);
        return [$firstWords, $lastWord];
    }

    /**
     * @param string $string
     * @return string
     */
    public static function removeMultipleSpaces(string $string): string
    {
        $words = explode(' ', $string);
        $words = array_filter($words, function($word) {
            return $word !== '';
        });
        return implode(' ', $words);
    }
}
