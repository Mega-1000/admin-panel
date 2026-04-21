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
        Schema::create('low_order_quantity_alert_messages', function (Blueprint $table) {
            $table->id();
            $table->string('attachment_name')->nullable();
            $table->string('title')->nullable();
            $table->text('message')->nullable();
            $table->integer('delay_time')->nullable();
            $table->unsignedBigInteger('low_order_quantity_alert_id')->nullable();
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
        Schema::dropIfExists('low_order_quantity_alert_messages');
    }
};
