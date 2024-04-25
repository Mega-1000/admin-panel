<?php

namespace App\Helpers;

use App\Entities\Customer;
use App\Entities\Employee;
use App\Entities\Firm;
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

    public static function getDistanceOfProductForZipCode(Firm $firm, string $zipCode): int
    {
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

        return $raw?->distance;
    }

    public static function getDistanceOfClientToEmployee(Employee $employee, Customer $customer): int
    {
        $coordinates1 = DB::table('postal_code_lat_lon')->where('postal_code', $customer->standardAddress()->postal_code)->get()->first();
        $coordinates2 = DB::table('postal_code_lat_lon')->where('postal_code', $employee->postal_code)->get()->first();
        $radius = $employee->radius;

        $raw = DB::selectOne(
            'SELECT 6371 * 2 * ASIN(SQRT(
                POW(SIN((:latitude2 - :latitude1) * PI() / 360), 2) +
                COS(:latitude1 * PI() / 180) * COS(:latitude2 * PI() / 180) *
                POW(SIN((:longitude2 - :longitude1) * PI() / 360), 2)
            )) AS distance',
            [
                'latitude1' => $coordinates1->latitude,
                'longitude1' => $coordinates1->longitude,
                'latitude2' => $coordinates2->latitude,
                'longitude2' => $coordinates2->longitude
            ]
        );


        $distance = $raw->distance;

        return $radius - $distance;
    }
}
