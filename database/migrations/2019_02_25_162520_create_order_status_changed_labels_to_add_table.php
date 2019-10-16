<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderStatusChangedLabelsToAddTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_status_changed_labels_to_add', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('status_id');
            $table->unsignedInteger('label_id');

            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');
            $table->foreign('label_id')->references('id')->on('labels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_status_changed_labels_to_add');
    }
}
