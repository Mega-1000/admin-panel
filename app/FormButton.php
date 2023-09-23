<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormButton extends Model
{
    use HasFactory;

    public $fillable = [
        'text',
        'color',
        'size',
        'form_element_id',
    ];
}
