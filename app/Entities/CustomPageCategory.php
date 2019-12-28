<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

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

    public function childrens()
    {
        return $this->hasMany('App\Entities\CustomPageCategory', 'parent_id', 'id');
    }

    public function pages()
    {
        return $this->hasMany('App\Entities\CustomPage', 'category_id', 'id');
    }
}
