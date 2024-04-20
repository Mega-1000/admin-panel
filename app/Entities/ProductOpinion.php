<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOpinion extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'rating'
    ];
}
