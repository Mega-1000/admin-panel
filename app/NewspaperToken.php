<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $token
 * @property int $user_id
 * @property User $user
 * @property string $categories
 * @property string $created_at
 * @property string $updated_at
 */
class NewspaperToken extends Model
{
    use HasFactory;

    public $fillable = [
        'token',
        'user_id',
        'categories',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
