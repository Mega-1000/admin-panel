<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faq_category_indices', function (Blueprint $table) {
            $table->id();
            $table->string('faq_category_name');
            $table->unsignedInteger('faq_category_index');
            $table->unsignedInteger('faq_id')->nullable();
            $table->enum('faq_category_type', ['question', 'category'])->default('category');
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
        Schema::dropIfExists('faq_category_indices');
    }
};
