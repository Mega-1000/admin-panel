<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_dates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->dateTime('customer_preferred_shipment_date')->nullable();
            $table->dateTime('customer_preferred_delivery_date')->nullable();
            $table->dateTime('consultant_preferred_shipment_date')->nullable();
            $table->dateTime('consultant_preferred_delivery_date')->nullable();
            $table->dateTime('warehouse_preferred_shipment_date')->nullable();
            $table->dateTime('warehouse_preferred_delivery_date')->nullable();
            $table->boolean('customer_acceptance')->default(0);
            $table->boolean('consultant_acceptance')->default(0);
            $table->boolean('warehouse_acceptance')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_dates');
    }
}
