<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDimensionToProductPackings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_packings', function (Blueprint $table) {
            Schema::table('product_packings', function (Blueprint $table) {
                $table->dropColumn('volume_ratio_paczkomat');
                $table->dropColumn('number_of_items_for_paczkomat');
            });

            Schema::table('product_packings', function (Blueprint $table) {
                $table->decimal('dimension_x', 15, 2);
                $table->decimal('dimension_y', 15, 2);
                $table->decimal('dimension_z', 15, 2);
            });
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
            Schema::table('product_packings', function (Blueprint $table) {
                $table->dropColumn('dimension_x');
                $table->dropColumn('dimension_y');
                $table->dropColumn('dimension_z');
            });

            Schema::table('product_packings', function (Blueprint $table) {
                $table->decimal('volume_ratio_paczkomat', 15, 4);
                $table->decimal('number_of_items_for_paczkomat', 15, 4);
            });
        });
    }
}
