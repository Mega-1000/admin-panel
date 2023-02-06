<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAttachmentsToTextInAllegroChatThreads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('allegro_chat_threads', function (Blueprint $table) {
            $table->text('attachments')->change();
        });
        Artisan::call('db:seed', ['--class' => 'MenuItemsTableSeeder', '--force' => true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('allegro_chat_threads', function (Blueprint $table) {
            $table->json('attachments')->change();
        });
    }
}
