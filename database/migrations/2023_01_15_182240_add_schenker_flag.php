<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSchenkerFlag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('container_types', function (Blueprint $table) {
            $table->char('shipping_provider', 30)
                ->default('')
                ->after('symbol');
            $table->json('additional_informations')->nullable()->after('shipping_provider');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('container_types', function (Blueprint $table) {
            $table->dropColumn(['shipping_provider']);
            $table->dropColumn(['additional_informations']);
        });
    }
}
