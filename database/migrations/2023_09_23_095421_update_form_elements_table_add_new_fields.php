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
    public function up(): void
    {
        Schema::table('form_elements', function (Blueprint $table) {
            $table->text('text')->nullable();
            $table->text('color')->nullable();
            $table->text('size')->nullable();
            $table->boolean('new_tab')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('form_elements', function (Blueprint $table) {
            $table->dropColumn('text');
        });
    }
};
