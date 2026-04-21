<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChimneyReplacement extends Model
{
    public $fillable = ['product', 'quantity'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ChimneyProduct::class);
    }
}
