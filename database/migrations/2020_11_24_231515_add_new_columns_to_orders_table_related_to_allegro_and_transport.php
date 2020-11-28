<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnsToOrdersTableRelatedToAllegroAndTransport extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedDecimal('allegro_deposit_value', 8, 2)->nullable()
                ->after('allegro_form_id');
            $table->dateTime('allegro_operation_date')->nullable()
                ->after('allegro_deposit_value');
            $table->string('allegro_additional_service')->nullable()
                ->after('allegro_operation_date');
            $table->string('payment_channel')->nullable()
                ->after('allegro_additional_service');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'allegro_deposit_value',
                'allegro_operation_date',
                'allegro_additional_service',
                'payment_channel',
            ]);
        });
    }
}
