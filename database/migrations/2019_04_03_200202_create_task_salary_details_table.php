<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateTaskSalaryDetailsTable.
 */
class CreateTaskSalaryDetailsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('task_salary_details', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('task_id');
            $table->text('consultant_notice')->nullable();
            $table->decimal('consultant_value', 8,2)->nullable();
            $table->text('warehouse_notice')->nullable();
            $table->decimal('warehouse_value', 8, 2)->nullable();
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
		Schema::drop('task_salary_details');
	}
}
