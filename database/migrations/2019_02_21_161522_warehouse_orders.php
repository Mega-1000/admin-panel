<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WarehouseOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_orders', function(Blueprint $table) {
            $table->increments('id');
            $table->text('symbol')->nullable();
            $table->dateTime('shipment_date')->nullable();
            $table->dateTime('confirmation_date')->nullable();
            $table->text('company')->nullable();
            $table->text('email')->nullable();
            $table->text('confirmation')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('arrival_date')->nullable();
            $table->text('status')->nullable();
            $table->unsignedInteger('warehouse_id')->nullable();
            $table->dateTime('consultant_comment_date')->nullable();
            $table->text('consultant_comment')->nullable();
            $table->text('warehouse_comment')->nullable();
            $table->text('comments_for_warehouse')->nullable();
            $table->timestamps();

            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
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
