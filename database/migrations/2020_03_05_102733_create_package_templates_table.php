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
            $table->string('symbol')->nullable();
            $table->string('info')->nullable();
            $table->integer('sizeA')->nullable();
            $table->integer('sizeB')->nullable();
            $table->integer('sizeC')->nullable();
            $table->time('accept_time')->nullable();
            $table->string('accept_time_info')->nullable();
            $table->time('max_time')->nullable();
            $table->string('max_time_info')->nullable();
            $table->string('service_courier_name')->nullable();
            $table->string('delivery_courier_name')->nullable();
            $table->decimal('weight')->nullable();
            $table->string('container_type')->nullable();
            $table->string('shape')->nullable();
            $table->integer('notice_max_lenght');
            $table->string('content')->nullable();
            $table->decimal('cod_cost')->nullable();
            $table->decimal('approx_cost_client')->nullable();
            $table->decimal('approx_cost_firm')->nullable();
            $table->decimal('max_weight')->nullable();
            $table->integer('volume')->nullable();
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
