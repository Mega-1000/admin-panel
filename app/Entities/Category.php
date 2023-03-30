<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Category.
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $img
 * @property string $rewrite
 * @property boolean $save_name
 * @property boolean $save_description
 * @property boolean $save_image
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
        'save_name',
        'save_description',
        'save_image'
    ];

    protected $casts = [
        'save_name' => 'boolean',
        'save_description' => 'boolean',
        'save_image' => 'boolean',
    ];

    /**
     * @return HasMany
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
