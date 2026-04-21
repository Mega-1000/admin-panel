<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrdersTableAddNewFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('warehouse_id');
            $table->decimal('additional_service_cost');
            $table->string('invoice_warehouse_file');
            $table->unsignedInteger('document_number');
            $table->decimal('consultant_earning');
            $table->decimal('warehouse_cost');
            $table->enum('printed', [true, false]);
            $table->text('correction_description');
            $table->decimal('correction_amount');
            $table->decimal('packing_warehouse_cost');
            $table->integer('rating');
            $table->text('rating_message');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
}
