<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChimneyReplacementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chimney_replacements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('chimney_product_id')->unsigned();
            $table->foreign('chimney_product_id')->references('id')->on('chimney_products');
            $table->string('product');
            $table->string('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chimney_replacements');
    }
}
