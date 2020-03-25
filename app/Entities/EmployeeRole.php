<?php

namespace App\Entities;


use Illuminate\Database\Eloquent\Model;

class EmployeeRole extends Model
{
    public function employee()
    {
        return $this->belongsToMany(Employee::class);
    }
}
