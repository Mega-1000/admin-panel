<?php

namespace App\Repositories;

use App\Entities\PostalCodeLatLon;

class PostalCodeLatLons
{

    public static function getLatLonByPostalCode(string $postalCode): PostalCodeLatLon
    {
        return PostalCodeLatLon::where('postal_code', $postalCode)->get()->first();
    }
}
