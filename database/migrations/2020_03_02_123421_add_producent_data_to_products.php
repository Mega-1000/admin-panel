<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProducentDataToProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('product_symbol_on_supplier_documents','supplier_product_symbol');
            $table->renameColumn('product_symbol_on_collective_box', 'supplier_product_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('supplier_product_symbol','product_symbol_on_supplier_documents');
            $table->renameColumn('supplier_product_name', 'product_symbol_on_collective_box');
        });
    }
}
