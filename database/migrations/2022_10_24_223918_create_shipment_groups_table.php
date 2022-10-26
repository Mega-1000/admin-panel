<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('courier_name')->comment('Courier name');
            $table->string('package_type')->nullable()->comment('Package group type');
            $table->integer('lp')->comment('Package group number of the day.');
            $table->date('shipment_date')->comment('Group shipment date');
            $table->boolean('sent')->comment('If package group was send');
            $table->boolean('closed')->comment('If package group was closed');
            $table->timestamps();
        });
        Schema::table('order_packages', function (Blueprint $table) {
            $table->unsignedInteger('shipment_group_id')->nullable(true);
            $table->foreign('shipment_group_id')->references('id')->on('shipment_groups')->onDelete('set NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_packages', function (Blueprint $table) {
            $table->dropForeign('order_packages_shipment_group_id_foreign');
            $table->dropColumn('shipment_group_id');
        });
        Schema::dropIfExists('shipment_groups');
    }
}
