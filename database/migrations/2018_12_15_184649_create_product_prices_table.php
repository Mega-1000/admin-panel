<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateProductPricesTable.
 */
class CreateProductPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->decimal('net_purchase_price_commercial_unit', 9,
                2)->nullable()->comment('Cena zakupowa netto jednostki handlowej');
            $table->decimal('net_purchase_price_commercial_unit_after_discounts', 9,
                2)->nullable()->comment('Cena zakupowa netto jednostki handlowej po rabatach');
            $table->decimal('net_special_price_commercial_unit', 9,
                2)->nullable()->comment('Cena specjalna netto zakupu jednostki handlowej');
            $table->decimal('net_purchase_price_basic_unit', 9,
                2)->nullable()->comment('Cena zakupowa netto jednostki podstawowej');
            $table->decimal('net_purchase_price_basic_unit_after_discounts', 9,
                2)->nullable()->comment('Cena zakupowa netto po rabatach');
            $table->decimal('net_special_price_basic_unit', 9,
                2)->nullable()->comment('Cena specjalna netto zakupu jednostki podstawowej');
            $table->decimal('net_purchase_price_calculated_unit', 9,
                2)->nullable()->comment('Cena zakupowa netto jednostki obliczeniowej');
            $table->decimal('net_purchase_price_calculated_unit_after_discounts', 9,
                2)->nullable()->comment('Cena zakupowa netto jednostki obliczeniowej po rabatach');
            $table->decimal('net_special_price_calculated_unit', 9,
                2)->nullable()->comment('Cena specjalna netto jednostki obliczeniowej');
            $table->decimal('gross_purchase_price_aggregate_unit', 9,
                2)->nullable()->comment('Cena zakupowa brutto jednostki zbiorczej');
            $table->decimal('gross_purchase_price_aggregate_unit_after_discounts', 9,
                2)->nullable()->comment('Cena zakupowa brutto jednostki zbiorczej po rabatach');
            $table->decimal('gross_special_price_aggregate_unit', 9,
                2)->nullable()->comment('Cena specjalna brutto jednostki zbiorczej');
            $table->decimal('gross_purchase_price_the_largest_unit', 9,
                2)->nullable()->comment('Cena zakupowa brutto jednostki największej');
            $table->decimal('gross_purchase_price_the_largest_unit_after_discounts', 9,
                2)->nullable()->comment('Cena zakupowa brutto jednostki największej po rabatach');
            $table->decimal('gross_special_price_the_largest_unit', 9,
                2)->nullable()->comment('Cena specjalna brutto jednostki największej');
            $table->decimal('net_selling_price_commercial_unit', 9,
                2)->nullable()->comment('Cena sprzedaży netto jednostki handlowej');
            $table->decimal('net_selling_price_basic_unit', 9,
                2)->nullable()->comment('Cena sprzedaży netto jednostki podstawowej');
            $table->decimal('net_selling_price_calculated_unit', 9,
                2)->nullable()->comment('Cena sprzedaży netto jednostki obliczeniowej');
            $table->decimal('net_selling_price_aggregate_unit', 9,
                2)->nullable()->comment('Cena sprzedaży netto jednostki zbiorczej');
            $table->decimal('net_selling_price_the_largest_unit', 9,
                2)->nullable()->comment('Cena sprzedaży netto jednostki największej');
            $table->decimal('discount1', 9, 2)->nullable()->comment('Rabat 1');
            $table->decimal('discount2', 9, 2)->nullable()->comment('Rabat 2');
            $table->decimal('discount3', 9, 2)->nullable()->comment('Rabat 3');
            $table->decimal('bonus1', 9, 2)->nullable();
            $table->decimal('bonus2', 9, 2)->nullable();
            $table->decimal('bonus3', 9, 2)->nullable();
            $table->decimal('gross_price_of_packing', 9, 2)->nullable()->comment('Cena brutto opakowania');
            $table->decimal('table_price', 9, 2)->nullable()->comment('Cena tabelaryczna');
            $table->integer('vat');
            $table->decimal('additional_payment_for_milling', 9, 2)->nullable()->comment('Dopłata za frezowanie');
            $table->float('coating')->nullable()->comment('Narzut');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('product_prices');
    }
}
