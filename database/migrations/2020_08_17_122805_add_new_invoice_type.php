<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewInvoiceType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_invoices', function (Blueprint $table) {
            $table->string('invoice_type')->default('buy')->change();
            \DB::table('order_invoices')->update(['invoice_type' => 'buy']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_invoices', function (Blueprint $table) {
            $table->string('invoice_type')->default('')->change();
            \DB::table('order_invoices')->update(['invoice_type' => '']);
        });
    }
}
