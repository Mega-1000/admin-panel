<?php

declare(strict_types=1);

namespace App\Helpers;

class TokenHelper
{
    public static function generateMD5Token(): string
    {
        return md5(uniqid());
    }
}
