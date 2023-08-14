<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $title
 * @property string $content
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 */
class FastResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
    ];
}
