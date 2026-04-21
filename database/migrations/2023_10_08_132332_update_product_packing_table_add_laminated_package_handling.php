<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('product_packings', function (Blueprint $table) {
            $table->float('number_of_trade_items_in_p1')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('product_packing', function (Blueprint $table) {
            $table->dropColumn('number_of_trade_items_in_p1');
        });
    }
};
