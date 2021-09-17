<?php

use App\Entities\Tag;
use Illuminate\Database\Migrations\Migration;
use TCG\Voyager\Models\Setting;

class UpdateSettings extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$setting = Setting::firstOrNew(['key' => 'site.new_allegro_order_msg']);
		$setting->key = 'allegro.new_allegro_order_msg';
		$setting->display_name = 'Wiadomość dla nowego zamówienia allegro (allegro cron)';
		$setting->group = 'Allegro';
		$setting->save();
		
		$setting = Setting::firstOrNew(['key' => 'site.new_allegro_order_on_sello_import_msg']);
		$setting->key = 'allegro.new_allegro_order_on_sello_import_msg';
		$setting->display_name = 'Wiadomość dla nowego zamówienia allegro (sello import)';
		$setting->group = 'Allegro';
		$setting->save();
		
		$setting = Setting::firstOrNew(['key' => 'allegro.check_address_msg']);
		$setting->display_name = 'Sprawdz dane do dostawy i faktury';
		$setting->value = 'SPRAWDZ DANE DO DOSTAWY I FAKTURY I EWENTUALNIE JE SKORYGUJ</br>
[LINK-DO-FORMULARZA-ADRESU]</br>
W związku z dokonanymi zakupami na portalu allegro informujemy w iż najblizszym czasie będzie wysyłane do państwa zamówienie.</br>
Prosimy poniżej sprawdzić dane do dostawy oraz dane do faktury ponieważ takimi danymi teleadresowymi będziemy się posługiwać.</br>
Jeżeli chcielibyście jeszcze je skorygować to prosimy o dokonanie takich czynności i zatwierdzenie skorygowanych danych.</br>
';
		$setting->details = '';
		$setting->type = 'text_area';
		$setting->order = '8';
		$setting->group = 'Allegro';
		$setting->save();
		
		$setting = Setting::firstOrNew(['key' => 'allegro.address_changed_msg']);
		$setting->display_name = 'Powiadomienie po zmianie danych przez klienta';
		$setting->value = '';
		$setting->details = '';
		$setting->type = 'text_area';
		$setting->order = '8';
		$setting->group = 'Allegro';
		$setting->save();
		
		$setting = Setting::firstOrNew(['key' => 'allegro.final_confirmation_msg']);
		$setting->display_name = 'Ostateczne potwierdzenie zgodnosci oferty pod wzlgdedem danych i asortymentu';
		$setting->value = '';
		$setting->details = '';
		$setting->type = 'text_area';
		$setting->order = '9';
		$setting->group = 'Allegro';
		$setting->save();
		
		Tag::create(['name' => '[LINK-DO-FORMULARZA-ADRESU]', 'handler' => 'addressFormLink']);
		Tag::create(['name' => '[LINK-DO-FORMULARZA-NIEZGODNOŚCI]', 'handler' => 'declineProformFormLink']);
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$setting = Setting::firstOrNew(['key' => 'allegro.new_allegro_order_msg']);
		$setting->key = 'site.new_allegro_order_msg';
		$setting->group = 'Site';
		$setting->save();
		
		$setting = Setting::firstOrNew(['key' => 'allegro.new_allegro_order_on_sello_import_msg']);
		$setting->key = 'site.new_allegro_order_on_sello_import_msg';
		$setting->group = 'Site';
		$setting->save();
		
		Setting::where('key', '=', 'allegro.check_address_msg')->delete();
		Setting::where('key', '=', 'allegro.address_changed_msg')->delete();
		Setting::where('key', '=', 'allegro.final_confirmation_msg')->delete();
		
		Tag::where('name', '=', '[LINK-DO-FORMULARZA-ADRESU]')->delete();
		Tag::where('name', '=', '[LINK-DO-FORMULARZA-NIEZGODNOŚCI]')->delete();
	}
}
