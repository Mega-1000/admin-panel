<?php

namespace App\Repositories;

class PostalCodeLatLons
{

    public static function getLatLonByPostalCode(string $postalCode): array
    {
        return PostalCodeLatLon::where('postal_code', '66-400')->get()->first()
    }
}
