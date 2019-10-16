<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateProductStockLogsTable.
 */
class CreateProductStockLogsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product_stock_logs', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_stock_id');
            $table->unsignedInteger('product_stock_position_id');
            $table->enum('action', ['ADD', 'DELETE']);
            $table->integer('quantity');
            $table->unsignedInteger('user_id');
            $table->timestamps();

            $table->foreign('product_stock_id')->references('id')->on('product_stocks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('product_stock_logs');
	}
}
