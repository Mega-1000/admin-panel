<?php

namespace App\Repositories;

use App\Entities\Firm;
use App\Entities\Order;
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

    public static function getEmployeesForAuction(Order $order): array
    {
        $variations = app(ProductService::class)->getVariations($order);

        $firms = array_unique(app(ChatAuctionsService::class)->getFirms($variations));

        $firms = array_filter($firms, function ($firm) {
            return $firm->id != 1;
        });

        $employees = [];
        foreach ($firms as $firm) {
            foreach (Employees::getEmployeesForAuctionOrderByFirm($firm) as $employee) {
                $employees[] = $employee;
            }
        }

        $employees = array_unique($employees, SORT_REGULAR);

        $allEmployees = [];
        foreach ($employees as $employee) {
            $firmAssociatedWithEmployee = Firm::where('email', $employee->email)->first();

            if ($firmAssociatedWithEmployee) {
                foreach ($firmAssociatedWithEmployee->employees as $emp) {
                    $allEmployees[] = $emp;
                }
            }
        }

        return array_unique($allEmployees, SORT_REGULAR);

    }
}
