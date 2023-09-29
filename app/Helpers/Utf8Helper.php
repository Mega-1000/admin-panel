<?php

namespace App\Helpers;

class Utf8Helper
{
    public static function sanitizeString(string $input): string
    {
        if (mb_check_encoding($input, 'UTF-8')) {
            return $input;
        } else {
            $sanitizedString = mb_convert_encoding($input, 'UTF-8', 'UTF-8');

            return preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '', $sanitizedString);
        }
    }
}
