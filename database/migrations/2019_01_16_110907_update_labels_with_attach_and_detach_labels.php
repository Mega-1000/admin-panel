<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLabelsWithAttachAndDetachLabels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('label_labels_after_removal', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('main_label_id');
            $table->unsignedInteger('label_to_add_id');

            $table->foreign('main_label_id')->references('id')->on('labels')->onDelete('cascade');
            $table->foreign('label_to_add_id')->references('id')->on('labels')->onDelete('cascade');
        });

        Schema::create('label_labels_after_addition', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('main_label_id');
            $table->unsignedInteger('label_to_add_id');

            $table->foreign('main_label_id')->references('id')->on('labels')->onDelete('cascade');
            $table->foreign('label_to_add_id')->references('id')->on('labels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('label_labels_after_removal');
        Schema::drop('label_labels_after_addition');
    }
}
