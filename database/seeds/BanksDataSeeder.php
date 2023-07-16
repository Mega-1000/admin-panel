<?php
namespace Database\Seeders;

use App\Bank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BanksDataSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('banks')->delete();
        $locationImgBanks = '/images/banks/';

        DB::table('banks')->insert([
            [
                'name' => 'Bank PKO BP',
                'site_url' => 'http://ipko.pl',
                'img_url' => $locationImgBanks . 'pko-bp-logo.png',
            ],
            [
                'name' => 'Inteligo',
                'site_url' => 'https://inteligo.pl/secure',
                'img_url' => $locationImgBanks . 'inteligoBank.png',
            ],
            [
                'name' => 'mBank',
                'site_url' => 'https://online.mbank.pl',
                'img_url' => $locationImgBanks . 'mbank-logo.png',
            ],
            [
                'name' => 'PKO24',
                'site_url' => 'https://www.pekao24.pl/ClientLogon.html',
                'img_url' => $locationImgBanks . 'pekaobp.png',
            ],
            [
                'name' => 'Santander Bank Polski',
                'site_url' => 'https://www.centrum24.pl/centrum24-web/login?_ga=2.53058664.1393986100.1565273480-1402816162.1565273480',
                'img_url' => $locationImgBanks . 'santander.png',
            ],
            [
                'name' => 'Alior Bank',
                'site_url' => 'https://system.aliorbank.pl/sign-in',
                'img_url' => $locationImgBanks . 'aliorBank.png',
            ],
            [
                'name' => 'ING Bank Śląski',
                'site_url' => 'https://login.ingbank.pl/mojeing/app/#login',
                'img_url' => $locationImgBanks . 'ingBank.png',
            ],
            [
                'name' => 'BNP PARIBAS Bank Polski',
                'site_url' => 'https://planet.bnpparibas.pl/',
                'img_url' => $locationImgBanks . 'bnpParibasBank.png',
            ],
            [
                'name' => 'Bank Ochrony Środowiska',
                'site_url' => 'https://bosbank24.pl/twojekonto',
                'img_url' => $locationImgBanks . 'bosBank.png',
            ],
            [
                'name' => 'Bank Millennium',
                'site_url' => 'https://www.bankmillennium.pl/logowanie',
                'img_url' => $locationImgBanks . 'millenniumBank.png',
            ],
            [
                'name' => 'Bank Pocztowy',
                'site_url' => 'https://www.pocztowy24.pl/cbp-webapp/login',
                'img_url' => $locationImgBanks . 'bankPocztowy.png',
            ],
            [
                'name' => 'Citi Handlowy',
                'site_url' => 'https://www.citibankonline.pl/apps/auth/signin/?_Locale=pl',
                'img_url' => $locationImgBanks . 'cityHandlowyBank.png',
            ],
            [
                'name' => 'Credit Agricole',
                'site_url' => 'https://ca24.credit-agricole.pl/',
                'img_url' => $locationImgBanks . 'creditagricoleBank.png',
            ],
            [
                'name' => 'EnveloBank',
                'site_url' => 'https://online.envelobank.pl/',
                'img_url' => $locationImgBanks . 'enveloBank.png',
            ],
            [
                'name' => 'EuroBank',
                'site_url' => 'https://online.eurobank.pl/nbi/bezpieczenstwo/logowanie',
                'img_url' => $locationImgBanks . 'euroBank.png',
            ],
            [
                'name' => 'Get In Bank',
                'site_url' => 'https://secure.getinbank.pl/?_ga=2.15341366.1699792741.1565336467-1889268706.1565336467',
                'img_url' => $locationImgBanks . 'getinBank.png',
            ],
            [
                'name' => 'Idea bank',
                'site_url' => 'https://www.ideabank.pl/logowanie',
                'img_url' => $locationImgBanks . 'ideaBank.png',
            ],
            [
                'name' => 'Nest Bank',
                'site_url' => 'https://login.nestbank.pl/login',
                'img_url' => $locationImgBanks . 'nestbank.png',
            ],
            [
                'name' => 'Plus Bank',
                'site_url' => 'https://plusbank24.pl/',
                'img_url' => $locationImgBanks . 'plusBankI.png',
            ],
            [
                'name' => 'Deutsche Bank',
                'site_url' => 'https://db-direct.db.com/',
                'img_url' => $locationImgBanks . 'deutcheBankI.png',
            ],
        ]);
    }

}
