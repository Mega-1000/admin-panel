<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
/**
 * Class CreateOrderPackagesTable.
 */
class CreateOrderPackagesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_packages', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->string('size_a')->nullable();
            $table->string('size_b')->nullable();
            $table->string('size_c')->nullable();
            $table->date('shipment_date')->comment('Data wysyłki');
            $table->date('delivery_date')->comment('Data dostarczenia przesyłki')->nullable();
            $table->string('courier_name')->comment('Nazwa kuriera');
            $table->float('weight');
            $table->decimal('cash_on_delivery', 9, 2)->comment('Kwota pobrania')->nullable();
            $table->text('notices')->comment('Uwagi dla kuriera')->nullable();
            $table->enum('status', ['DELIVERED', 'CANCALLED']);//To są przykładowe statusy, trzeba się zastanowić co tu umieścić
            $table->string('sending_number')->comment('Numer nadania')->nullable();
            $table->string('letter_number')->comment('Numer listu przewozowego')->nullable();
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
		Schema::drop('order_packages');
	}
}
