<?php

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
    public function up(): void
    {
        Schema::create('email_sending', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('email_setting_id');
            $table->string('email');
            $table->string('title')->default('');
            $table->text('content')->default('');
            $table->string('attachment')->default('');
            $table->dateTime('scheduled_date')->comment('Data planownego wysłania emaila');
            $table->dateTime('send_date')->nullable(true)->comment('Data wysłania emaila');
            $table->boolean('message_send')->default(0);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('email_setting_id')->references('id')->on('email_settings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('email_sending');
    }
};
