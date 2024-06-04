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
        Schema::create('styro_leads', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->string('firm_name');
            $table->string('email');
            $table->string('email_sent')->default(true);
            $table->string('email_read')->default(false);
            $table->string('on_website')->default(false);
            $table->string('made_inquiry')->default(false);
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
        Schema::dropIfExists('styro_leads');
    }
};
