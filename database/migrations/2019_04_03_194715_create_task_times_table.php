<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateTaskTimesTable.
 */
class CreateTaskTimesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('task_times', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('task_id');
            $table->dateTime('date_start');
            $table->dateTime('date_end');
            $table->timestamps();

		    $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('task_times');
	}
}
