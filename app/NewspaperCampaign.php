<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewspaperCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email_value',
        'email_title',
    ];
}
