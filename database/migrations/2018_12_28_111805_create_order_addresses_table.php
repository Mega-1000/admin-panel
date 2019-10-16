<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateOrderAddressesTable.
 */
class CreateOrderAddressesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_addresses', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->enum('type', ['STANDARD_ADDRESS', 'INVOICE_ADDRESS', 'DELIVERY_ADDRESS']);
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('firmname')->nullable();
            $table->string('nip')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('flat_number')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('email')->nullable();
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
		Schema::drop('order_addresses');
	}
}
