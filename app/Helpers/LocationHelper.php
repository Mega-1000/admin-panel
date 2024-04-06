
<?php

namespace App\Helpers;

use App\Entities\Product;
use Illuminate\Support\Facades\DB;

class LocationHelper
{
    public function getAvaiabilityOfProductForZipCode(Product $product, string $zipCode): bool
    {
        $firm = $product->firm;
        $coordinatesOfUser = DB::table('postal_code_lat_lon')->where('postal_code', $zipCode)->get()->first();

        $raw = DB::selectOne(
            'SELECT w.id, pc.latitude, pc.longitude, 1.609344 * SQRT(
                    POW(69.1 * (pc.latitude - :latitude), 2) +
                    POW(69.1 * (:longitude - pc.longitude) * COS(pc.latitude / 57.3), 2)) AS distance
                    FROM postal_code_lat_lon pc
                         JOIN warehouse_addresses wa on pc.postal_code = wa.postal_code
                         JOIN warehouses w on wa.warehouse_id = w.id
                    WHERE w.firm_id = :firmId AND w.status = \'ACTIVE\'
                    ORDER BY distance
                limit 1',
            [
                'latitude' => $coordinatesOfUser->latitude,
                'longitude' => $coordinatesOfUser->longitude,
                'firmId' => $firm->id
            ]
        );

        $radius = $raw?->distance;

        if ($radius && $radius > $firm->warehouses()->first()->radius) {
            return false;
        }

        return true;
    }
}
