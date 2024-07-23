<?php

namespace App\Helpers;

use App\Entities\Customer;
use App\Entities\Employee;
use App\Entities\Firm;
use App\Entities\Order;
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

    public static function getNearestEmployeeOfFirm(Customer $customer, Firm $firm): ?Employee
    {
        $employees = $firm->employees;
        $nearestEmployee = null;
        $shortestDistance = PHP_FLOAT_MAX;

        foreach ($employees as $employee) {
            $distance = self::getDistanceOfClientToEmployee($employee, $customer);

            if ($distance >= 0 && $distance < $shortestDistance) {
                $nearestEmployee = $employee;
                $shortestDistance = $distance;
            }
        }

        return $nearestEmployee ?? $firm->employees()->first();
    }

    public static function getDistanceOfClientToEmployee(Employee $employee, Customer $customer): float
    {
        $customerCoordinates = self::getCoordinates($customer->standardAddress()?->postal_code);

        if (!$customerCoordinates) {
            return PHP_FLOAT_MAX; // Return max float value instead of a magic number
        }

        $minDistance = PHP_FLOAT_MAX;
        $employeeRadius = $employee->radius;

        for ($i = 1; $i <= 5; $i++) {
            $zipCodeField = "zip_code_$i";
            if (empty($employee->$zipCodeField)) {
                continue;
            }

            $zipCodeParts = explode(';', $employee->$zipCodeField);
            $employeeZipCode = $zipCodeParts[0];
            $employeeRadius = $zipCodeParts[1] ?? $employeeRadius; // Use the radius from zip code if available

            $employeeCoordinates = self::getCoordinates($employeeZipCode);

            if (!$employeeCoordinates) {
                continue;
            }

            $distance = self::calculateHaversineDistance(
                $customerCoordinates->latitude,
                $customerCoordinates->longitude,
                $employeeCoordinates->latitude,
                $employeeCoordinates->longitude
            );

            $minDistance = min($minDistance, $distance);
        }

        if ($minDistance === PHP_FLOAT_MAX) {
            return PHP_FLOAT_MAX; // No valid employee coordinates found
        }

        return $employeeRadius - $minDistance;
    }

    private static function getCoordinates(string $postalCode): ?object
    {
        return DB::table('postal_code_lat_lon')
            ->where('postal_code', $postalCode)
            ->first();
    }

    private static function calculateHaversineDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $earthRadius = 6371; // km

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $latDelta = $lat2 - $lat1;
        $lonDelta = $lon2 - $lon1;

        $a = sin($latDelta / 2) ** 2 + cos($lat1) * cos($lat2) * sin($lonDelta / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
    public static function nearestWarehouse(Order $order, Firm $firm): Warehouse
    {
        $coordinatesOfUser = DB::table('postal_code_lat_lon')->where('postal_code', $order->getDeliveryAddress()->postal_code)->get()->first();

        if (!$coordinatesOfUser) {
            return Warehouse::find($firm->warehouses()->first()->id);
        }

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
