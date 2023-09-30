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
        Schema::table('low_order_quantity_alerts', function (Blueprint $table) {
            $table->dropColumn('message');
            $table->dropColumn('delay_time');
            $table->dropColumn('space');
            $table->longText('php_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('low_order_quantity_alerts', function (Blueprint $table) {
            $table->dropColumn('php_code');
            $table->string('message')->nullable();
            $table->integer('delay_time')->nullable();
            $table->string('space')->nullable();
        });
    }
};
