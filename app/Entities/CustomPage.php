<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomPage extends Model
{

    protected $table = 'custom_page_content';

    public function category(): BelongsTo
    {
        return $this->belongsTo(CustomPageCategory::class, 'category_id', 'id');
    }

}
