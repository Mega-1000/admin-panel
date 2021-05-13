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
            $table->dateTime('customer_shipment_date_from')->nullable();
            $table->dateTime('customer_shipment_date_to')->nullable();
            $table->dateTime('customer_delivery_date_from')->nullable();
            $table->dateTime('customer_delivery_date_to')->nullable();
            $table->dateTime('consultant_shipment_date_from')->nullable();
            $table->dateTime('consultant_shipment_date_to')->nullable();
            $table->dateTime('consultant_delivery_date_from')->nullable();
            $table->dateTime('consultant_delivery_date_to')->nullable();
            $table->dateTime('warehouse_shipment_date_from')->nullable();
            $table->dateTime('warehouse_shipment_date_to')->nullable();
            $table->dateTime('warehouse_delivery_date_from')->nullable();
            $table->dateTime('warehouse_delivery_date_to')->nullable();
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
