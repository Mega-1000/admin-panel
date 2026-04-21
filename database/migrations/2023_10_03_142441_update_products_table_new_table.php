<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('product_packings', function (Blueprint $table) {
            $table->float('number_of_trade_units_in_package_width')->nullable();
            $table->float('number_of_trade_units_in_full_horizontal_layer_in_global_package')->nullable();
            $table->float('number_of_layers_of_trade_units_in_height_in_global_package')->nullable();
            $table->float('number_of_trade_units_in_length_in_global_package')->nullable();
            $table->float('number_of_trade_units_in_width_in_global_package')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('product_packings', function (Blueprint $table) {
            $table->dropColumn('number_of_layers_of_trade_units_in_vertical_column');
            $table->dropColumn('number_of_layers_of_trade_units_in_height_in_global_package');
            $table->dropColumn('number_of_trade_units_in_full_horizontal_layer_in_global_package');
            $table->dropColumn('number_of_trade_units_in_length_in_global_package');
            $table->dropColumn('number_of_trade_units_in_package_width');
            $table->dropColumn('number_of_trade_units_in_width_in_global_package');
        });
    }
};
