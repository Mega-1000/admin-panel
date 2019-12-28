<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomPageContent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_page_content', function (Blueprint $table) {
            $table->increments('id');
            $table->text('title');
            $table->unsignedInteger('category_id');
            $table->longText('content');
            $table->timestamps();
            $table->foreign('category_id')->references('id')->on('custom_page_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_page_content');
    }
}
