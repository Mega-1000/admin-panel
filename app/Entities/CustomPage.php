<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class CustomPage extends Model
{

    protected $table = 'custom_page_content';

    public function category()
    {
        return $this->belongsTo('App\Entities\CustomPageCategory', 'category_id', 'id');
    }

}
