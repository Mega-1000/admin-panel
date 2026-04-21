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
        Schema::table('chat_auctions', function (Blueprint $table) {
           $table->dateTime('end_of_auction')->change();
            $table->dateTime('date_of_delivery')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('chat_auctions', function (Blueprint $table) {
            $table->dropColumn('end_of_auction');
        });
    }
};
