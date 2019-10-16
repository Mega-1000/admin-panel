<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePaymentsTableAddNewFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->text('notices')->comment('Dodatkowe uwagi do wpłaty')->nullable();
            $table->enum('promise', ['1', ''])->nullable();
            $table->date('promise_date')->comment('data do ktorej klient zadeklarowal wplate kwoty obiecane')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
