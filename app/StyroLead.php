<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StyroLead extends Model
{
    use HasFactory;

    public function mails(): HasMany
    {
        return $this->hasMany(StyroLeadMail::class);
    }
}
