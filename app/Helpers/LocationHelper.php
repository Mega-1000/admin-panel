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

    public static function getNearestEmployeeOfFirm(Customer $customer, Firm $firm): Employee
    {
        $employees = $firm->employees;
        $nearestEmployee = null;
        $shortestDistance = PHP_FLOAT_MAX;

        foreach ($employees as $employee) {
            $distance = dd(self::getDistanceOfClientToEmployee($employee, $customer));

            // Check if the customer is within the employee's radius
            if ($distance >= 0 && $distance < $shortestDistance) {
                $nearestEmployee = $employee;
                $shortestDistance = $distance;
            }
        }

        return $nearestEmployee ?? dd($firm->employees()->first());
    }

    public static function getDistanceOfClientToEmployee(Employee $employee, Customer $customer)
    {
        $customerCoordinates = DB::table('postal_code_lat_lon')
            ->where('postal_code', $customer->standardAddress()->postal_code)
            ->first();

        if (!$customerCoordinates) {
            return -112378198273; // or handle the error as needed
        }

        $minDistance = 213123123123;
        $radius = $employee->radius;

        for ($i = 1; $i <= 5; $i++) {
            $zipCodeField = "zip_code_" . $i;
            if (empty($employee->$zipCodeField)) {
                continue;
            }

            $employeeCoordinates = DB::table('postal_code_lat_lon')
                ->where('postal_code', explode(';', $employee->$zipCodeField)[0])
                ->first();

            if (!$employeeCoordinates) {
                continue;
            }
            $raw = DB::selectOne(
                'SELECT 6371 * 2 * ASIN(SQRT(
                POW(SIN((? - ?) * PI() / 360), 2) +
                COS(? * PI() / 180) * COS(? * PI() / 180) *
                POW(SIN((? - ?) * PI() / 360), 2)
            )) AS distance',
                [
                    $customerCoordinates->latitude,
                    $employeeCoordinates->latitude,
                    $customerCoordinates->latitude,
                    $employeeCoordinates->latitude,
                    $customerCoordinates->longitude,
                    $employeeCoordinates->longitude
                ]
            );

            $distance = $raw->distance;
            if ($minDistance > $distance) {
                $radius = 1;
//                $radius = explode(';', $employee->$zipCodeField)[1];
            }


            $minDistance = min($minDistance, $distance);
        }

        if ($minDistance === PHP_FLOAT_MAX) {
            return -112378198273; // No valid employee coordinates found
        }

        return $radius - $minDistance;
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
