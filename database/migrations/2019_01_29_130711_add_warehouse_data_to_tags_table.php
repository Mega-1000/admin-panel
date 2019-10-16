<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWarehouseDataToTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tag = \App\Entities\Tag::where('name', '[DANE-MAGAZYNU]')->first();
        if (empty($tag)) {
            \App\Entities\Tag::create([
                'name' => '[DANE-MAGAZYNU]',
                'handler' => 'warehouseData',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
