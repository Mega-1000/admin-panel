<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLabelsTimedAfterAdditionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('labels_timed_after_addition', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('main_label_id');
            $table->unsignedInteger('label_to_handle_id');
            $table->text('to_add_type_a')->nullable();
            $table->text('to_remove_type_a')->nullable();
            $table->text('to_add_type_b')->nullable();
            $table->text('to_remove_type_b')->nullable();

            $table->foreign('main_label_id')->references('id')->on('labels')->onDelete('cascade');
            $table->foreign('label_to_handle_id')->references('id')->on('labels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('labels_timed_after_addition');
    }
}
