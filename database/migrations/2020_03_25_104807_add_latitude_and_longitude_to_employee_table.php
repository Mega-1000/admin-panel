<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLatitudeAndLongitudeToEmployeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->decimal('latitude', 12, 10);
            $table->decimal('longitude', 12, 10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });
    }
}
