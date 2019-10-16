<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToSpeditionExchangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('spedition_exchanges', function (Blueprint $table) {
            $table->foreign('chosen_spedition_offer_id')->references('id')->on('spedition_exchange_offers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('spedition_exchanges', function (Blueprint $table) {
            //
        });
    }
}
