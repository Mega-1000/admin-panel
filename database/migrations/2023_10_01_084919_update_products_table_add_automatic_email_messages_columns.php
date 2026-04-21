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
        Schema::table('products', function (Blueprint $table) {
            $table->string('automatic_email_messages_14_column')->nullable();
            $table->string('automatic_email_messages_15_column')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('', function (Blueprint $table) {
            $table->dropColumn('automatic_email_messages_14_column');
            $table->dropColumn('automatic_email_messages_15_column');
        });
    }
};
