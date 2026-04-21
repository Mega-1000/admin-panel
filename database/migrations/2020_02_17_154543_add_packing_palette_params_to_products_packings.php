<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPackingPaletteParamsToProductsPackings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_packings', function (Blueprint $table) {
            $table->decimal('max_in_pallete_80', 15, 2);
            $table->decimal('max_in_pallete_100', 15, 2);
            $table->decimal('per_package_factor', 15, 2);
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
            $table->dropColumn('max_in_pallete_80');
            $table->dropColumn('max_in_pallete_100');
            $table->dropColumn('per_package_factor');
        });
    }
}
