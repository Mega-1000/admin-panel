<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSelloPackagesToTemplatesLink extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_templates', function (Blueprint $table) {
            $table->integer('sello_delivery_id')->nullable();
            $table->integer('sello_deliverer_id')->nullable();
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
            $table->dropColumn('sello_delivery_id');
            $table->dropColumn('sello_deliverer_id');
        });
    }
}
