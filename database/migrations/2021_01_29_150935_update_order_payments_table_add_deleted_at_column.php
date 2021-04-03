<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrderPaymentsTableAddDeletedAtColumn extends Migration
{
    public function up(): void
    {
        Schema::table('order_payments', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('order_payments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
