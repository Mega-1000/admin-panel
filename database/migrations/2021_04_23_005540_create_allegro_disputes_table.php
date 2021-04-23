<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllegroDisputesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allegro_disputes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dispute_id');
            $table->string('hash');
            $table->string('status');
            $table->string('subject');
            $table->string('buyer_id');
            $table->string('buyer_login');
            $table->string('form_id');
            $table->dateTime('ordered_date');
            $table->boolean('unseen_changes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('allegro_disputes');
    }
}
