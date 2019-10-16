<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrderPackagesTableAddNewFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_packages', function (Blueprint $table) {
            $table->decimal('cost_for_client')->comment('Koszt wysylki brutto dla klienta')->nullable();
            $table->decimal('cost_for_company')->comment('Koszt wysylki brutto dla firmy')->nullable();
            $table->decimal('real_cost_for_company')->comment('Realny / naliczony koszt wysylki brutto dla firmy')->nullable();

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
            //
        });
    }
}
