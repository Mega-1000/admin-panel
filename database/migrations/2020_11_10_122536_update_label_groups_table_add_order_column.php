<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLabelGroupsTableAddOrderColumn extends Migration
{
    public function up()
    {
        Schema::table('label_groups', function (Blueprint $table) {
            $table->unsignedInteger('order')->nullable();
        });
    }

    public function down()
    {
        Schema::table('label_groups', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
}
