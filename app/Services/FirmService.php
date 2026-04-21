<?php

namespace App\Services;

use App\Entities\Employee;
use App\Entities\EmployeeRole;
use App\Entities\Firm;

class FirmService {

    public function __construct(
        public Firm $firm
    ) {}

    /**
     * Add new Employee for Firm Complaint Email Address
     *
     * @return bool
     */
    public function addNewEmployeeForComplaint(): bool {

        if($this->firm->complaint_email === null) return false;
        
        $employee = Employee::firstOrNew([
            'email' => $this->firm->complaint_email,
        ]);
        $employee->firm_id = $this->firm->id;
        $employee->email   = $this->firm->complaint_email;
        $employee->status  = 'ACTIVE';

        $employeeRole = EmployeeRole::where('symbol', 'OZZ')->first();
        $employee->employeeRoles()->sync($employeeRole);
        $employee->radius  = 1000;
        $employee->save();

        return true;
    }
}
