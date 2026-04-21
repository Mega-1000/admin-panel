<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateSpeditionExchangesTable.
 */
class CreateSpeditionExchangesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('spedition_exchanges', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('chosen_spedition_offer_id')->nullable(true);
            $table->text('hash');
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
		Schema::drop('spedition_exchanges');
	}
}
