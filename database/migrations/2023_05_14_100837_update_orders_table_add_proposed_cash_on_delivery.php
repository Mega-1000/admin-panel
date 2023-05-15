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
    public function up()
    {
        //add update_orders_table_add_proposed_cash_on_delivery
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('proposed_cash_on_delivery')->default(20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('proposed_cash_on_delivery');
        });
    }
};
