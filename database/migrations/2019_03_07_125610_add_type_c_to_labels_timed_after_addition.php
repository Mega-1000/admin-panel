<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeCToLabelsTimedAfterAddition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('labels_timed_after_addition', function (Blueprint $table) {
            $table->boolean('to_add_type_c')->nullable();
            $table->boolean('to_remove_type_c')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('labels_timed_after_addition', function (Blueprint $table) {
            //
        });
    }
}
