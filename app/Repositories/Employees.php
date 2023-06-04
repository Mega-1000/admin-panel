<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

class Employees
{
    /**
     * @param $firm
     * @return mixed
     */
    public function getEmployeesForAuctionOrderByFirm($firm): Collection
    {
        return $firm->employees()->where('status', '!=', 'PENDING')->distinct('email')->whereHas('employeeRoles', function ($query) {
            $query->where('name', 'zamowienia towaru');
        })->get();
    }
}
