<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProductPackingsChangeFieldsTypeFromIntegerToTheDecimalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_packings', function (Blueprint $table) {
            $table->decimal('numbers_of_basic_commercial_units_in_pack',15,4)->change();
            $table->decimal('number_of_sale_units_in_the_pack',15,4)->change();
            $table->decimal('number_of_trade_items_in_the_largest_unit',15,4)->change();
            $table->decimal('number_of_items_per_30_kg',15,4)->change();
            $table->decimal('number_of_pieces_in_total_volume',15,4)->change();
            $table->decimal('courier_volume_factor',15,4)->change();
            $table->decimal('max_pieces_in_one_package',15,4)->change();
            $table->decimal('number_of_items_per_25_kg',15,4)->change();
            $table->decimal('number_of_volume_items_for_paczkomat',15,4)->change();
            $table->decimal('number_of_items_for_paczkomat',15,4)->change();
            $table->decimal('volume_ratio_paczkomat',15,4)->change();
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
            //
        });
    }
}
