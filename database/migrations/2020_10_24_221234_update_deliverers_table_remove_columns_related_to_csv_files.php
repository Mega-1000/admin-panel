<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDeliverersTableRemoveColumnsRelatedToCsvFiles extends Migration
{
    public function up(): void
    {
        Schema::table('deliverers', function (Blueprint $table) {
            $table->dropColumn('net_payment_column_number');
            $table->dropColumn('gross_payment_column_number_gross');
            $table->dropColumn('letter_number_column_number');
        });
    }

    public function down(): void
    {
        Schema::table('deliverers', function (Blueprint $table) {
            $table->unsignedInteger('net_payment_column_number')->nullable();
            $table->unsignedInteger('gross_payment_column_number_gross')->nullable();
            $table->unsignedInteger('letter_number_column_number')->nullable(false);
        });
    }
}
