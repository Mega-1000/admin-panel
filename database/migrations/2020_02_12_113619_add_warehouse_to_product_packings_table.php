<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWarehouseToProductPackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_packings', function (Blueprint $table) {
            $table->dropColumn('number_of_items_per_30_kg');
            $table->dropColumn('courier_volume_factor');
        });

        Schema::table('product_packings', function (Blueprint $table) {
            $table->string('warehouse', 25);
            $table->string('packing_name', 25);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_packings', function (Blueprint $table) {
            $table->dropColumn('warehouse');
            $table->dropColumn('packing_name');
        });
        Schema::table('product_packings', function (Blueprint $table) {
            $table->decimal('number_of_items_per_30_kg', 15, 4);
            $table->decimal('courier_volume_factor', 15, 4);
        });
    }
}
