<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use TCG\Voyager\Models\MenuItem;
use TCG\Voyager\Models\Permission;

class PriceAnalyzer extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$item = new MenuItem();
		$item->menu_id = 1;
		$item->title = 'Analizator cen';
		$item->url = '';
		$item->target = '_self';
		$item->icon_class = 'voyager-money';
		$item->color = null;
		$item->parent_id = 20;
		$item->order = 5;
		$item->route = 'product_analyzer.index';
		$item->parameters = null;
		$item->save();
		
		$perm = new Permission();
		$perm->id = 171;
		$perm->key = 'browse_product_analyzer';
		$perm->table_name = 'product_analyzer';
		$perm->save();
		$perm = new Permission();
		$perm->id = 172;
		$perm->key = 'read_product_analyzer';
		$perm->table_name = 'product_analyzer';
		$perm->save();
		
		
		Schema::table('products', function (Blueprint $table) {
			$table->string('allegro_analyze_id', 36);
		});
		
		Schema::create('product_analyzer', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('product_id');
			$table->decimal('price');
			$table->dateTime('analyze_date');
			
			$table->foreign('product_id')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
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
			$table->dropColumn('allegro_analyze_id');
		});
		
		Schema::dropIfExists('product_analyzer');
	}
}
