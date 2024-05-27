<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirmRepresent extends Model
{
    use HasFactory;

    public $fillable = [
        'email_of_employee',
        'phone',
        'email',
        'is_main',
        'contact_info',
        'firm_id',
    ];
}
