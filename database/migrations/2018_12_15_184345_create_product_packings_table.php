<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateProductPackingsTable.
 */
class CreateProductPackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_packings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->string('calculation_unit')->nullable()->comment('Jednostka obliczeniowa'); //?? ja bym dał tu pewnie enum
            $table->string('unit_consumption')->nullable()->comment('Zużycie jednostki');
            $table->string('unit_commercial')->nullable()->comment('Jednostka miary handlowej');//??
            $table->string('unit_basic')->nullable()->comment('Podstawowa jednostka miary');//??
            $table->string('unit_of_collective')->nullable()->comment('Jednostka zbiorcza miary');//??
            $table->string('unit_biggest')->nullable()->comment('Największa jednostka');//??
            $table->integer('numbers_of_basic_commercial_units_in_pack')->nullable()->comment('Ilość jednostek podstawowych w opakowaniu handlowym');
            $table->integer('number_of_sale_units_in_the_pack')->nullable()->comment('Ilość jednostek handlowych w opakowaniu zbiorczym');
            $table->integer('number_of_trade_items_in_the_largest_unit')->nullable()->comment('Ilość jednostek handlowych w jednostce największej');
            $table->string('ean_of_commercial_packing')->nullable()->comment('Kod kreskowy opakowania handlowego');
            $table->string('ean_of_collective_packing')->nullable()->comment('Kod kreskowy opakowania zbiorczego');
            $table->string('ean_of_biggest_packing')->nullable()->comment('Kod kreskowy opakowania największego');
            $table->integer('number_of_items_per_30_kg')->nullable()->comment('Ilość sztuk na 30kg');
            $table->string('packing_type')->nullable()->comment('Rodzaj opakowania');
            $table->integer('number_of_pieces_in_total_volume')->nullable()->comment('Ilość sztuk w całkowitej objętości');
            $table->string('recommended_courier')->nullable()->comment('Zalecany kurier');
            $table->float('courier_volume_factor')->nullable()->comment('Współczynnik objętości kurier');
            $table->integer('max_pieces_in_one_package')->nullable()->comment('Maksymalna ilość danego asortymentu w paczce');
            $table->integer('number_of_items_per_25_kg')->nullable()->comment('Ilość sztuk na 25kg');
            $table->integer('number_of_volume_items_for_paczkomat')->nullable()->comment('Ilość sztuk w całkowitej objętości dla paczkomatu');
            $table->integer('number_of_items_for_paczkomat')->nullable()->comment('Maksymalna ilość danego asortymentu w paczce do paczkomatu');
            $table->string('inpost_courier_type')->nullable()->comment('Rodzaj kuriera inpost');
            $table->float('volume_ratio_paczkomat')->nullable()->comment('Wspołczynnik objętości paczkomat');
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
        Schema::drop('product_packings');
    }
}
