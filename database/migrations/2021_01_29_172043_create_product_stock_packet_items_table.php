<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductStockPacketItemsTable extends Migration
{
    public function up(): void
    {
        Schema::create('product_stock_packet_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('product_stock_packet_id');
            $table->unsignedInteger('quantity');
            $table->timestamps();

            $table->foreign('product_id')->references('id')
                ->on('products')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('product_stock_packet_id')->references('id')
                ->on('product_stock_packets')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stock_packet_items');
    }
}
