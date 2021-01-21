<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrderItemsTableAddProductStockPacketRelation extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedInteger('product_stock_packet_id')->nullable();
            $table->foreign('product_stock_packet_id')
                ->references('id')
                ->on('product_stock_packets');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign('product_stock_packet_id');
            $table->dropColumn('product_stock_packet_id');
        });
    }
}
