<?php

use Illuminate\Database\Migrations\Migration;
use TCG\Voyager\Models\Setting;

class UpdateSettings2 extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$setting = Setting::firstOrNew(['key' => 'allegro.order_items_constructed_msg']);
		$setting->display_name = 'Oferta zostałą przygotowana i oczekuje na odbior przez kuriera';
		$setting->group = 'Allegro';
		$setting->value = 'W związku z dokonanymi zakupami na portalu allegro na koncie EPH3 informujemy iż produkcja państawa przesyłek została z oferty nr [NUMER-OFERTY] została zakończona i oczekuje na odbioru przez kuriera' . PHP_EOL .
'Kurierzey zazwyczaj odbierają przesyłki około godzin podanych poniżej i jeżeli ona jeszcze nie nastąpiła to jest bardzo duże prawdopodobieństwo że w dniu dzisiejszym zostanie ona jeszcze odebrana (z pominięciem soboty i niedzieli )' . PHP_EOL .
'Informacja o jej odbiorze zostanie jeszcze wysłana w momencie odbioru przez kuriera w oddzielnej wiadomości gdy zostanie do dokonane.' . PHP_EOL .
'INPOST - 14' . PHP_EOL .
'DPD - 16' . PHP_EOL .
'GLS - 16' . PHP_EOL .
'POCZTA POLSKA ( palety ) - od 11 do 16' . PHP_EOL .
PHP_EOL .
'Z pozdrowieniami' . PHP_EOL;
		
		$setting->details = '';
		$setting->type = 'text_area';
		$setting->order = '9';
		$setting->save();
		
		$settingSite = $setting->replicate();
		$settingSite->key = 'site.order_items_constructed_msg';
		$settingSite->group = 'Site';
		$settingSite->order = '9';
		$settingSite->save();
		
		$setting = Setting::firstOrNew(['key' => 'allegro.order_items_redeemed_msg']);
		$setting->display_name = 'Towar został odebrany przez kuriera';
		$setting->group = 'Allegro';
		$setting->value = 'W związku z dokonanymi zakupami na portalu allegro na koncie EPH3 informujemy iż państwa towar z oferty nr [NUMER-OFERTY] został odebrany przez kuriera.' . PHP_EOL .
			'Jednocześnie informujemy iż faktura będzie wysłana na adres e mailowy najpózniej do 15 dnia nastepego miesiaca po otrzymaniu towaru.' . PHP_EOL .
			'Jest to zgodne z art. 106i ust. 1 ustawy o VAT fakturę należy wystawić nie później niż 15. dnia miesiąca następującego po miesiącu, w którym dokonano dostawy towaru lub wykonano usługę.' . PHP_EOL .
			'W przypadku gdybyście państwo chcieli ją otrzymać wcześniej to będzie taka możliwość po zatwierdzeniu jej poprawności a dokonać tego będzie można w formularzu który przyjdzie w następnym e mailu' . PHP_EOL .
			 'Z pozdrowieniami' . PHP_EOL .
			PHP_EOL .
			'Z pozdrowieniami' . PHP_EOL;
		
		$setting->details = '';
		$setting->type = 'text_area';
		$setting->order = '10';
		$setting->save();
		
		$settingSite = $setting->replicate();
		$settingSite->key = 'site.order_items_redeemed_msg';
		$settingSite->group = 'Site';
		$settingSite->order = '10';
		$settingSite->save();
		
		$setting = Setting::firstOrNew(['key' => 'allegro.order_invoice_msg']);
		$setting->display_name = 'Faktura za zakupy dokonane na allegro';
		$setting->group = 'Allegro';
		$setting->value = 'W związku z dokonanymi zakupami na portalu allegro na koncie EPH3 informujemy iż przygotowujemy się do wystawienia faktury wynikającej z dostawy oferty nr [NUMER-OFERTY].' . PHP_EOL .
'Prosimy o skopiowanie linku i wklejenie go w przeglądarke po czym otrzymacie formularz za pomocą którego można' . PHP_EOL .
 '- zgłosić prośbe o fakturę w dniu dzisiejszym która zostanie wysłana na e maila około godziny 23:45 ( według regulaminu standartowo wychodzą do 15 dnia nastepnego miesiąca po otrzymaniu towaru)' . PHP_EOL .
 '- skontrolować i ewentualnie skorygować dane do faktury . ( w przypadku nie zatwierdzenia tych danych system sam automatycznie je zatwierdzi według danych z formularza najpóźniej do 15 dnia nastepnego miesiąca )' . PHP_EOL .
 '- zgłosić bląd w asortymencie względem faktury' . PHP_EOL .
		'[LINK-DO-FORMULARZA-ADRESU] ' . PHP_EOL .
			'[LINK-DO-FORMULARZA-NIEZGODNOSCI]';
		
		$setting->details = '';
		$setting->type = 'text_area';
		$setting->order = '11';
		$setting->save();
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Setting::where('key', '=', 'allegro.order_items_constructed_msg')->delete();
		Setting::where('key', '=', 'site.order_items_constructed_msg')->delete();
		
		Setting::where('key', '=', 'allegro.order_items_redeemed_msg')->delete();
		Setting::where('key', '=', 'site.order_items_redeemed_msg')->delete();
	}
}
