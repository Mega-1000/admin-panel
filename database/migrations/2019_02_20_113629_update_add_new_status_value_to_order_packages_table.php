<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddNewStatusValueToOrderPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_packages', function (Blueprint $table) {
            $table->enum('status', ['WAITING_FOR_SENDING','DELIVERED', 'CANCELLED', 'WAITING_FOR_CANCELLED','SENDING', 'NEW'])->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('the_status_to_order_packages', function (Blueprint $table) {
            //
        });
    }
}
