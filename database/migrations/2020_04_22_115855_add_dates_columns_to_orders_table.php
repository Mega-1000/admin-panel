<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDatesColumnsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->datetime('initial_sending_date_client')->nullable();
            $table->datetime('initial_sending_date_consultant')->nullable();
            $table->datetime('initial_sending_date_magazine')->nullable();
            $table->datetime('confirmed_sending_date_con_mag')->nullable();
            $table->datetime('initial_pickup_date_client')->nullable();
            $table->datetime('confirmed_pickup_date_client')->nullable();
            $table->datetime('confirmed_pickup_date_con_mag')->nullable();
            $table->datetime('initial_delivery_date_con_mag')->nullable();
            $table->datetime('confirmed_delivery_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('initial_sending_date_client');
            $table->dropColumn('initial_sending_date_consultant');
            $table->dropColumn('initial_sending_date_magazine');
            $table->dropColumn('confirmed_sending_date_con_mag');
            $table->dropColumn('initial_pickup_date_client');
            $table->dropColumn('confirmed_pickup_date_client');
            $table->dropColumn('confirmed_pickup_date_con_mag');
            $table->dropColumn('initial_delivery_date_con_mag');
            $table->dropColumn('confirmed_delivery_date');
        });
    }
}
