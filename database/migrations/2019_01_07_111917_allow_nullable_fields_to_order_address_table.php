<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AllowNullableFieldsToOrderAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->enum('type', ['INVOICE_ADDRESS', 'DELIVERY_ADDRESS'])->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->enum('type', ['STANDARD_ADDRESS', 'INVOICE_ADDRESS', 'DELIVERY_ADDRESS'])->change();
        });
    }
}
