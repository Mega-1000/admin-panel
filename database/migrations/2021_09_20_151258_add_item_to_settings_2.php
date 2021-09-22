<?php

use Illuminate\Database\Migrations\Migration;
use TCG\Voyager\Models\Setting;

class AddItemToSettings2 extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$setting = Setting::firstOrNew(['key' => 'site.check_address_msg']);
		
		$setting->display_name = 'Sprawdz dane do dostawy i faktury';
		$setting->value = 'Prosimy o sprawdzenie danych do dostawy i faktury ponieważ nasz system wykrył iż nie wszystkie są poprawne
Prosimy o zalogowanie się na swoim koncie na naszej stronie i skorygowanie danych do dostawy i faktury.
W przypadku gdyby były problemy prosimy napisac wysłac informacje poprzez chata który także znajduje się w tym samym mijescu.';
		$setting->details = '';
		$setting->type = 'text_area';
		$setting->order = '6';
		$setting->group = 'Site';
		$setting->save();
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Setting::where('key', '=', 'site.check_address_msg')->delete();
	}
}
