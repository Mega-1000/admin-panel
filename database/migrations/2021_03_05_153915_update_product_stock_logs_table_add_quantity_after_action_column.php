<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProductStockLogsTableAddQuantityAfterActionColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_stock_logs', function (Blueprint $table) {
            $table->integer('stock_quantity_after_action')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_stock_logs', function (Blueprint $table) {
            $table->dropColumn('stock_quantity_after_action');
        });
    }
}
