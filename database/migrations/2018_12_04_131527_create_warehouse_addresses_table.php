<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateWarehouseAddressesTable.
 */
class CreateWarehouseAddressesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('warehouse_addresses', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('warehouse_id');
            $table->string('address')->nullable();
            $table->string('warehouse_number')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->timestamps();

            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('warehouse_addresses');
	}
}
