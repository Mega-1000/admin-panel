<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDisplayRoleCollumnInEmployeeRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_roles', function (Blueprint $table) {
            $table->decimal('is_contact_displayed_in_fronted', 1,0)->default('1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_roles', function (Blueprint $table) {
            $table->dropColumn('is_contact_displayed_in_fronted');
        });
    }
}
