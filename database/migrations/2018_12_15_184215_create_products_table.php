<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateProductsTable.
 */
class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->index()->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->string('symbol');
            $table->string('name');
            $table->integer('multiplier_of_the_number_of_pieces')->nullable()->comment('Mnożnik ilości sztuk');
            $table->string('url')->nullable();
            $table->float('weight_trade_unit')->nullable()->comment('Waga jednostki handlowej');
            $table->float('weight_collective_unit')->nullable()->comment('Waga jednostki zbiorczej');
            $table->float('weight_biggest_unit')->nullable()->comment('Waga jednostki największej');
            $table->float('weight_base_unit')->nullable()->comment('Waga jednostki podstawowej');
            $table->text('description')->nullable();
            $table->string('video_url')->nullable()->comment('Film');
            $table->string('manufacturer_url')->nullable()->comment('Link strony"');
            $table->integer('priority')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->enum('status', ['ACTIVE', 'PENDING'])->nullable();
            $table->text('description_photo_promoted')->nullable()->comment('Opis zdjęcie polecamy');
            $table->text('description_photo_table')->nullable()->comment('Opis zdjęcie tabela');
            $table->text('description_photo_contact')->nullable()->comment('Opis zdjęcie kontakt');
            $table->text('description_photo_details')->nullable()->comment('Opis zdjęcie szczegóły');
            $table->text('set_symbol')->nullable()->comment('Symbol kompletu');//???
            $table->string('set_rule')->nullable()->comment('Reguła kompletu');//???
            $table->string('manufacturer')->nullable()->comment('producent');
            $table->text('additional_info1')->nullable()->comment('Uwagi 1');
            $table->text('additional_info2')->nullable()->comment('Uwagi 2');
            $table->string('product_symbol_on_collective_box')->nullable()->comment('Symbol produktu na opakowaniu zbiorczym');
            $table->string('product_name_on_collective_box')->nullable()->comment('Nazwa produktu na opakowaniu zbiorczym');
            $table->string('product_name_supplier')->nullable()->comment('Nazwa dostawcy');
            $table->string('product_name_supplier_on_documents')->nullable()->comment('Nazwa produktu u dostawcy na dokumentach');
            $table->string('product_symbol_on_supplier_documents')->nullable()->comment('Symbol towaru u dostawcy na dokumentach');
            $table->string('product_name_manufacturer')->nullable()->comment('Nazwa towaru producenta');
            $table->string('symbol_name_manufacturer')->nullable()->comment('Symbol towaru producenta');
            $table->string('pricelist_name')->nullable()->comment('Nazwa cennika');
            $table->string('calculator_type')->nullable()->comment('Rodzaj kalkulatora na stronie');//???
            $table->string('product_url')->nullable()->comment('url_kat1 + url_kat2 + url_kat3 + url_kat4 + url_kat5 ze starej bazy');
            $table->string('product_group')->nullable()->comment('Grupowanie produktów dla przestawienia oferty[wariacji]');//???
            $table->dateTime('price_change_date')->nullable()->comment('Data zmiany ceny dla produktu potrzebna do przypomnienia fabryce o konieczności zmiany ceny');
            $table->string('url_for_website')->nullable()->comment('Url do zdjec po translacji na sciezke serwerowa a nie lokalna');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('products');
    }
}
