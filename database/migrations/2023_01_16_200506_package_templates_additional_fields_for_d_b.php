<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PackageTemplatesAdditionalFieldsForDB extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_templates', function (Blueprint $table) {
            $table->string('protection_method', 20)->default('')->after('list_order');
            $table->string('services')->default('')->after('list_order');
        });

        Schema::table('order_packages', function (Blueprint $table) {
            $table->string('protection_method', 20)->default('')->after('delivery_cost_balance');
            $table->string('services')->default('')->after('delivery_cost_balance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package_templates', function (Blueprint $table) {
            $table->dropColumn(['protection_method', 'services']);
        });

        Schema::table('order_packages', function (Blueprint $table) {
            $table->dropColumn(['protection_method', 'services']);
        });
    }
}
