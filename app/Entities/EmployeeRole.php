<?php

namespace App\Entities;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EmployeeRole extends Model
{
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employeerole_employee');
    }
}
