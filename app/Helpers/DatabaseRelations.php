<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

class DatabaseRelations extends Migration
{
    protected array $relations = [];

    public function upRelations()
    {
        foreach ($this->getRelations() as $table => $columns) {
            Schema::table($table, function (Blueprint $table) use ($columns) {
                foreach ($columns as $column => $data) {
                    if ($data['nullable']) {
                        $table->integer($column)->unsigned()->nullable();
                    } else {
                        $table->integer($column)->unsigned();
                    }
                    if ($data['foreign']) {
                        $table->foreign($column)->references('id')->on($data['table']);
                    } else {
                        $table->index($column);
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function downRelations(): void
    {
        foreach ($this->getRelations() as $tableName => $columns) {
            Schema::table($tableName, function (Blueprint $table) use ($columns) {
                foreach ($columns as $column => $data) {
                    if ($data['foreign']) {
                        $table->dropForeign([$column]);
                    }
                }
            });
        }
    }

    private function getRelations()
    {
        $out = [];

        foreach ($this->relations as $table => $row) {
            $a = [];
            foreach ($row as $key => $value) {
                $a[(is_string($key) ? $key : $value).'_id'] = [
                    'table' => is_string($key) && isset($value['table']) ? $value['table'] : Str::plural(is_string($key) ? $key : $value),
                    'nullable' => is_string($key) && isset($value['nullable']) && $value['nullable'],
                    'foreign' => is_string($key) && isset($value['foreign']) ? $value['foreign'] : true
                ];
            }
            $out[$table] = $a;
        }

        return $out;
    }
}
