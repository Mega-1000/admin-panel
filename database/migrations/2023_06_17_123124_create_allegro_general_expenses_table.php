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
        Schema::create('allegro_general_expenses', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_of_commitment_creation')->nullable();
            $table->string('offer_name')->nullable();
            $table->string('offer_identification')->nullable();
            $table->string('operation_type')->nullable();
            $table->string('credit')->nullable();
            $table->string('debit')->nullable();
            $table->string('balance')->nullable();
            $table->string('operation_details')->nullable();
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
        Schema::dropIfExists('allegro_general_expenses');
    }
};
