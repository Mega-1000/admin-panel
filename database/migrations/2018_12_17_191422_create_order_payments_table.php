<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateOrderPaymentsTable.
 */
class CreateOrderPaymentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_payments', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->decimal('amount', 9, 2)->comment('Kwota wpłacona')->nullable();
            $table->text('notices')->comment('Dodatkowe uwagi do wpłaty')->nullable();
            $table->timestamps();

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
		Schema::drop('order_payments');
	}
}
