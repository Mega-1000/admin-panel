<?php

use Illuminate\Database\Seeder;
use App\Entities\Status;

class StatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        Status::create([
            'name' => 'przyjete zapytanie ofertowe',
            'color' => $faker->hexColor,
            'message' => '',
            'status' => 'ACTIVE'
        ]);

        Status::create([
            'name' => 'w trakcie analizowania przez konsultanta',
            'color' => $faker->hexColor,
            'message' => '1111 [KONSULTANT/MAGAZYNIER] 2222',
            'status' => 'ACTIVE',
        ]);

        Status::create([
            'name' => 'mozliwa do realizacji',
            'color' => $faker->hexColor,
            'message' =>    '<p>nr oferty&nbsp;<span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">[NUMER-OFERTY]</span></p>
                            
                            <p>&nbsp;</p>
                            
                            <p>DLA &nbsp; &nbsp; &nbsp; &nbsp;<span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">[DANE-KUPUJACEGO]</span> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p>
                            
                            <p>OD&nbsp;</p>
                            
                            <p><span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">&nbsp; &nbsp; &nbsp; &nbsp;MEGA1000 BIS&nbsp;Z O O</span></p>
                            
                            <p><span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">&nbsp; &nbsp; &nbsp; &nbsp;ZEROMSKIEGO 52/18</span></p>
                            
                            <p>&nbsp; &nbsp; &nbsp; &nbsp;50-112 WROCLAW</p>
                            
                            <p>&nbsp; &nbsp; &nbsp; &nbsp;NIP 8971719229&nbsp;</p>
                            
                            <p>&nbsp;</p>
                            
                            <p>PONIZSZY TEKST DO KONCA AKAPITU OBOWIAZUJE DLA OSOB KTORE MIALY Z NAMI TYLKO KONTAKT TELEFONICZNY I JEST WYMAGANY PRZEZ PRZEPISY DOTYCZACE RODO.</p>
                            
                            <p>POZOSTALE OSOBY MOGA GO POMINAC&nbsp; I PRZEJSC DO OFERTY ROZPOZOCZYNAJACEJ SIE PONIZEJ</p>
                            
                            <p>W zwiazku z przeprowadzoną rozmową telefoniczną z nurmerem [TELEFON-KUPUJACEGO] podczas ktorej zostal podany panstwa adres e mailowy przesylamy oferte handlową kt&oacute;rą byliście panstwo zainteresowani.</p>
                            
                            <p>Jeśli czytelnik niniejszej wiadomości nie jest jej zamierzonym adresatem, niniejszym informujemy, że wszelkie rozprowadzanie, dystrybucja lub powielanie niniejszej wiadomości jest zabronione.</p>
                            
                            <p>Jeżeli otrzymałeś tę wiadomość omyłkowo, proszę bezzwłocznie zawiadomić nadawcę wysyłając odpowiedź na niniejszą wiadomość i usunąć ją z poczty.</p>
                            
                            <p>Dziękujemy.</p>
                            
                            <p style="font-size: 14px;">&nbsp;</p>
                            
                            <p style="font-size: 14px;">------ OFERTA ------</p>
                            
                            <p style="font-size: 14px;">UWAGA ZMIANA NR KONTA BANKOWEGO&nbsp; !!!!!!!!!!!!!!!</p>
                            
                            <p>Dane kontaktowe osoby prowadzacej sprawe to</p>
                            
                            <p><span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">[KONSULTANT/MAGAZYNIER]</span></p>
                            
                            <p><span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">Jezeli powyzej brak danych osoby prowadzacej to oznacza to iz od tego momentu wszelkie dyspozycje,zapytania i informacje nalezy dokonywac tylko i wylacznie na naszej stronie na swoim koncie.</span></p>
                            
                            <p><span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">Infomracje droga e mailowa poza wyslaniem potwierdzenie przelewu ktory przyspiesza proces wysylki nie beda brane pod uwage.</span></p>
                            
                            <p style="font-size: 14px;">Aby zalogowac sie na swoje konto nalezy wejsc na strone www.mega1000.pl i nastepnie po najechaniu na ikone ludzika ktora sie znajduje w gornym prawym rogu wpisac swoj email oraz haslo ktore jest nr telefonu chyba ze zostalo zmienione.</p>
                            
                            <p style="font-size: 14px;">Po zalogowaniu na swoim koncie maciem mozliwosc</p>
                            
                            <p style="font-size: 14px;">-wpisanie daty rozpoczecia nadawania przesylki</p>
                            
                            <p style="font-size: 14px;">-uzupelnienia i skorygowania danych do dostawy i faktury</p>
                            
                            <p style="font-size: 14px;">-edytowanie zamowienia i skorygowania go pod wzgledem ilosciowym i towarowym</p>
                            
                            <p style="font-size: 14px;">-sprawdzenie statusu przesylki oraz w przypadku wydania towaru spedycji waraz z numerem lp</p>
                            
                            <p style="font-size: 14px;">-wydrukowania proformy dotyczacej tej oferty</p>
                            
                            <p>Takze pod&nbsp; przyciskiem PODLGAD KOMPLETNEJ OFERTY WRAZ Z ZASADAMI PLATNOSCI I DOSTAWY mozna zawsze edytowac do pogladu ta oferte ktora wlasnie czytacie gdyby zaginela na panstwa koncie pocztowym.</p>
                            
                            <p>&nbsp;</p>
                            
                            <p><span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">[ZAMOWIENIE]</span></p>
                            
                            <p>&nbsp;</p>
                            
                            <p>PODSUMOWANIE</p>
                            
                            <p>&nbsp; &nbsp;wartosc towaru brutto:&nbsp;<span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">[WARTOSC-ZAMOWIENIA]</span></p>
                            
                            <p>&nbsp; &nbsp;koszt transportu brutto :<span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">[KOSZT-TRANSPORTU]</span></p>
                            
                            <p>&nbsp; &nbsp;dodatkowy koszt obslugi: [DODATKOWY-KOSZT-OBSLUGI]</p>
                            
                            <p>&nbsp; &nbsp;wartosc towaru waraz ze wszystkimi kosztami :&nbsp;<span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">[WARTOSC-ZAMOWIENIA-ZE-WSZYSTKIMI-KOSZTAMI]</span></p>
                            
                            <p><font color="#000000" face="Arial, Helvetica, sans-serif">&nbsp; &nbsp;uwagi :</font><span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif; font-size: 14px;">[UWAGI-OSOBY-ZAMAWIAJACEJ]</span></p>
                            
                            <p>ZASADY PLATNOSCI&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;</p>
                            
                            <p>UWAGA ZMIANA NR KONTA BANKOWEGO&nbsp; !!!!!!!!!!!!!!!</p>
                            
                            <p>&nbsp; Potwierdzenie powyzszego zlecenia dokanacie poprzez wplate wartosci&nbsp;<span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">[WARTOSC-ZAMOWIENIA-ZE-WSZYSTKIMI-KOSZTAMI]</span>&nbsp;obejmuje ono towar wraz z paletami &nbsp;na&nbsp;&nbsp;numer konta&nbsp;<span style="font-size: 14px;">58 1600 1156 1846 1675 6000 000</span>1 BANK BGZ BNP PARIBAS SA w tytule wplaty prosimy wpisac tylko &nbsp;&nbsp;<span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">[NUMER-OFERTY]</span>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;<br />
                            W uwagach przelewu&nbsp;prosimy wpisac tylko ten nr oferty nie ma potrzeby pisania nic wiecej.&nbsp;</p>
                            
                            <p>Nazwa odbiorcy znajduje sie na gorze oferty.&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<br />
                            Odpowidzialnosc i wszelkie skutki &nbsp;powiazane z&nbsp;blednie wpisnym nr oferty ponosi kupujacy&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<br />
                            W&nbsp;przypadku wplaty jednym przelewem za kilka zlecen nalezy koniecznie w tytule wyszczegolnic wszytkie numery zlecen.&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<br />
                            Po zaksiegowaniu wplaty zostaniecie panstwo o tym bezzwlocznie &nbsp;poinformowani droga mailowa.</p>
                            
                            <p><span style="font-size: 14px;">Istnieje mozliwosc platnosci przy odbiorze przy czym zaliczke w wysokosci&nbsp;</span><span style="font-size: 14px; color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">[ZALICZKA-PROPONOWANA] zl nalezy wplacic na konto a &nbsp; reszte</span><span style="font-size: 14px;">&nbsp;</span><span style="font-size: 14px; color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">przy rozladunku</span><span style="font-size: 14px;">&nbsp;(+50 zl jako usluge pobraniowa).</span></p>
                            
                            <p>&nbsp;</p>
                            
                            <p style="font-size: 14px;">ODBIOR WLASNY</p>
                            
                            <p>&nbsp;</p>
                            
                            <p style="font-size: 14px;">Wycena towarow jest wykonywana na podstawie cen z najtanszego magazynu.</p>
                            
                            <p style="font-size: 14px;">W przypadku checi odbioru w magazynie w panstwa okolicy&nbsp;prosimy&nbsp;podac kilka okolicznych&nbsp;skladow budowlanych w panstwa rejonie po czym sprawdzimy mozliwosci odbioru&nbsp;w jednym z nich.</p>
                            
                            <p style="font-size: 14px;">Moze sie zdazyc iz ceny w tym przypadku bede nieco inne.</p>
                            
                            <p>&nbsp;</p>
                            
                            <p>TRANSPORT&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;</p>
                            
                            <p>Towar jestesmy w stanie wyslac do 1 dnia roboczego od momentu wyslania potwierdzenia przelewu</p>
                            
                            <p>Dalszy przebieg dostawy przesylki mozna sledzic na stronie internetowej firmy spedycyjnej&nbsp;</p>
                            
                            <p>Numer listu przewozowego wyslemy na e maila po odbiorze przesylki przez firme spedycyjna</p>
                            
                            <p>Czas dostawy przesylki to 95 procent do 24 godzin oraz 99 procent do 48 godzin od nadania przesyslki</p>
                            
                            <p>Jezeli zalezy panstwu na dokladnej dacie dostawy to prosimy nadac przesylke 2 dni wczesniej a w uwagach wpisac date dostarczenia towaru ktora bedzie 2 dni pozniejsza od nadania przesylki (skutecznosc dostawy 99 procent)</p>
                            
                            <p>Prosimy pamietac iz pomijamy wszystkie soboty niedziele i swieta bo spedycje w te dni nie pracuja&nbsp;</p>
                            
                            <p>W przypadku gdyby towar nie doszedl do 2 dni roboczych to prosimy to pilnie zglosic na swoim koncie jako reklamacje.</p>
                            
                            <p>W przypadku dostawy spedycja gdy palety nie sa wyszczegolnione w specyfikacji wraz z wartoscia oznacza to iz beda dostarczone na paletaech bezplatnych</p>
                            
                            <p>Prosimy wziasc pod uwage iz prawie zawsze przesylki sa dostarczane samochodami o masie od 3 do 10 z winda i wjazd na wskazane miejsce musi byl bezproblemowy za co odpowiada zamawiajacy towar.</p>
                            
                            <p>Uruchomienie procesu przygotowywania towaru do wysylki mozna przyspieszyc poprzez odeslanie&nbsp;potwierdzenia wplaty na adares zwrotny</p>
                            
                            <p>Odbiory towaru przez spedycje z naszych magazynow odbywa sie w granicach godziny 11-15&nbsp; po czym towar wieziony jest&nbsp;&nbsp;na magazyn regionalny i w nocy wyjezdza do panstwa magazynu regionlanego</p>
                            
                            <p>W 95 procentach dojezdza tam nastepnego dnia z rana po czym kurierzy swoimi samochodami przewaznie&nbsp;od godziny 8 do 14 rozworza towar do panstwa</p>
                            
                            <p>Nie mamy za bardzo wplywu na pore dnia w ktorej chcemy aby kurier dostarczyl w prawdzie mozna wpisac&nbsp;w uwagach jak sobie panstwo zyczycie aczkolwiek kurier ma przeloty trasy mniej wiecej ustalone z gory&nbsp;i moze nie dostosowac sie do wskazanych informacji czy tez zalecen.</p>
                            
                            <p>Dostawy pozno popoludniowe sa bardzo malo prawdopodobne aczkolwiek moga sie takie przytrafic</p>
                            
                            <p>Jezeli w trakcie dostawy zostalby uszkodzony towar to koniecznie trzeba spisac protokol szkody z podpisem kierowcy</p>
                            
                            <p>Towar jest ubezpieczony i uszkodzenia bedziemy mogli panstwu wyslac bezplatnie</p>
                            
                            <p>Jedoczesnie informujemy iz mozemy wycofac się z umowy dostarczenia towaru bez podania przyczyny zwracajac natychmiastowo wplate</p>
                            
                            <p>&nbsp;</p>
                            
                            <p><span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">Ponizsza oferta jest ofertą kompletu towaru skladajaca sie z wyzej wymienionych skladowych czesci towarow gdzie kazdy element skladowy jest integralna i nierozerwalna czescia tego kompletu.</span></p>
                            
                            <p><span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">Powyzsza oferta zawiera gratisowe opakowania usluge przygotowania kompletowania i zapakowania zamowienia oraz przygotowania dokumentow spedycyjnych w przydku zakupu calosci kompletu towaru.</span></p>
                            
                            <p><span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">Sprzedajacy warunkowo moze dopuscic zwrot dowolnej skladowej czesci kompletu towaru aczkolwiek bedzie to generowalo koszt obsugi powiazany z przyjeciem towaru ,uszkodzeniem opakowan zbiorczych wystawieniem wszelkich dokumentow zwiazanych ze zwrotem itp i&nbsp; bedzie on na zycznie klienta indywidualnie wyliczany.</span></p>
                            
                            <p>&nbsp;</p>
                            
                            <p>z pozdrowieniami &nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;<br />
                            zespol MEGA1000&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;</p>
                            ',
            'status' => 'ACTIVE',
        ]);

        Status::create([
            'name' => 'mozliwa do realizacji kominy',
            'color' => $faker->hexColor,
            'message' =>    '<p>&nbsp;&nbsp;</p>
                            
                            <p>nr oferty&nbsp;<span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">[NUMER-OFERTY]</span></p>
                            
                            <p>&nbsp;</p>
                            
                            <p>DLA &nbsp; &nbsp; &nbsp; &nbsp;<span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">[DANE-KUPUJACEGO]</span>&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p>
                            
                            <p>OD&nbsp;</p>
                            
                            <p><span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">&nbsp; &nbsp; &nbsp; &nbsp;MEGA1000 BIS&nbsp;Z O O</span></p>
                            
                            <p><span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">&nbsp; &nbsp; &nbsp; &nbsp;ZEROMSKIEGO 52/18</span></p>
                            
                            <p>&nbsp; &nbsp; &nbsp; &nbsp;50-112 WROCLAW</p>
                            
                            <p>&nbsp; &nbsp; &nbsp; &nbsp;NIP 8971719229&nbsp;</p>
                            
                            <p>&nbsp;</p>
                            
                            <p>PONIZSZY TEKST DO KONCA AKAPITU OBOWIAZUJE DLA OSOB KTORE MIALY Z NAMI TYLKO KONTAKT TELEFONICZNY I JEST WYMAGANY PRZEZ PRZEPISY DOTYCZACE RODO.</p>
                            
                            <p>POZOSTALE OSOBY MOGA GO POMINAC&nbsp; I PRZEJSC DO OFERTY ROZPOZOCZYNAJACEJ SIE PONIZEJ</p>
                            
                            <p>W zwiazku z przeprowadzoną rozmową telefoniczną z nurmerem [TELEFON-KUPUJACEGO] podczas ktorej zostal podany panstwa adres e mailowy przesylamy oferte handlową kt&oacute;rą byliście panstwo zainteresowani.</p>
                            
                            <p>Jeśli czytelnik niniejszej wiadomości nie jest jej zamierzonym adresatem, niniejszym informujemy, że wszelkie rozprowadzanie, dystrybucja lub powielanie niniejszej wiadomości jest zabronione.</p>
                            
                            <p>Jeżeli otrzymałeś tę wiadomość omyłkowo, proszę bezzwłocznie zawiadomić nadawcę wysyłając odpowiedź na niniejszą wiadomość i usunąć ją z poczty.</p>
                            
                            <p>Dziękujemy.</p>
                            
                            <p style="font-size: 14px;">&nbsp;</p>
                            
                            <p style="font-size: 14px;">------ OFERTA ------</p>
                            
                            <p style="font-size: 14px;">UWAGA ZMIANA NR KONTA BANKOWEGO&nbsp; !!!!!!!!!!!!!!!</p>
                            
                            <p>Dane kontaktowe osoby prowadzacej sprawe to</p>
                            
                            <p><span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">[KONSULTANT/MAGAZYNIER]</span></p>
                            
                            <p><span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">Jezeli powyzej brak danych osoby prowadzacej to oznacza to iz od tego momentu wszelkie dyspozycje,zapytania i informacje nalezy dokonywac tylko i wylacznie na naszej stronie na swoim koncie.</span></p>
                            
                            <p><span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">Infomracje droga e mailowa poza wyslaniem potwierdzenie przelewu ktory przyspiesza proces wysylki nie beda brane pod uwage.</span></p>
                            
                            <p style="font-size: 14px;">Aby zalogowac sie na swoje konto nalezy wejsc na strone www.mega1000.pl i nastepnie po najechaniu na ikone ludzika ktora sie znajduje w gornym prawym rogu wpisac swoj email oraz haslo ktore jest nr telefonu chyba ze zostalo zmienione.</p>
                            
                            <p style="font-size: 14px;">Po zalogowaniu na swoim koncie maciem mozliwosc</p>
                            
                            <p style="font-size: 14px;">-wpisanie daty rozpoczecia nadawania przesylki</p>
                            
                            <p style="font-size: 14px;">-uzupelnienia i skorygowania danych do dostawy i faktury</p>
                            
                            <p style="font-size: 14px;">-edytowanie zamowienia i skorygowania go pod wzgledem ilosciowym i towarowym</p>
                            
                            <p style="font-size: 14px;">-sprawdzenie statusu przesylki oraz w przypadku wydania towaru spedycji waraz z numerem lp</p>
                            
                            <p style="font-size: 14px;">-wydrukowania proformy dotyczacej tej oferty</p>
                            
                            <p>Takze pod&nbsp; przyciskiem PODLGAD KOMPLETNEJ OFERTY WRAZ Z ZASADAMI PLATNOSCI I DOSTAWY mozna zawsze edytowac do pogladu ta oferte ktora wlasnie czytacie gdyby zaginela na panstwa koncie pocztowym.</p>
                            
                            <p>&nbsp;</p>
                            
                            <p><span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">[ZAMOWIENIE]</span></p>
                            
                            <p>PODSUMOWANIE</p>
                            
                            <p>wartosc towaru brutto &nbsp;<span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">[WARTOSC-ZAMOWIENIA]</span></p>
                            
                            <p>koszt transportu brutto &nbsp;<span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">[KOSZT-TRANSPORTU]</span></p>
                            
                            <p>dodatkowy koszt obslugi&nbsp;[DODATKOWY-KOSZT-OBSLUGI]</p>
                            
                            <p>wartosc towaru waraz ze wszystkimi kosztami&nbsp;<span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">[WARTOSC-ZAMOWIENIA-ZE-WSZYSTKIMI-KOSZTAMI]</span></p>
                            
                            <p><font color="#000000" face="Arial, Helvetica, sans-serif">uwagi </font><span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif; font-size: 14px;">[UWAGI-OSOBY-ZAMAWIAJACEJ]</span></p>
                            
                            <p>&nbsp;</p>
                            
                            <p>&nbsp; &nbsp;&nbsp;!!!!!!!!!!!&nbsp;&nbsp; &nbsp;DAJEMY PANSTWU GWARANCJE NANIZSZEJ CENY&nbsp; !!!!!!!!!</p>
                            
                            <p>Jezeli panstwo dostaniecie analogiczny komin o podobnych parametrach jak nasz w nizszej cenie to wskazemy panstwu przyczyny w postaci slabosci konkurencyjnych kominow badz obnizymy&nbsp;panstwu cene jezeli komin okaze sie o podobnych parametrach jak nasz&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;</p>
                            
                            <p>W takim przypadku dzwonic pod numer 691801594 !!!!! &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<br />
                            &nbsp;&nbsp; &nbsp;!!!!!!!!!!!&nbsp;&nbsp; &nbsp;PROSIMY SPRAWDZIC CZY PRODUCENT KOMINOW MA ATEST &#39;CE&#39; NA CALOSC KOMINA CZY TYLKO na poszczegolne elementy czyli tak naprawde nie ma wogole a daje certyfiakty dostawcow poszczegolnych elementow twierdzac ze to jest certyfikat na system kominowy</p>
                            
                            <p>Certyfikaty te sa podobne i wygladaja tak samo tylko w tresci dostaniecie nie nazwe firmy produkujacej tylko dostawce rdzeni,na drugim certyfikacie pustakow itd&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;</p>
                            
                            <p>Ostatni wazny element to sprawdzenie kleju.</p>
                            
                            <p>W naszym przypadku proponujemy kleje w tubach w postaci gotowych mas do wyciskania ktore sa bardzo wygodne w uzyciu poniewaz mozemy w kazdym momencie przerwac prace zatykac tube a w przypadku rozrabianych tracimy niewyrobiona mase ktore twardnieje po krotkim czasie.Dodatkowo kity te maja bardzo wysoka ognioodpornosc do 1250 stopni C.</p>
                            
                            <p>Takze na zyczenie posiadamy kleje producenta rdzeni o nazwie rudomal aczkolwiek pod wzlgedem uzycia jak i wytrzymalosci sa gorszym rozwiazaniem.</p>
                            
                            <p>Prosimy uwazac na kleje ktore sa ostatnio uzywane ze wzgledu na ich niski koszt a po dokladnym sprawdzeniu parametrow nie spelniaja wszystkich norm poniewaz podczas palenia powstajacy kwas siarkowy wyzera ich strukture.</p>
                            
                            <p>Kleje te latwo rozpoznac poniewaz sa &nbsp;produkowane przez polska firme w postaci proszku a producenci kominowi przesypuja je do wlasnych wiaderek z swoim logo. &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;</p>
                            
                            <p>&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;<br />
                            Zwracamy uwage na to iz kominy do kominkow z plaszczem wodnym badz te ktore stawiamy na zewnatrz powinny byc uniwersalne&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;</p>
                            
                            <p>Takze prosimy pamietac iz kominy do kominkow posiadaja tylko welne stabilizacyjna jezeli chcielibyscie panstwo poprawic komfort palenia to proponujemy dokupic welne wraz z kratka przewietrzajaca (koszt przewaznie to ok 100-200 zl)&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;</p>
                            
                            <p>Zyskujecie panstwo przez to szybkosc nagrzewania sie komina w czasie rozpalania co powoduje lepszy ciag przez co dym nie wchodzi spowrotem do domu w tej fazie palenia.</p>
                            
                            <p>Powyzsze systemy kominowe zawieraja dodatkowo oslone zakonczenia komina oraz drzwiczki i betonowa czape chyba ze zostaly ze specyfikacji usuniete&nbsp; (na allegro ich brak).</p>
                            
                            <p>Takze prosimy pamietac iz nasza welna jest ognioodporna i posiada na to atesty w przeciwienstwie do czesto uzywanej welny rockwoola&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;<br />
                            Welna ktorej brak atestu na ognioodpornosc latwo rozpoznac poniewaz zaczyna sie od normy z cyfra 13 a nie 14 i jest to zwykla welna budowlana&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;<br />
                            Latwo to sprawdzic w intrnecie ze brak podania zakresu prac dla welny np z numerem 13,162 i pomimo iz caly komin ma certyfikat to zawiera w sobie welne budowlana&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;</p>
                            
                            <p>Dopytaj zawsze czy sprzedawca nie stosuje wleny rockwool w swoich systemach&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;<br />
                            Jezeli chcecie obnizyc wartosc za zakupione kominy nawet do 1700 zl to mozna to zrobic dokupujac kominek z oferty firmy kratki&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;<br />
                            Zasady zakupu opisane sa na stronie www.mega1000.pl&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p>
                            
                            <p>ZASADY PLATNOSCI&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;</p>
                            
                            <p>!!!!!!!!!&nbsp; UWAGA ZMIANA NR KONTA BANKOWEGO&nbsp; !!!!!!!!!!!!!!! &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;<br />
                            &nbsp; Potwierdzenie powyzszego zlecenia dokanacie poprzez wplate wartosci&nbsp;<span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">[WARTOSC-ZAMOWIENIA-ZE-WSZYSTKIMI-KOSZTAMI]</span>&nbsp;obejmuje ono towar wraz z paletami na&nbsp;&nbsp;numer konta <span style="font-size: 14px;">58 1600 1156 1846 1675 6000 0001</span> &nbsp;BANK BGZ BNP PARIBAS SA w tytule wplaty prosimy wpisac tylko &nbsp;&nbsp;<span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">[NUMER-OFERTY]</span>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;<br />
                            W uwagach przelewu&nbsp;prosimy wpisac tylko ten nr oferty nie ma potrzeby pisania nic wiecej.</p>
                            
                            <p>Odpowidzialnosc i wszelkie skutki &nbsp;powiazane z&nbsp;blednie wpisnym nr oferty ponosi kupujacy.</p>
                            
                            <p>W&nbsp;przypadku wplaty jednym przelewem za kilka zlecen nalezy koniecznie w tytule wyszczegolnic wszytkie numery zlecen.&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p>
                            
                            <p>Po zaksiegowaniu wplaty zostaniecie panstwo o tym bezzwlocznie &nbsp;poinformowani droga mailowa&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;</p>
                            
                            <p><span style="font-size: 14px;">Istnieje mozliwosc platnosci przy odbiorze przy czym zaliczke w wysokosci&nbsp;</span><span style="font-size: 14px; color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">[ZALICZKA-PROPONOWANA] zl nalezy wplacic na konto a reszte przy rozladunku</span><span style="font-size: 14px;">&nbsp;(+50 zl jako usluge pobraniowa).</span></p>
                            
                            <p>ODBIOR WLASNY</p>
                            
                            <p style="font-size: 14px;">Wycena towarow jest wykonywana na podstawie cen z najtanszego magazynu.</p>
                            
                            <p style="font-size: 14px;">W przypadku checi odbioru w magazynie w panstwa okolicy&nbsp;prosimy&nbsp;podac kilka okolicznych&nbsp;skladow budowlanych w panstwa rejonie po czym sprawdzimy mozliwosci odbioru&nbsp;w jednym z nich.</p>
                            
                            <p style="font-size: 14px;">Moze sie zdazyc iz ceny w tym przypadku bede nieco inne.</p>
                            
                            <p>Transport&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p>
                            
                            <p>Towar jestesmy w stanie wyslac do 1 dnia roboczego od momentu wyslania potwierdzenia przelewu.</p>
                            
                            <p>Dalszy przebieg dostawy przesylki mozna sledzic na stornie internetowej firmy spedycyjnej.</p>
                            
                            <p>Numer listu przewozowego wyslemy na e maila po odbiorze przesylki przez firme spedycyjna oraz beda one na stale widoczne na wszym koncie przy zleceniu.</p>
                            
                            <p>Czas dostawy przesylki to 95 procent do 24 godzin oraz 99,99 procent do 48 godzin od nadania przesyslki.</p>
                            
                            <p>Jezeli zalezy panstwu na dokladnej dacie dostawy to prosimy nadac przesylke 2 dni wczesniej a w uwagach&nbsp;wpisac date dostarczenia towaru ktora bedzie 2 dni pozniejsza od nadania przesylki (skutecznosc dostawy 99,99 procent).</p>
                            
                            <p>Prosimy pamietac iz pomijamy wszystkie soboty niedziele i swieta bo spedycje w te dni nie pracuja.</p>
                            
                            <p>W przypadku gdyby towar nie doszedl do 2 dni roboczych po wydaniu towaru to prosimy ustalic ze spedytorem przyczyne.<br />
                            W przypadku dostawy spedycja gdy palety nie sa wyszczegolnione w specyfikacji wraz z wartoscia oznacza to iz beda dostarczone na paletaech bezplatnych.</p>
                            
                            <p>Prosimy wziasc pod uwage iz prawie zawsze przesylki sa dostarczane samochodami o masie od 3 do 10 z winda a wjazd na wskazane miejsce musi byc bezproblemowy za co odpowiada zamawiajacy towar.</p>
                            
                            <p>&nbsp; &nbsp;&nbsp;</p>
                            
                            <p>Uruchomienie procesu przygotowywania towaru do wysylki mozna przyspieszyc poprzez odeslanie&nbsp;potwierdzenia wplaty na adares zwrotny&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;</p>
                            
                            <p>Odbiory towaru przez spedycje z naszych magazynow odbywa sie w granicach godziny 11-15&nbsp; po czym towar wieziony jest&nbsp;&nbsp;na magazyn regionalny i w nocy wyjezdza do panstwa magazynu regionlanego.</p>
                            
                            <p>W 95 procentach dojezdza tam nastepnego dnia z rana po czym kurierzy swoimi samochodami przewaznie&nbsp;od godziny 8 do 14 rozworza towar do panstwa.</p>
                            
                            <p>Nie mamy za bardzo wplywu na pore dnia w ktorej chcemy aby kurier dostarczyl w prawdzie mozna wpisac&nbsp;w uwagach jak sobie panstwo zyczycie aczkolwiek kurier ma przeloty trasy mniej wiecej ustalone z gory&nbsp;i moze nie dostosowac sie do wskazanych informacji czy tez zalecen.</p>
                            
                            <p>Dostawy pozno popoludniowe sa bardzo malo prawdopodobne aczkolwiek moga sie takie przytrafic.</p>
                            
                            <p>Jezeli w trakcie dostawy zostalby uszkodzony towar to koniecznie trzeba spisac protokol szkody z podpisem kierowcy. &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;</p>
                            
                            <p>Towar jest ubezpieczony i uszkodzenia bedziemy mogli panstwu wyslac bezplatnie&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;</p>
                            
                            <p>Poprzez wplate zaliczki przyjmuja panstwo zasady wczesniej opisane jednoczesnie zgadzaja się panstwo na przeslanie faktury droga e &nbsp;mailowa na podany adres e mailowy.</p>
                            
                            <p>Jednoczesnie informujemy iz faktura taka na podstawie przepisow jest pelnowartosciowa faktrua i nie wyamaga podpisow ani pieczatek&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;</p>
                            
                            <p>Faktura będzie do panstwa wyslana najpozniej do 5 nastpenego &nbsp;miesiaca liczac od daty dostawy&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;</p>
                            
                            <p>Jedoczesnie informujemy iz mozemy wycofac się z umowy dostarczenia towaru bez podania przyczyn zwracajac natychmiastowo wplate &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;</p>
                            
                            <p>W przypadku reklamacji klient zobowiazuje sie do zgloszenia jej na naszej stronie na swoim koncie.<br />
                            &nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;<br />
                            <span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">Ponizsza oferta jest ofertą kompletu towaru skladajaca sie z wyzej wymienionych skladowych czesci towarow gdzie kazdy element skladowy jest integralna i nierozerwalna czescia tego kompletu.</span></p>
                            
                            <p><span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">Ponizsza oferta zawiera gratisowe opakowania usluge przygotowania kompletowania i zapakowania zamowienia oraz przygotowania dokumentow spedycyjnych w przydku zakupu calosci kompletu towaru.</span></p>
                            
                            <p><span style="color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif;">Sprzedajacy warunkowo moze dopuscic zwrot dowolnej skladowej czesci kompletu towaru aczkolwiek bedzie to generowalo koszt obsugi powiazany z przyjeciem towaru ,uszkodzeniem opakowan zbiorczych wystawieniem wszelkich dokumentow zwiazanych ze zwrotem itp i bedzie on na zycznie klienta indywidualnie wyliczany.</span></p>
                            
                            <p>&nbsp;</p>
                            
                            <p>z pozdrowieniami &nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;<br />
                            zespol MEGA1000&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;</p>
                            ',
            'status' => 'ACTIVE',
        ]);

        Status::create([
            'name' => 'w trakcie realizacji',
            'color' => $faker->hexColor,
            'message' => '',
            'status' => 'ACTIVE'
        ]);

        Status::create([
            'name' => 'oferta zakonczona',
            'color' => $faker->hexColor,
            'message' => '',
            'status' => 'ACTIVE'
        ]);

        Status::create([
            'name' => 'oferta oczekujaca',
            'color' => $faker->hexColor,
            'message' => '',
            'status' => 'ACTIVE'
        ]);

        Status::create([
            'name' => 'oferta bez realizacji',
            'color' => $faker->hexColor,
            'message' => '',
            'status' => 'ACTIVE'
        ]);

    }
}
