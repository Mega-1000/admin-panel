<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPreferredInvoiceDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dateTime('preferred_invoice_date')->nullable(true)->comment('Preferowana data wystawienia systemu');
        });
        DB::statement('UPDATE orders SET preferred_invoice_date = (SELECT created_at from order_labels where order_id = orders.id AND label_id = 66 LIMIT 1)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('preferred_invoice_date');
        });
    }
}
