<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChimneyTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chimney_attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_detail_id')->unsigned();
            $table->string('name');
            $table->foreign('category_detail_id')->references('id')->on('category_details');
        });

        Schema::create('chimney_attributes_options', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('chimney_attribute_id')->unsigned();
            $table->string('name');
            $table->foreign('chimney_attribute_id')->references('id')->on('chimney_attributes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chimney_attributes');
        Schema::dropIfExists('chimney_attributes_options');
    }
}
