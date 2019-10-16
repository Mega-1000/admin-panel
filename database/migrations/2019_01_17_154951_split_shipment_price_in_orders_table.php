<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SplitShipmentPriceInOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('shipment_price_for_us')->comment('Koszt wysyłki dla firmy')->after('shipment_price')->nullable();
            $table->renameColumn('shipment_price', 'shipment_price_for_client');
            $table->decimal('shipment_price')->comment('Koszt wysyłki dla dla')->change();
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
            //
        });
    }
}
