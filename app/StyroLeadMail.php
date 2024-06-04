<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StyroLeadMail extends Model
{
    use HasFactory;

    public function lead(): BelongsTo
    {
        return $this->belongsTo(StyroLead::class);
    }
}
