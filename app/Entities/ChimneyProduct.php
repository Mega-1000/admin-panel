<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class ChimneyProduct extends Model
{
    public $fillable = ['product_code', 'formula', 'column_number', 'optional'];

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function replacements()
    {
        return $this->hasMany(ChimneyReplacement::class);
    }
}
