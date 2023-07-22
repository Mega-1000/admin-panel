<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $offer_name
 * @property string $offer_identification
 * @property string $operation_type
 * @property string $credit
 * @property string $debit
 * @property string $balance
 * @property string $operation_details
 * @property string $attached_value_parameter
 */
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
        'attached_value_parameter'
    ];

    protected $casts = [
        'date_of_commitment_creation' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
