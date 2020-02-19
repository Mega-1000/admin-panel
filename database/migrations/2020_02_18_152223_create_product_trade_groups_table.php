<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductTradeGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_trade_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', ['price', 'weight']);
            $table->integer('product_id');
            $table->decimal('first_condition', 12, 2);
            $table->decimal('first_price', 12, 2);
            $table->decimal('second_condition', 12, 2);
            $table->decimal('second_price', 12, 2);
            $table->decimal('third_condition', 12, 2);
            $table->decimal('third_price', 12, 2);
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
        Schema::dropIfExists('product_trade_groups');
    }
}
