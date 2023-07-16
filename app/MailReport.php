<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'subject',
        'body',
    ];
}
