<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category');
            $table->string('category_edited');
            $table->text('description');
            $table->string('img_url');
            $table->string('url_for_website');
            $table->string('category_navigation');
            $table->string('token_prod_cat')->nullable()->index();
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
        Schema::dropIfExists('category_details');
    }
}
