<?php

use Illuminate\Database\Migrations\Migration;
use TCG\Voyager\Models\Setting;

class UpdateSettingsItem extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if ($setting = Setting::where(['key' => 'site.allegro_order_change_status_msg'])->first()) {
			$setting->key = 'site.new_allegro_order_on_sello_import_msg';
			$setting->group = 'Site';
			$setting->save();
		}
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		if ($setting = Setting::where(['key' => 'site.new_allegro_order_on_sello_import_msg'])->first()) {
			$setting->key = 'site.allegro_order_change_status_msg';
			$setting->save();
		}
	}
}
