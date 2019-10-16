<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
/**
 * Class CreateWarehousePropertiesTable.
 */
class CreateWarehousePropertiesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('warehouse_properties', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('warehouse_id');
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('phone')->nullable();
            $table->text('comments')->nullable();
            $table->text('additional_comments')->nullable();
            $table->text('open_days')->nullable();
            $table->string('email')->nullable();
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
		Schema::drop('warehouse_properties');
	}
}
