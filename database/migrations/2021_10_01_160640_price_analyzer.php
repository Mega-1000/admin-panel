<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use TCG\Voyager\Models\MenuItem;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Models\Menu;


class PriceAnalyzer extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Menu::firstOrCreate([
            'name' => 'admin',
        ]);

		$item = new MenuItem();
		$item->menu_id = 1;
		$item->title = 'Analizator cen';
		$item->url = '';
		$item->target = '_self';
		$item->icon_class = 'voyager-pie-chart';
		$item->color = null;
		$item->parent_id = 20;
		$item->order = 5;
		$item->route = 'product_analyzer.index';
		$item->parameters = null;
		$item->save();
		
		$perm = new Permission();
		$perm->key = 'browse_product_analyzer';
		$perm->table_name = 'product_analyzer';
		$perm->save();
		DB::raw('INSERT INTO permission_role (permission_id, role_id) VALUES (' . $perm->id .' }, 1)');
		
		$perm = new Permission();
		$perm->key = 'read_product_analyzer';
		$perm->table_name = 'product_analyzer';
		$perm->save();
		
		DB::raw('INSERT INTO permission_role (permission_id, role_id) VALUES (' . $perm->id .' }, 1)');
		
		Schema::create('product_analyzers', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('product_id');
			$table->decimal('price')->nullable();
			$table->dateTime('analyze_date')->nullable();
			$table->string('parse_service', 50);
			$table->string('parse_url', 500);
			
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
		Schema::dropIfExists('product_analyzers');
	}
}
