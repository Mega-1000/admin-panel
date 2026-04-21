<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateSpeditionExchangeOffersTable.
 */
class CreateSpeditionExchangeOffersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('spedition_exchange_offers', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('spedition_exchange_id');
            $table->text('firm_name');
            $table->text('street');
            $table->text('number');
            $table->text('postal_code');
            $table->text('city');
            $table->text('nip');
            $table->text('account_number');
            $table->text('phone_number');
            $table->text('contact_person');
            $table->text('email');
            $table->text('comments')->nullable(true);

            $table->text('driver_first_name');
            $table->text('driver_last_name');
            $table->text('driver_phone_number');
            $table->text('driver_document_number');
            $table->text('driver_car_registration_number');
            $table->date('driver_arrival_date');
            $table->time('driver_approx_arrival_time');

            $table->timestamps();

            $table->foreign('spedition_exchange_id')->references('id')->on('spedition_exchanges')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('spedition_exchange_offers');
	}
}
