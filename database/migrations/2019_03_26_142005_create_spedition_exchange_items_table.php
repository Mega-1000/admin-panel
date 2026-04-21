<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateSpeditionExchangeItemsTable.
 */
class CreateSpeditionExchangeItemsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('spedition_exchange_items', function(Blueprint $table) {
            $table->increments('id');
            $table->boolean('invoiced');
            $table->unsignedInteger('spedition_exchange_id');
            $table->unsignedInteger('order_id');

            $table->foreign('spedition_exchange_id')->references('id')->on('spedition_exchanges')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('spedition_exchange_items');
	}
}
