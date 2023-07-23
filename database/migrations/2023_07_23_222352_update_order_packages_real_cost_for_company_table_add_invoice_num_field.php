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
        Schema::table('order_packages_real_cost_for_company', function (Blueprint $table) {
            $table->string('invoice_num')->nullable()->after('invoice_file');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('order_packages_real_cost_for_company', function (Blueprint $table) {
            $table->dropColumn('invoice_num');
        });
    }
};
