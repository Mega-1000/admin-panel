<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterGuide extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'description',
    ];
}
