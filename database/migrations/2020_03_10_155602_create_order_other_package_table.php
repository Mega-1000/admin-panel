<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderOtherPackageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_other_packages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id');
            $table->decimal('price', 8, 2)->nullable();
            $table->enum('type', ['not_calculable', 'from_factory']);
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_other_packages');
    }
}
