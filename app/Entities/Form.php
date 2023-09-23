<?php

namespace App\Entities;

use App\FormElement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property string $name
 * @property string $description
 */
class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function elements(): HasMany
    {
        return $this->hasMany(FormElement::class)->orderBy('order');
    }
}
