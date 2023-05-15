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
        Schema::table('order_payments', function (Blueprint $table) {
            $table->text('status')->nullable()->comment('Payment status - depending on warehouse action.')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_payments', function (Blueprint $table) {``
            $table->enum('status', ['ACCEPTED', 'PENDING', 'DECLINED'])->nullable()->comment('Payment status - depending on warehouse action.')->change();
        });
    }
};
