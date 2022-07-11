<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['name', 'iso2'];
}
