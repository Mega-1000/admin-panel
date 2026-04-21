<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColumnsNameProductPrices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->renameColumn('gross_purchase_price_aggregate_unit', 'net_purchase_price_aggregate_unit');
            $table->renameColumn('gross_purchase_price_aggregate_unit_after_discounts', 'net_purchase_price_aggregate_unit_after_discounts');
            $table->renameColumn('gross_special_price_aggregate_unit', 'net_special_price_aggregate_unit');
            $table->renameColumn('gross_purchase_price_the_largest_unit', 'net_purchase_price_the_largest_unit');
            $table->renameColumn('gross_purchase_price_the_largest_unit_after_discounts', 'net_purchase_price_the_largest_unit_after_discounts');
            $table->renameColumn('gross_special_price_the_largest_unit', 'net_special_price_the_largest_unit');
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
