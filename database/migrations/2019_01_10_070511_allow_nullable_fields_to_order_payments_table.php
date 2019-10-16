<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AllowNullableFieldsToOrderPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_payments', function (Blueprint $table) {
            $table->decimal('amount', 9, 2)->comment('Kwota wpłacona')->nullable()->change();
            $table->text('notices')->comment('Dodatkowe uwagi do wpłaty')->nullable()->change();
            $table->enum('promise', ['1', ''])->nullable()->change();
            $table->date('promise_date')->comment('data do ktorej klient zadeklarowal wplate kwoty obiecane')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_payments', function (Blueprint $table) {
            //
        });
    }
}
