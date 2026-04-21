<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrderItemsTableAddNewFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('net_purchase_price_commercial_unit_after_discounts', 9, 2)->nullable();
            $table->decimal('net_purchase_price_basic_unit_after_discounts', 9, 2)->nullable();
            $table->decimal('net_purchase_price_calculated_unit_after_discounts', 9, 2)->nullable();
            $table->decimal('net_purchase_price_aggregate_unit_after_discounts', 9, 2)->nullable();
            $table->decimal('net_purchase_price_the_largest_unit_after_discounts', 9, 2)->nullable();
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
