<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    public static function getVisibilities($module_id): mixed
    {
        return self::where([['module_id', $module_id], ['role_id', Auth::user()->role_id]])->get();
    }

    /**
     * @param string $module nazwa tabeli lub modułu (zdefiniowana przy tworzeniu modułu)
     * @return int|null
     */
    public static function getModuleId(string $module): ?int
    {
        return Module::select('id')->where('name', $module)->orWhere('table_name', $module)->get()->first()->id ?? null;
    }

    /**
     * @return BelongsTo
     */
    public function modules(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'id', 'module_id');
    }


}
