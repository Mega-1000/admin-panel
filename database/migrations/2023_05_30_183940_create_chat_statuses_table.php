<?php

use App\ChatStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('message');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $chatStatus = new ChatStatus();
        $chatStatus->is_active = true;
        $chatStatus->message = 'Witaj, jak mogę Ci pomóc?';
        $chatStatus->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_statuses');
    }
};
