<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create('package_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('info');
            $table->integer('sizeA');
            $table->integer('sizeB');
            $table->integer('sizeC');
            $table->time('accept_time');
            $table->string('accept_time_info');
            $table->time('max_time');
            $table->string('max_time_info');
            $table->string('service_courier_name');
            $table->string('delivery_courier_name');
            $table->integer('weight');
            $table->string('container_type');
            $table->string('shape');
            $table->integer('notice_max_lenght');
            $table->string('content');
            $table->integer('cod_cost');
            $table->integer('approx_cost_client');
            $table->integer('approx_cost_firm');            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_templates');
    }
}
