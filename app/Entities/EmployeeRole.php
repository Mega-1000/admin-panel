<?php

namespace App\Entities;


use Illuminate\Database\Eloquent\Model;

class EmployeeRole extends Model
{
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employeerole_employee');
    }
}
