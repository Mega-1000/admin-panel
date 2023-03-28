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
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('save_name')->default(true)->tooltip('Zaczytywanie nazwy kategorii z nazwy pliku cvs');
            $table->boolean('save_description')->default(true)->tooltip('Zaczytywanie opisu kategorii z nazwy pliku cvs');
            $table->boolean('save_image')->default(true)->tooltip('Zaczytywanie obrazka kategorii z nazwy pliku cvs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
