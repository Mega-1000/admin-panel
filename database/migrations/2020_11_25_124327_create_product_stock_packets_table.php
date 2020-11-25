<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductStockPacketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stock_packets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_stock_id');
            $table->foreign('product_stock_id')
                ->references('id')
                ->on('product_stocks');
            $table->unsignedInteger('order_item_id')->nullable();
            $table->foreign('order_item_id')
                ->references('id')
                ->on('order_items');
            $table->string('packet_name');
            $table->unsignedInteger('packet_quantity');
            $table->unsignedInteger('packet_product_quantity');
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
        Schema::dropIfExists('product_stock_packets');
    }
}
