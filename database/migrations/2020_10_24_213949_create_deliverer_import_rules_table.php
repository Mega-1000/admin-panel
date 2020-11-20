<?php declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDelivererImportRulesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deliverer_import_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('deliverer_id')->nullable(false);
            $table->enum('action', ['searchCompare', 'searchRegex', 'set', 'get', 'getAndReplace'])->nullable(false);
            $table->string('db_column_name')->nullable(false);
            $table->unsignedTinyInteger('import_column_number')->nullable(false);
            $table->string('value')->nullable(false);
            $table->string('change_to')->nullable(false);
            $table->unsignedTinyInteger('sort')->nullable(false);
            $table->timestamps();

            $table->foreign('deliverer_id')->references('id')->on('deliverers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliverer_import_rules', function (Blueprint $table) {
            $table->dropForeign('deliverer_import_rules_deliverer_id_foreign');
        });

        Schema::dropIfExists('deliverer_import_rules');
    }
}
