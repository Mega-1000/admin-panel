<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkingEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('working_events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('event');
            $table->unsignedInteger('order_id')->nullable(true);
            $table->foreign('order_id')->references('id')->on('orders');
            $table->unsignedInteger('user_id')->nullable(true);
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('working_events');
    }
}
