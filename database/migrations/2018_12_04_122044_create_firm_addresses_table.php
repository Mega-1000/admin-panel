<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateFirmAddressesTable.
 */
class CreateFirmAddressesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('firm_addresses', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('firm_id');
            $table->string('city')->nullable();
            $table->decimal('latitude',9,4)->nullable();
            $table->decimal('longitude',9,4)->nullable();
            $table->string('flat_number')->nullable();
            $table->string('address')->nullable();
            $table->string('address2')->nullable();
            $table->string('postal_code')->nullable();
            $table->timestamps();

            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('firm_addresses');
	}
}
