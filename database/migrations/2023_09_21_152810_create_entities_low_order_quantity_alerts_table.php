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
        Schema::create('low_order_quantity_alerts', function (Blueprint $table) {
            $table->id();
            $table->integer('item_names');
            $table->integer('min_quantity');
            $table->text('message');
            $table->integer('delay_time');
            $table->string('title');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('low_order_quantity_alerts');
    }
};
