<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id')->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->dateTime('posted_in_system_date')->nullable(true)->comment('Data zaksięgowania w systemie');
            $table->dateTime('posted_in_bank_date')->nullable(true)->comment('Data zaksięgowania w banku');
            $table->text('payment_id')->nullable(true)->comment('Identyfikator płatności z sello');
            $table->string('kind_of_operation')->nullable(true)->comment('Rodzaj operacji');
            $table->string('order_id')->nullable(true)->comment('Identyfikator zamówienia');
            $table->string('operator')->nullable(true)->comment('Operator płatności');
            $table->float('operation_value')->nullable(true)->comment('Wartość operacji');
            $table->float('balance')->nullable(true)->comment('Saldo');
            $table->string('accounting_notes')->nullable(true)->comment('Uwagi księgowe');
            $table->string('transaction_notes')->nullable(true)->comment('Uwagi dotyczace transakcji');
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
        Schema::dropIfExists('transactions');
    }
}
