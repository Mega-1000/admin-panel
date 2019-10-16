<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddGrossPricesProductPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->double('gross_purchase_price_basic_unit', 8, 4)->nullable();
            $table->double('gross_purchase_price_basic_unit_after_discounts', 8, 4)->nullable();
            $table->double('gross_special_price_basic_unit', 8, 4)->nullable();
            $table->double('gross_purchase_price_commercial_unit', 8, 4)->nullable();
            $table->double('gross_purchase_price_commercial_unit_after_discounts', 8, 4)->nullable();
            $table->double('gross_special_price_commercial_unit', 8, 4)->nullable();
            $table->double('gross_purchase_price_calculated_unit', 8, 4)->nullable();
            $table->double('gross_purchase_price_calculated_unit_after_discounts', 8, 4)->nullable();
            $table->double('gross_special_price_calculated_unit', 8, 4)->nullable();
            $table->double('gross_purchase_price_aggregate_unit', 8, 4)->nullable();
            $table->double('gross_purchase_price_aggregate_unit_after_discounts', 8, 4)->nullable();
            $table->double('gross_special_price_aggregate_unit', 8, 4)->nullable();
            $table->double('gross_purchase_price_the_largest_unit', 8, 4)->nullable();
            $table->double('gross_purchase_price_the_largest_unit_after_discounts', 8, 4)->nullable();
            $table->double('gross_special_price_the_largest_unit', 8, 4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
