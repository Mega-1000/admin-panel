<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrdersTableMakeFieldsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('warehouse_id')->nullable()->change();
            $table->decimal('additional_service_cost')->nullable()->change();
            $table->string('invoice_warehouse_file')->nullable()->change();
            $table->unsignedInteger('document_number')->nullable()->change();
            $table->decimal('consultant_earning')->nullable()->change();
            $table->decimal('warehouse_cost')->nullable()->change();
            $table->enum('printed', [true, false])->nullable()->change();
            $table->text('correction_description')->nullable()->change();
            $table->decimal('correction_amount')->nullable()->change();
            $table->decimal('packing_warehouse_cost')->nullable()->change();
            $table->integer('rating')->nullable()->change();
            $table->text('rating_message')->nullable()->change();
            $table->unsignedInteger('status_id')->nullable()->change();
            $table->dateTime('last_status_update_date')->comment('Status data zmiany')->nullable()->change();
            $table->decimal('total_price', 9, 2)->comment('Suma zamówienia')->nullable()->change();
            $table->float('weight')->comment('Waga zamówienia')->nullable()->change();
            $table->decimal('shipment_price')->comment('Koszt wysyłki')->nullable()->change();
            $table->string('customer_notices')->nullable()->change();
            $table->decimal('cash_on_delivery_amount')->comment('Kwota pobrania')->nullable()->change();
            $table->unsignedInteger('employee_id')->comment('Przypisany pracownik do zamówienia, w praktyce jest nim konsultant')->nullable()->change();
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
}
