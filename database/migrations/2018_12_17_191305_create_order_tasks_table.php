<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateOrderTasksTable.
 */
class CreateOrderTasksTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_tasks', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('employee_id')->comment('Id pracownika który stworzył zadanie');
            $table->text('description')->comment('Treść zadania');
            $table->string('title')->comment('Tytuł zadania');
            $table->dateTime('show_label_at')->comment('Po upływie tej daty podawanej z godzinami, na liście zamówień wybranym do zadania pracownika pojawia się odpowiednia etykieta');
            $table->enum('status', ['OPEN', 'CLOSED']);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
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
		Schema::drop('order_tasks');
	}
}
