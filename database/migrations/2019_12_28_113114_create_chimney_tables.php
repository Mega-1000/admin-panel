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
            $table->foreign('category_detail_id')->references('id')->on('category_details');
            $table->string('name');
            $table->integer('column_number');
            $table->timestamps();
        });

        Schema::create('chimney_attribute_options', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('chimney_attribute_id')->unsigned();
            $table->foreign('chimney_attribute_id')->references('id')->on('chimney_attributes');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('chimney_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_detail_id')->unsigned();
            $table->foreign('category_detail_id')->references('id')->on('category_details');
            $table->string('product_code');
            $table->string('formula');
            $table->integer('column_number')->default(0);
            $table->integer('optional')->default(0);
            $table->string('replacement_description')->nullable();
            $table->string('replacement_img')->nullable();
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
        Schema::dropIfExists('chimney_products');
        Schema::dropIfExists('chimney_attribute_options');
        Schema::dropIfExists('chimney_attributes');
    }
}
