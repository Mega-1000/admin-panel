<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrderAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->string('phone_code', 15)->after('nip')->nullable();
            $table->string('country', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->dropColumn(['phone_code', 'country']);
        });
    }
}
