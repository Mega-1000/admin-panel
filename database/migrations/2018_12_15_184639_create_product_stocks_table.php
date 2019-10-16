<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateProductStocksTable.
 */
class CreateProductStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->integer('quantity')->comment('Stan magazynowy');
            $table->integer('min_quantity')->nullable()->comment('Minimalny stan magazynowy')->nullable();
            $table->string('unit')->nullable()->comment('Jednostka w jakiej pokazujemy stan minimalny')->nullable();
            $table->integer('start_quantity')->nullable()->comment('Początkowy stan magazynowy')->nullable();
            $table->integer('number_on_a_layer')->nullable()->comment('Ilość na warstwie')->nullable();
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
        Schema::drop('product_stocks');
    }
}
