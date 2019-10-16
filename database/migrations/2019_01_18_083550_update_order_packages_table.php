<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrderPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_packages', function (Blueprint $table) {
            $table->string('service_courier_name')->comment('Spedycja obsługująca')->after('courier_name')->nullable();
            $table->renameColumn('courier_name', 'delivery_courier_name');
            $table->decimal('quantity')->nullable()->after('weight');
            $table->string('container_type')->nullable()->after('quantity');
            $table->string('shape')->nullable()->after('container_type');
            $table->string('chosen_data_template')->nullable()->comment('Template na podstawie, którego wybrano autouzupełnienie danych');
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
