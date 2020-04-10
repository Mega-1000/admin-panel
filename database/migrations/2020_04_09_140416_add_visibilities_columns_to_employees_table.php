<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVisibilitiesColumnsToEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->decimal('firstname_visibility', 1,0)->default('1');
            $table->decimal('lastname_visibility', 1,0)->default('1');
            $table->decimal('phone_visibility', 1,0)->default('1');
            $table->decimal('firm_visibility', 1,0)->default('1');
            $table->decimal('comments_visibility', 1,0)->default('1');
            $table->decimal('postal_code_visibility', 1,0)->default('1');
            $table->decimal('email_visibility', 1,0)->default('1');
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
            $table->dropColumn('firstname_visibility');
            $table->dropColumn('lastname_visibility');
            $table->dropColumn('phone_visibility');
            $table->dropColumn('firm_visibility');
            $table->dropColumn('comments_visibility');
            $table->dropColumn('postal_code_visibility');
            $table->dropColumn('email_visibility');
        });
    }
}
