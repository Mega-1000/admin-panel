<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePricePrecisionsInOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('net_purchase_price_basic_unit', 9,4)->change();
            $table->decimal('net_purchase_price_calculated_unit', 9,4)->change();
            $table->decimal('net_purchase_price_aggregate_unit', 9,4)->change();
            $table->decimal('net_purchase_price_the_largest_unit', 9,4)->change();
            $table->decimal('net_selling_price_basic_unit', 9,4)->change();
            $table->decimal('net_selling_price_calculated_unit', 9,4)->change();
            $table->decimal('net_selling_price_aggregate_unit', 9,4)->change();
            $table->decimal('net_selling_price_the_largest_unit', 9,4)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            //
        });
    }
}
