<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPendingToAllegroDisputesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('allegro_disputes', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable();
            $table->boolean('is_pending')->default(0);
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
        Schema::table('allegro_disputes', function (Blueprint $table) {
            $table->dropColumn('is_pending');
            $table->dropColumn('user_id');
        });
    }
}
