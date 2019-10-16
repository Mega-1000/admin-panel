<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLabelToHandleIdToOrderLabelSchedulersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_label_schedulers', function (Blueprint $table) {
            $table->unsignedInteger('label_id_to_handle')->after('label_id')->nullable();

            $table->foreign('label_id_to_handle')->references('id')->on('labels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('handle_id_to_order_label_schedulers', function (Blueprint $table) {
            //
        });
    }
}
