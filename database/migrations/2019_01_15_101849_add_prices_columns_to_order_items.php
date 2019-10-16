<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPricesColumnsToOrderItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('net_purchase_price_commercial_unit', 9,
                2)->nullable()->comment('Cena zakupowa netto jednostki handlowej');
            $table->decimal('net_purchase_price_basic_unit', 9,
                2)->nullable()->comment('Cena zakupowa netto jednostki podstawowej');
            $table->decimal('net_purchase_price_calculated_unit', 9,
                2)->nullable()->comment('Cena zakupowa netto jednostki obliczeniowej');
            $table->decimal('net_purchase_price_aggregate_unit', 9,
                2)->nullable()->comment('Cena zakupowa netto jednostki zbiorczej');
            $table->decimal('net_purchase_price_the_largest_unit', 9,
                2)->nullable()->comment('Cena zakupowa netto jednostki największej');
            $table->decimal('net_selling_price_commercial_unit', 9,
                2)->nullable()->comment('Cena sprzedaży netto jednostki handlowej');
            $table->decimal('net_selling_price_basic_unit', 9,
                2)->nullable()->comment('Cena sprzedaży netto jednostki podstawowej');
            $table->decimal('net_selling_price_calculated_unit', 9,
                2)->nullable()->comment('Cena sprzedaży netto jednostki obliczeniowej');
            $table->decimal('net_selling_price_aggregate_unit', 9,
                2)->nullable()->comment('Cena sprzedaży netto jednostki zbiorczej');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            //
        });
    }
}
