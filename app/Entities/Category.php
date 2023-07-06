<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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
        'save_image',
        'rewrite',
    ];

    protected $casts = [
        'save_name' => 'boolean',
        'save_description' => 'boolean',
        'save_image' => 'boolean',
    ];

    /**
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function chimneyAttributes(): HasMany
    {
        return $this->hasMany(ChimneyAttribute::class);
    }

    public function chimneyProducts(): HasMany
    {
        return $this->hasMany(ChimneyProduct::class);
    }

    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function discounts(): HasManyThrough
    {
        return $this->hasManyThrough(
            Discount::class,
            Product::class
        );
    }
}
