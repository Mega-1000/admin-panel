<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomPageCategory extends Model
{
    protected $table = 'custom_page_categories';

    protected static function boot()
    {
        parent::boot();
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function childrens(): HasMany
    {
        return $this->hasMany(CustomPageCategory::class, 'parent_id', 'id');
    }

    public function pages(): HasMany
    {
        return $this->hasMany(CustomPage::class, 'category_id', 'id');
    }
}
