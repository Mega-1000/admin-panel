<?php

use Illuminate\Database\Migrations\Migration;
use TCG\Voyager\Models\Setting;

class AddItemToSettings extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$setting = Setting::firstOrNew(['key' => 'site.allegro_order_change_status_msg']);
		
		$setting->display_name = 'Wiadomość dla nowego zamówienia allegro';
		$setting->value = 'W zwiazku z dokanymi zakupami na portalu ALLEGRO wysyłąmy państwu na ten moment fakturę proforme aby była możliwość operacyjno-magazynowa<br/>
			Faktura ta będzie przekształcona na fakture z oryginalnym numerem po zatwierdzeniu przez państwa zgodności danych oraz cen i asortymentu otrzymanego w dostawie.<br/>
			Możliwość zatwierdzenia zmiany faktury proformy na fakture oryginalna będzie możliwa nie wcześniej niż po 14 dniach od momentu dostawy a nie pożńiej niż 15 dnia nastepnego miesiąca po zakupie.<br/>
			Jest to zgodne z art. 106i ust. 1 ustawy o VAT fakturę należy wystawić nie później niż 15. dnia miesiąca następującego po miesiącu, w którym dokonano dostawy towaru lub wykonano usługę.';
		$setting->details = '';
		$setting->type = 'text_area';
		$setting->order = '7';
		$setting->group = 'site';
		$setting->save();
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Setting::where('key', '=', 'site.allegro_order_change_status_msg')->delete();
	}
}
