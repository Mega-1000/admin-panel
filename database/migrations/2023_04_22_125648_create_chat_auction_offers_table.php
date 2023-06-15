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
        Schema::create('chat_auction_offers', function (Blueprint $table) {
            $table->id();
            $table->float('commercial_price_net');
            $table->float('basic_price_net');
            $table->float('calculated_price_net');
            $table->float('aggregate_price_net');
            $table->float('commercial_price_gross');
            $table->float('basic_price_gross');
            $table->float('calculated_price_gross');
            $table->float('aggregate_price_gross');
            $table->unsignedInteger('order_item_id');
            $table->unsignedInteger('chat_auction_id');
            $table->unsignedInteger('firm_id');
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
        Schema::dropIfExists('chat_auction_offers');
    }
};
