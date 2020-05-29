<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaczkomatFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_packings', function (Blueprint $table) {
            $table->dropColumn('number_of_items_per_25_kg');
            $table->dropColumn('number_of_volume_items_for_paczkomat');
            $table->dropColumn('inpost_courier_type');
            $table->integer('paczkomat_size_a')->nullable();
            $table->integer('paczkomat_size_b')->nullable();
            $table->integer('paczkomat_size_c')->nullable();
            $table->integer('allegro_courier')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_packings', function (Blueprint $table) {
            $table->decimal('number_of_items_per_25_kg',15,4)->nullable()->comment('Ilość sztuk na 25kg');
            $table->decimal('number_of_volume_items_for_paczkomat', 15, 4)->nullable()->comment('Ilość sztuk w całkowitej objętości dla paczkomatu');
            $table->string('inpost_courier_type')->nullable()->comment('Rodzaj kuriera inpost');
            $table->dropColumn('paczkomat_size_a');
            $table->dropColumn('paczkomat_size_b');
            $table->dropColumn('paczkomat_size_c');
            $table->dropColumn('allegro_courier');
        });
    }
}
