<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Category.
 *
 * @package namespace App\Entities;
 */
class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'img',
        'slug',
        'is_active',
        'is_deleted',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function chimneyAttributes()
    {
        return $this->hasMany('App\Entities\ChimneyAttribute');
    }

    public function chimneyProducts()
    {
        return $this->hasMany('App\Entities\ChimneyProduct');
    }

    public function parentCategory()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }
}
