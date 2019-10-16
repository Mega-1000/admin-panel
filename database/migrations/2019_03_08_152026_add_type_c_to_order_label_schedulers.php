<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeCToOrderLabelSchedulers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_label_schedulers', function (Blueprint $table) {
            $table->enum('type', ['A', 'B', 'C'])->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_label_schedulers', function (Blueprint $table) {
            $table->enum('type', ['A','B'])->change();
        });
    }
}
