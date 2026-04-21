<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePricePrecisionsInProductPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->decimal('net_purchase_price_basic_unit', 9,4)->change();
            $table->decimal('net_purchase_price_basic_unit_after_discounts', 9,4)->change();
            $table->decimal('net_special_price_basic_unit', 9,4)->change();
            $table->decimal('net_purchase_price_calculated_unit', 9, 4)->change();
            $table->decimal('net_purchase_price_calculated_unit_after_discounts', 9, 4)->change();
            $table->decimal('net_special_price_calculated_unit', 9, 4)->change();
            $table->decimal('net_purchase_price_aggregate_unit', 9, 4)->change();
            $table->decimal('net_purchase_price_aggregate_unit_after_discounts', 9, 4)->change();
            $table->decimal('net_special_price_aggregate_unit', 9, 4)->change();
            $table->decimal('net_purchase_price_the_largest_unit', 9, 4)->change();
            $table->decimal('net_purchase_price_the_largest_unit_after_discounts', 9, 4)->change();
            $table->decimal('net_special_price_the_largest_unit', 9, 4)->change();
            $table->decimal('net_selling_price_basic_unit', 9, 4)->change();
            $table->decimal('net_selling_price_calculated_unit', 9, 4)->change();
            $table->decimal('net_selling_price_aggregate_unit', 9, 4)->change();
            $table->decimal('net_selling_price_the_largest_unit', 9, 4)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_prices', function (Blueprint $table) {
            //
        });
    }
}
