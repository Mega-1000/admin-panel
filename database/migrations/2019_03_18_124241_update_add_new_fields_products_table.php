<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddNewFieldsProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->date('date_of_price_change')->nullable()->comment('Następna data zapytania o cenę');
            $table->date('date_of_the_new_prices')->nullable()->comment('Data od kiedy obowiązują nowe ceny');
            $table->string('product_group_for_change_price')->nullable()->comment('Grupa produktów');
            $table->string('products_related_to_the_automatic_price_change')->nullable()->comment('Produkty powiązane z automatyczną zmianą ceny');
            $table->text('text_price_change')->nullable()->comment('Tekst zmiana ceny');
            $table->text('text_price_change_data_first')->nullable()->comment('Tekst do kolumny Automatyczna zmiana cen dana 1');
            $table->text('text_price_change_data_second')->nullable()->comment('Tekst do kolumny Automatyczna zmiana cen dana 2');
            $table->text('text_price_change_data_third')->nullable()->comment('Tekst do kolumny Automatyczna zmiana cen dana 3');
            $table->text('text_price_change_data_fourth')->nullable()->comment('Tekst do kolumny Automatyczna zmiana cen dana 4');
            $table->boolean('subject_to_price_change')->default(false)->comment('Czy podczas importu podlega automatycznej zmiany ceny');
            $table->text('value_of_price_change_data_first')->nullable()->comment('Wartość pola Automatyczna zmiana cen dana 1');
            $table->text('value_of_price_change_data_second')->nullable()->comment('Wartość pola Automatyczna zmiana cen dana 2');
            $table->text('value_of_price_change_data_third')->nullable()->comment('Wartość pola Automatyczna zmiana cen dana 3');
            $table->text('value_of_price_change_data_fourth')->nullable()->comment('Wartość pola Automatyczna zmiana cen dana 4');
            $table->string('pattern_to_set_the_price')->nullable()->comment('Wzór do ustalenia ceny za jednostkę wskazaną');

            $table->dropColumn('price_change_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
