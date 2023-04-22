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
        Schema::create('chat_auctions', function (Blueprint $table) {
            $table->id();
            $table->date('end_of_auction');
            $table->date('date_of_delivery');
            $table->integer('price');
            $table->integer('quality');
            $table->unsignedBigInteger('chat_id');
            $table->boolean('confirmed')->default(false);
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
        Schema::dropIfExists('auctions');
    }
};
