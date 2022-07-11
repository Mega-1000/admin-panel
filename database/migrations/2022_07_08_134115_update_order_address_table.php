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
        Schema::create('countries', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->string('name');
        });
        
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->string('phone_code', 15)->after('nip')->nullable();
            $table->mediumInteger('country_id', false, true)
                ->nullable();
            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
        
        \App\Entities\Country::insert(['name' => 'Polska']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn(['phone_code', 'country_id']);
        });
        
        Schema::dropIfExists('countries');
    }
}
