<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RefactorOrderMessagesAttachmentsToNewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_message_attachments', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_message_id');
            $table->text('file');
            $table->timestamps();

            $table->foreign('order_message_id')->references('id')->on('order_messages')->onDelete('cascade');
        });

        Schema::table('order_messages', function (Blueprint $table) {
            $table->dropColumn('file');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
