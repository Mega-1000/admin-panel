<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormElement extends Model
{
    use HasFactory;

    public $fillable = [
        'type',
        'text',
        'label',
        'placeholder',
        'required',
        'form_id',
        'order',
        'color',
        'size',
        'action',
        'new_tab',
    ];
}
