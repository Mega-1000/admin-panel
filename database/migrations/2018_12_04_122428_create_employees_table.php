<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateEmployeesTable.
 */
class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('firm_id')->nullable();
            $table->unsignedInteger('warehouse_id')->nullable();
            $table->string('email');
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('phone')->nullable();
            $table->enum('job_position', ['SECRETARIAT', 'CONSULTANT', 'STOREKEEPER', 'SALES']);
            $table->text('comments')->nullable();
            $table->text('additional_comments')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('radius')->nullable();
            $table->enum('status', ['ACTIVE', 'PENDING']);
            $table->timestamps();

            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('employees');
    }
}
