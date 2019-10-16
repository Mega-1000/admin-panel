<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateProductStockPositionsTable.
 */
class CreateProductStockPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stock_positions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_stock_id');
            $table->string('lane')->nullable()->comment('Alejka')->nullable();
            $table->string('bookstand')->nullable()->comment('Regał')->nullable();
            $table->string('shelf')->nullable()->comment('Półka')->nullable();
            $table->string('position')->nullable()->comment('Pozycja')->nullable();
            $table->timestamps();

            $table->foreign('product_stock_id')->references('id')->on('product_stocks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('product_stock_positions');
    }
}
