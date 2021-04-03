<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProductStockPacketsRemoveStockAndQuantityColumns extends Migration
{
    public function up(): void
    {
        Schema::table('product_stock_packets', function (Blueprint $table) {
            $table->dropForeign(['product_stock_id']);
            $table->dropColumn('product_stock_id');
            $table->dropColumn('packet_product_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('product_stock_packets', function (Blueprint $table) {
            $table->unsignedInteger('product_stock_id');
            $table->foreign('product_stock_id')
                ->references('id')
                ->on('product_stocks');
            $table->unsignedInteger('packet_product_quantity');
        });
    }
}
