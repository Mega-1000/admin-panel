<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllegroChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allegro_chat_threads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('allegro_thread_id');
            $table->string('allegro_msg_id');
            $table->unsignedInteger('user_id');
            $table->string('allegro_user_login');
            $table->string('subject')->nullable();
            $table->text('content');
            $table->boolean('is_outgoing');
            $table->json('attachments')->nullable();
            $table->string('type');
            $table->string('allegro_offer_id')->nullable();
            $table->string('allegro_order_id')->nullable();
            $table->datetime('original_allegro_date');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('allegro_chat_threads');
    }
}
