<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
/**
 * Class CreateOrderMessagesTable.
 */
class CreateOrderMessagesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(): void
	{
		Schema::create('order_messages', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('employee_id')->comment('Null jeśli klient')->nullable();
            $table->string('title')->comment('Temat wiadomości');
            $table->text('message')->comment('Treść wiadomości');
            $table->enum('type', ['GENERAL', 'SHIPPING', 'WAREHOUSE', 'COMPLAINT'])->comment('Ogólne, spedycja, magazyn, reklamacja');
            $table->enum('status', ['OPEN', 'CLOSED']);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(): void
	{
		Schema::drop('order_messages');
	}
}
