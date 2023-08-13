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
        Schema::create('order_invoice_documents', function (Blueprint $table) {
            $table->id();
            $table->string('preliminary_buying_document_number')->nullable();
            $table->string('buying_document_number')->nullable();
            $table->decimal('gross_value', 10, 2)->nullable();
            $table->date('invoice_date')->nullable();
            $table->unsignedBigInteger('order_id')->onDelete('cascade');
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
        Schema::dropIfExists('order_invoice_documents');
    }
};
