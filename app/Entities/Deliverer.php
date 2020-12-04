<?php

declare(strict_types=1);

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deliverer extends Model
{
    protected $fillable = ['name'];

    public function importRules(): HasMany
    {
        return $this->hasMany(DelivererImportRule::class);
    }
}
