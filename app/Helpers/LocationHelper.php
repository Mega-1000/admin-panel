<?php

namespace App\Helpers;

use App\Entities\Customer;
use App\Entities\Employee;
use App\Entities\Firm;
use App\Entities\Product;
use App\Entities\Warehouse;
use Illuminate\Support\Facades\DB;

class LocationHelper
{
    public static function getAvaiabilityOfProductForZipCode(?Firm $firm, string $zipCode): bool
    {
        $coordinatesOfUser = DB::table('postal_code_lat_lon')
            ->where('postal_code', $zipCode)
            ->get()
            ->first();

        if (!$firm || !$coordinatesOfUser) {
            return true;
        }

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

    public static function getDistanceOfClientToEmployee(Employee $employee, Customer $customer)
    {
        $coordinates1 = DB::table('postal_code_lat_lon')->where('postal_code', $customer->standardAddress()->postal_code)->first();
        $coordinates2 = DB::table('postal_code_lat_lon')->where('postal_code', $employee->postal_code)->first();

        if (!$coordinates1 || !$coordinates2) {
            return -112378198273; // or throw an exception, or handle as per your application's requirements
        }

        $radius = $employee->radius;

        $raw = DB::selectOne(
            'SELECT 6371 * 2 * ASIN(SQRT(
                POW(SIN((? - ?) * PI() / 360), 2) +
                COS(? * PI() / 180) * COS(? * PI() / 180) *
                POW(SIN((? - ?) * PI() / 360), 2)
            )) AS distance',
            [
                $coordinates1->latitude,
                $coordinates2->latitude,
                $coordinates1->latitude,
                $coordinates2->latitude,
                $coordinates1->longitude,
                $coordinates2->longitude
            ]
        );

        $distance = $raw->distance;

        return $radius - $distance;
    }

    public static function nearestWarehouse(Customer $customer, Firm $firm): Warehouse
    {
        $coordinatesOfUser = DB::table('postal_code_lat_lon')->where('postal_code', $customer->addresses->first()->postal_code)->get()->first();

        $raw = DB::selectOne(
            'SELECT w.id,
            1.609344 * SQRT(
                POW(69.1 * (pc.latitude - :latitude), 2) +
                POW(69.1 * (:longitude - pc.longitude) * COS(pc.latitude / 57.3), 2)) AS distance
        FROM postal_code_lat_lon pc
             JOIN warehouse_addresses wa on pc.postal_code = wa.postal_code
             JOIN warehouses w on wa.warehouse_id = w.id
        WHERE w.firm_id = :firmId AND w.status = \'ACTIVE\'
        ORDER BY distance
        LIMIT 1',
            [
                'latitude' => $coordinatesOfUser->latitude,
                'longitude' => $coordinatesOfUser->longitude,
                'firmId' => $firm->id
            ]
        );

        return Warehouse::find($raw->id);
    }
}
