<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Entities\Module;
class ColumnVisibility extends Model
{
    public $table = 'column_visibilities';

    public $fillable = [
        'name',
        'role_id',
        'module_id',
        'show',
        'hidden',
        'display_name'
    ];

    /**
     * @param $module_id
     * @return mixed
     */
    public static function getVisibilities($module_id)
    {
        return self::where([['module_id',$module_id],['role_id',\Auth::user()->role_id]])->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function modules()
    {
        return $this->belongsTo(Module::class,'id','module_id');
    }

    /**
     * @param $module nazwa tabeli lub modułu (zdefiniowana przy tworzeniu modułu)
     * @return int
     */
    public static function getModuleId($module)
    {
        return Module::select('id')->where('name',$module)->orWhere('table_name',$module)->get()->first()->id ?? null;
    }


}
