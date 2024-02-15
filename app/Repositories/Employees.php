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

        $employees = [];
        foreach ($firms as $firm) {
            foreach (Employees::getEmployeesForAuctionOrderByFirm($firm) as $employee) {
                $employees[] = $employee;
            }
        }

        $employees = array_unique($employees);

        foreach ($employees as $employee) {
            $firmAssociatedWithEmployee = Firm::where('email', $employee->email)->first();

            if ($firmAssociatedWithEmployee) {
                foreach ($firmAssociatedWithEmployee->employees as $employee) {
                    $employees[] = $employee;
                }
            }
        }

        return array_unique($employees);
    }
}
