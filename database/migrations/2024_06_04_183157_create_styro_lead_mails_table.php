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
    public function up()
    {
        Schema::create('styro_lead_mails', function (Blueprint $table) {
            $table->id();
            $table->string('email_sent')->default(true);
            $table->string('email_read')->default(false);
            $table->string('on_website')->default(false);
            $table->string('made_inquiry')->default(false);
            $table->integer('number_of_emails_sent')->default(0);
            $table->integer('styro_lead_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('styro_lead_mails');
    }
};
