<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrderSources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_sources', function (Blueprint $table) {
            $table->increments('id');
	        $table->string('name', 100);
	        $table->string('short_name', 3);
            $table->boolean('multiple');
            $table->softDeletes();
        });
	
	    DB::table('order_sources')->insert([
		    'name' => 'Allegro.pl',
		    'short_name' => 'A',
		    'multiple' => true
	    ]);
	    
	    DB::table('order_sources')->insert([
		    'name' => 'mega1000.pl',
		    'short_name' => 'M',
		    'multiple' => false
	    ]);
	    
	    Schema::create('firm_sources', function (Blueprint $table) {
		    $table->increments('id');
		    $table->unsignedInteger('firm_id');
		    $table->unsignedInteger('order_source_id');
		    $table->softDeletes();
		
		    $table->foreign('firm_id')->references('id')->on('firms')->onUpdate('cascade')->onDelete('cascade');
		    $table->foreign('order_source_id')->references('id')->on('order_sources')->onUpdate('cascade')->onDelete('cascade');
		
		    $table->unique(['firm_id', 'order_source_id']);
	    });
	
	    Schema::table('orders', function (Blueprint $table) {
		    $table->unsignedInteger('firm_source_id')->nullable()->after('status_id');
		    $table->foreign('firm_source_id')->references('id')->on('firm_sources')->onUpdate('cascade')->onDelete('cascade');
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('firm_sources', function (Blueprint $table) {
		    $table->dropForeign('firm_sources_firm_id_foreign');
		    $table->dropForeign('firm_sources_order_source_id_foreign');
	    });
	
	    Schema::table('orders', function (Blueprint $table) {
		    $table->dropForeign('orders_firm_source_id_foreign');
		    $table->dropColumn('firm_source_id');
	    });
	    
	    Schema::dropIfExists('firm_sources');
        Schema::dropIfExists('order_sources');
	    
    }
}
