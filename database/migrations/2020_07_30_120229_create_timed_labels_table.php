<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimedLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timed_labels', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('execution_time')->comment('Kolumna ta zawiera datę oraz czas w którym zostanie dodana etykieta interwencja');
            $table->unsignedInteger('order_id')->nullable();
            $table->unsignedInteger('label_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('label_id')->references('id')->on('labels');
            $table->boolean('is_executed');
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
        Schema::dropIfExists('timed_labels');
    }
}
