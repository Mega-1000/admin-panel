<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliverersImportConditionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deliverers_import_conditions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('deliverer_id')->nullable(false);
            $table->enum('type', ['search_compare', 'search_regex', 'get'])->nullable(false);
            $table->unsignedTinyInteger('sort')->nullable(false);
            $table->unsignedTinyInteger('import_column_number')->nullable(false);
            $table->string('db_column_name')->nullable(false);
            $table->timestamps();

            $table->foreign('deliverer_id')->references('id')->on('deliverers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliverers_import_conditions', function (Blueprint $table) {
            $table->dropForeign('deliverers_import_conditions_deliverer_id_foreign');
        });

        Schema::dropIfExists('deliverers_import_conditions');
    }
}
