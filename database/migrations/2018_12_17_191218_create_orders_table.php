'<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateOrdersTable.
 */
class CreateOrdersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(): void
    {
		Schema::create('orders', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('status_id');
            $table->dateTime('last_status_update_date')->comment('Status data zmiany');
            $table->decimal('total_price', 9, 2)->comment('Suma zamówienia');
            $table->float('weight')->comment('Waga zamówienia');
            $table->decimal('shipment_price')->comment('Koszt wysyłki');
            $table->string('customer_notices');
            $table->decimal('cash_on_delivery_amount')->comment('Kwota pobrania');
            $table->integer('allegro_transaction_id')->nullable();
            $table->unsignedInteger('employee_id')->comment('Przypisany pracownik do zamówienia, w praktyce jest nim konsultant');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('orders');
	}
}
