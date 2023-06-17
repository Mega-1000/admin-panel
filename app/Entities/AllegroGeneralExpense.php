<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllegroGeneralExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_name',
        'offer_identification',
        'operation_type',
        'credit',
        'debit',
        'balance',
        'operation_details',
    ];

    protected $casts = [
        'date_of_commitment_creation' => 'datetime',
    ];
}
