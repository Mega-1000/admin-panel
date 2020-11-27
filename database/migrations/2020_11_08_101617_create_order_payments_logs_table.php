<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderPaymentsLogsTable extends Migration
{
    public function up(): void
    {
        Schema::create('order_payments_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('booked_date')->nullable();
            $table->enum('payment_type', ['CLIENT_PAYMENT', 'ORDER_PAYMENT', 'RETURN_PAYMENT']);
            $table->unsignedInteger('order_payment_id');
            $table->foreign('order_payment_id')
                ->references('id')
                ->on('order_payments')->onDelete('cascade')->nullable();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('customers')->onDelete('cascade')->nullable();
            $table->unsignedInteger('employee_id');
            $table->foreign('employee_id')
                ->references('id')
                ->on('users')->onDelete('cascade')->nullable();
            $table->string('payment_service_operator')->nullable();
            $table->unsignedInteger('order_id');
            $table->foreign('order_id')
                ->references('id')
                ->on('orders')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->decimal('payment_amount')->nullable();
            $table->decimal('transfer_payment_amount')->nullable();
            $table->decimal('client_return_payment_amount')->nullable();
            $table->decimal('payment_sum_before_payment')->nullable();
            $table->decimal('payment_sum_after_payment')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('order_payments_logs', function (Blueprint $table) {
            $table->dropForeign('order_payment_id');
            $table->dropColumn('order_payment_id');
            $table->dropForeign('user_id');
            $table->dropColumn('user_id');
            $table->dropForeign('order_id');
            $table->dropColumn('order_id');
            $table->dropForeign('employee_id');
            $table->dropColumn('employee_id');
        });
        Schema::dropIfExists('order_payments_logs');
    }
}
