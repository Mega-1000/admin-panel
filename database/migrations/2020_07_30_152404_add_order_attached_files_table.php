<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderAttachedFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_files', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->string('file_name');
            $table->string('hash');
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('order_files');
    }
}
