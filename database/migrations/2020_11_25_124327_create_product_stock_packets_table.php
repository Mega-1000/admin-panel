<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductStockPacketsTable extends Migration
{
    public function up(): void
    {
        Schema::create('product_stock_packets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_stock_id');
            $table->foreign('product_stock_id')
                ->references('id')
                ->on('product_stocks');
            $table->string('packet_name');
            $table->unsignedInteger('packet_quantity');
            $table->unsignedInteger('packet_product_quantity');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('product_stock_packets', function (Blueprint $table) {
            $table->dropForeign('product_stock_id');
        });
        Schema::dropIfExists('product_stock_packets');
    }
}
