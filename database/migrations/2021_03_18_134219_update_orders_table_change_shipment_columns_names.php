<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrdersTableChangeShipmentColumnsNames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('confirmed_sending_date_con_mag', 'confirmed_sending_date_consultant');
            $table->renameColumn('confirmed_pickup_date_con_mag', 'confirmed_pickup_date_consultant');
            $table->renameColumn('initial_delivery_date_con_mag', 'initial_delivery_date_consultant');
            $table->datetime('confirmed_sending_date_warehouse')->nullable();
            $table->datetime('confirmed_pickup_date_warehouse')->nullable();
            $table->datetime('initial_delivery_date_warehouse')->nullable();
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
            $table->renameColumn('confirmed_sending_date_consultant', 'confirmed_sending_date_con_mag');
            $table->renameColumn('confirmed_pickup_date_consultant', 'confirmed_pickup_date_con_mag');
            $table->renameColumn('initial_delivery_date_consultant', 'initial_delivery_date_con_mag');
            $table->dropColumn('confirmed_sending_date_warehouse');
            $table->dropColumn('confirmed_pickup_date_warehouse');
            $table->dropColumn('initial_delivery_date_warehouse');
        });
    }
}
