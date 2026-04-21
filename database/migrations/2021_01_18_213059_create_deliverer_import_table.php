<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDelivererImportTable extends Migration
{
    public function up(): void
    {
        Schema::create('deliverer_import', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('deliverer_id');
            $table->string('originalFileName', 300);
            $table->string('importFileName', 300);
            $table->timestamps();

            $table->foreign('deliverer_id')->references('id')
                ->on('deliverers')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('deliverer_import', function (Blueprint $table) {
            $table->dropForeign('deliverer_import_deliverer_id_foreign');
        });

        Schema::dropIfExists('deliverer_import');
    }
}
