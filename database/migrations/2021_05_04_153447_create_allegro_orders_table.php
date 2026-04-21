<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllegroOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allegro_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_id');
            $table->string('buyer_email');
            $table->boolean('new_order_message_sent')->default(false);
            $table->timestamps();

            $table->index('new_order_message_sent');
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('allegro_orders');
    }
}
