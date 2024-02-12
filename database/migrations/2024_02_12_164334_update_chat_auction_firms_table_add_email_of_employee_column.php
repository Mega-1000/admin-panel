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
        Schema::table('chat_auction_firms', function (Blueprint $table) {
            $table->string('email_of_employee')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('chat_auction_firms', function (Blueprint $table) {
            $table->dropColumn('email_of_employee');
        });
    }
};
