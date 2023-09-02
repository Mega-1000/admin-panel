<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterMessage extends Model
{
    use HasFactory;

    public $fillable = [
        'title',
        'subject',
        'content',
        'status',
        'type',
    ];

}
