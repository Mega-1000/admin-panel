<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    public $table ="modules";

    public $fillable =[
        'name',
        'column_name',
        'model_path'

    ];

    /**
     * @var array zmienna którą należy zdefiniować aby dodać możliwość wycinania dodatkowych kolumn poza tymi które są w tablicy w bazie
     *
     */
    public  $customColumnsVisibilities = [

    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function visibilities()
    {
        return $this->hasMany(ColumnVisibility::class,'module_id','id');
    }



}
