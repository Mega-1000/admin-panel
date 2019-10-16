<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddNewVariationFieldsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('variation_group')->nullable();
            $table->string('review')->nullable();
            $table->string('quality')->nullable();
            $table->string('quality_to_price')->nullable();
            $table->string('comments')->nullable();
            $table->string('value_of_the_order_for_free_transport')->nullable()->comment('Wartość zamóienia u danego producenta aby otrzymać darmowy transport');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
