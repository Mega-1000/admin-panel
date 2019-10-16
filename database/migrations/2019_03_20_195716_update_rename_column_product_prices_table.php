<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRenameColumnProductPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->renameColumn('gross_purchase_price_basic_unit', 'gross_selling_price_basic_unit');
            $table->renameColumn('gross_purchase_price_commercial_unit', 'gross_selling_price_commercial_unit');
            $table->renameColumn('gross_purchase_price_calculated_unit', 'gross_selling_price_calculated_unit');
            $table->renameColumn('gross_purchase_price_aggregate_unit', 'gross_selling_price_aggregate_unit');
            $table->renameColumn('gross_purchase_price_the_largest_unit', 'gross_selling_price_the_largest_unit');
            $table->dropColumn('gross_special_price_commercial_unit');
            $table->dropColumn('gross_special_price_calculated_unit');
            $table->dropColumn('gross_special_price_aggregate_unit');
            $table->dropColumn('gross_special_price_basic_unit');
            $table->dropColumn('gross_special_price_the_largest_unit');
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
