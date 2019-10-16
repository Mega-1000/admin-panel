<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrderPaymentsTableAddMasterPaymentColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_payments', function (Blueprint $table) {
            $table->unsignedInteger('master_payment_id')->nullable()->comment('Jeśli puste - wpłata do konkretnego zamówienia, jeśli wypełnione - płatność jest częścią większej wpłaty klineta');
            $table->foreign('master_payment_id')->references('id')->on('payments')->onDelete('cascade');

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
