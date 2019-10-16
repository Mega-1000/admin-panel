<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateTasksTable.
 */
class CreateTasksTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tasks', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('warehouse_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('order_id')->nullable();
            $table->unsignedInteger('created_by');
            $table->string('name');
            $table->string('color');
            $table->enum('status', ['WAITING_FOR_ACCEPT', 'REJECTED', 'TO_DO', 'FINISHED', 'IN_PROGRESS']);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
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
		Schema::drop('tasks');
	}
}
