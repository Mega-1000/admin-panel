<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGrossPriceColumnToOrderItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('gross_selling_price_commercial_unit',8,2);
            $table->decimal('gross_selling_price_basic_unit',8,2);
            $table->decimal('gross_selling_price_calculated_unit',8,2);
            $table->decimal('gross_selling_price_aggregate_unit',8,2);
            $table->decimal('gross_selling_price_the_largest_unit',8,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('gross_selling_price_commercial_unit');
            $table->dropColumn('gross_selling_price_basic_unit');
            $table->dropColumn('gross_selling_price_calculated_unit');
            $table->dropColumn('gross_selling_price_aggregate_unit');
            $table->dropColumn('gross_selling_price_the_largest_unit');
        });
    }
}
