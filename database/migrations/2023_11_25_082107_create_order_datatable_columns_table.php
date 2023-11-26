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
        Schema::create('order_datatable_columns', function (Blueprint $table) {
            $table->id();
            $table->integer('order');
            $table->boolean('hidden');
            $table->string('size');
            $table->unsignedInteger('user_id');
            $table->string('label');
            $table->string('filter');
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
        Schema::dropIfExists('order_datatable_columns');
    }
};
