<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersSurplusPaymentsHistoryTableAddUserSurplusRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_surplus_payments_history', function (Blueprint $table) {
            $table->unsignedInteger('user_surplus_payment')->nullable();
            $table->foreign('user_surplus_payment')->references('id')->on('users_surplus_payments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_surplus_payments_history', function (Blueprint $table) {
            $table->dropForeign(['user_surplus_payment']);
            $table->dropColumn('user_surplus_payment');
        });
    }
}
