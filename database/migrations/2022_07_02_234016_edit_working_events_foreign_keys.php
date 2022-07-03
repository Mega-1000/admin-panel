<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditWorkingEventsForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('working_events', function (Blueprint $table) {
            $table->dropForeign('working_events_order_id_foreign');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');;
            $table->dropForeign('working_events_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');;
        });

        Schema::table('chats', function (Blueprint $table) {
            $table->dropForeign('chats_order_id_foreign');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');;
        });

        Schema::table('chat_user', function (Blueprint $table) {
            $table->dropForeign('chat_user_chat_id_foreign');
            $table->foreign('chat_id')->references('id')->on('chats')->onDelete('cascade');;
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign('messages_chat_user_id_foreign');
            $table->foreign('chat_user_id')->references('id')->on('chat_user')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
