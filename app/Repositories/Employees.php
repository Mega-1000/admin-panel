<?php

namespace App\Repositories;

use App\Entities\Firm;
use App\Entities\Order;
use App\Helpers\LocationHelper;
use App\Services\ChatAuctionsService;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\Collection;

class Employees
{
    /**
     * @param $firm
     * @return mixed
     */
    public static function getEmployeesForAuctionOrderByFirm($firm): Collection
    {
        return $firm->employees()->where('status', '!=', 'PENDING')->distinct('email')->whereHas('employeeRoles', function ($query) {
            $query->where('name', 'zamowienia towaru');
        })->get();
    }

    public static function getEmployeesForAuction(Order $order, ?Firm $firm = null): array
    {
        $variations = app(ProductService::class)->getVariations($order);

        $firms = array_unique(app(ChatAuctionsService::class)->getFirms($variations));

        $firms = array_filter($firms, function ($firm) {
            return $firm->id != 1;
        });

        if ($firm) {
            $firms = [
                $firm
            ];
        }

        $employees = collect();
        foreach ($firms as $firm) {
            foreach (Employees::getEmployeesForAuctionOrderByFirm($firm) as $employee) {
                $employee->finalRadius = LocationHelper::getDistanceOfClientToEmployee($employee, $order->customer);
                $employees->push($employee);
            }
        }

        $employees = $employees
            ->groupBy('firm_id') // Assuming there is a 'firm_id' attribute to group by
            ->map(function ($group) {
                return $group->reduce(function ($carry, $item) {
                    return ($carry === null || $item->finalRadius > $carry->finalRadius) ? $item : $carry;
                });
            });


        $employees = $employees->unique(function ($employee) {
            return $employee->id;  // Assuming each employee has a unique 'id' attribute
        });

        $allEmployees = collect(); // Initialize as a collection
        foreach ($employees as $employee) {
            $firmAssociatedWithEmployee = Firm::where('email', $employee->email)->first();

            if ($firmAssociatedWithEmployee) {
                foreach ($firmAssociatedWithEmployee->employees as $emp) {
                    $allEmployees->push($emp);
                }
            }
        }

        return $employees->unique()->all();
    }
}
