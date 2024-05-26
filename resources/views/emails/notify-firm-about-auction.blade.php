@if(!$auction->firms->where('firm_id', $firm->id)->first()?->practices_representatives_policy && !\App\FirmRepresent::where('email_of_employee', $auction->firms->where('firm_id', $firm->id)->first()->email_of_employee)->first())
    <p>
        W imieniu naszego klienta, chcielibyśmy ogłosić przetarg na dostawę styropianu dotyczącego rejonu obsługiwanego przez Ciebie.
    </p>
    <p>
        Aby zwiększyć prawdopodobieństwo wygrania przetargu względem innych producentów także w nim uczestniczących zalecane jest, abyście zaproponowali w jaki sposób uzyskać najniższą cenę waszego asortymentu.
    </p>
    <p>
        Jeżeli nie macie polityki bonusów itp. systemów, to proponujemy dokonywać sprzedaży bezpośrednio przez nasz system, ponieważ gwarantuje on brak dodawania narzutu do cen, które dodają hurtownie.
    </p>
    <p>
        Aby zaproponować swoją cenę w przetargu prosimy o użycie linku poniżej, który przeniesie was bezpośrednio do tego modułu.
    </p>
    <p>
        Informacja przez kogo została zaproponowana cena danego producenta styropianu jest niewidoczna dla wszystkich, ponieważ może być to cena wpisana bezpośrednio przez fabrykę lub przedstawiciela lub nasz system.
    </p>
    <p>
        Dodatkowo pod tym linkiem można zobaczyć tabelę z listą zaproponowanych cen przez innych oferentów oraz na którym miejscu znajdujecie się na tej liście.
    </p>
    <p>
        <a href="{{ route('auctions.offer.create', ['token' => $token]) }}">LINK DO MODUŁU PRZETARGU</a>
    </p>
    <p>
        W przypadku gdy jednak polityka firmy jest oparta na zasadzie dodatkowych bonusów dla dużych odbiorców, to prosimy podać kilku z nich, a my przekażemy im informacje o danym przetargu.
    </p>
    <p>
        Z doświadczenia wiemy, że firmy takie zaniżają cenę i często oddają jakąś część swojego bonusa, co powoduje większą atrakcyjność cenową aniżeli ceny, które zaproponujecie nam bez tych bonusów.
    </p>
    <p>
        Jeżeli chcecie natomiast zawrzeć od razu wszelkie bonusy w ofercie i skonta za przedpłatę, co spowoduje równe warunki startowe w przetargu, to warto dokonać to przez nasz system, ponieważ klient otrzyma najniższą cenę przez nasz system.
    </p>
    <p>
        Gdy jednak brak możliwości zastosowania takich warunków, to prosimy za pomocą linku poniżej podać nazwy takich firm.
    </p>
    <p>
        Prosimy nie brać pod uwagę miejsca dostawy i tym się nie sugerować przy ich podawaniu, bo całkowicie na tym nam nie zależy, tylko podać firmy, które mają duże bonusy i chęć, aby tę cenę zaniżyć, bo i tak towar jedzie bezpośrednio z fabryki i nie ma znaczenia, kto go awizuje.
    </p>
    <p>
        Prosimy pamiętać, że powinno Wam zależeć na wskazaniu, w jaki sposób uzyskać jak najniższe ceny dla klienta, ponieważ zapytanie takie idzie także do przynajmniej kilkunastu producentów, na tle których musicie jak najlepiej wypaść, a przeważnie cena jest jednym z ważniejszych czynników.
    </p>
    <p>
        Aby dokonać wpisu o takich firmach prosimy kliknąć link poniżej
    </p>
    <p>
        <a href="{{ route('create-represents', ['firm' => $firm->id, 'email' => $auction->firms->where('firm_id', $firm->id)->first()->email_of_employee]) }}">KLIKNIJ LINK ABY JE PODAĆ</a>
    </p>
    <p>
        Firmy lub telefony do tych firm trzeba dodać do chatu, ale tak aby były widoczne tylko i wyłącznie dla nas.
    </p>
@else

    <h1>
        !! Prosimy o wypełnienie formularza przetargowego, nawet jeśli Państwa oferta nie będzie najniższa, ponieważ klient może kierować się również jakością. !!
    </h1>
    <h2>
        Przetarg dotyczy cen firmy: {{ $firm->name }}
    </h2>
    <br>
    <br>
    W imieniu naszego klienta, chcielibyśmy ogłosić przetarg na
    dostawę styropianu.
    <br>
    <br>
    Prosimy kliknąć poniższy link który przeniesie Was do
    modułu na którym prowadzony jest przetarg.
    <br>
    <br>

    Podane tam będą wszystkie parametry oraz asortyment dotyczący przetargu oraz
    możliwość wprowadzenie swoich cen w m3 lub w opakowaniach.
    <br>
    <br>

    <a href="{{ route('auctions.offer.create', ['token' => $token]) }}">{{ route('auctions.offer.create', ['token' => $token]) }}</a>

    <br>
    <br>
    Ze względu na fakt
    że do przetargu jest zaproszonych wielu sprzedawców i producentów prosimy podać
    najniższe możliwe ceny aby zwiększyć prawdopodobieństwo wyboru waszej oferty.

    <br>
    <br>
    Chcielibyśmy również zaznaczyć, że klient nie musi zdecydować się na ofertę najtańszego styropianu więc prosimy o podanie najniższej ceny która jest dla Was akceptowalna.

    <br>
    <br>
    Podane przez państwa ceny nie są widoczne dla innych oferentów jedynie dla klienta. Widoczna dla oferenów jest jedynie najniższa cena dla danego rodzaju styropianu zaproponowana przez jednego z uczestników.

    <br>
    <br>
    Ostatecznie sprzedaż klientowi może
    się odbyć przez was bezpośrednio lub przez naszą firmę lub ewentualnie przez
    inna firmę którą możecie wskazać jeżeli sprzedaż przez nich gwarantuje
    najniższą cenę.

@endif

<br>
<br>

@if ($firm->products->where('date_of_price_change', '<', now())->count() > 0)
    <div style="color: red; font-weight: bold">
        !! UWAGA !! Zauważyliśmy, że cenniki firmy którą reprezentujesz w naszym systemie mogą być nie aktualne. Prosimy o zaaktualizowanie ich lub zmiane daty ponownego powiadomienia w panelu pod linkiem poniżej:
        <br>
        <br>
        <a href="https://mega1000.pl/firms/przetargi?firmToken={{ $firm->access_token }}">https://mega1000.pl/firms/przetargi?firmToken={{ $firm->access_token }}</a>
    </div>
@endif
<br>
<br>

W linku poniżej znajdziesz: <br>
- Dostęp do wszystkich przetargów w których wasza firma zosatła zaproszona do udziału <br>
- Możliwość dokonania zmiany cen tabelarycznych <br>
    <a href="https://mega1000.pl/firms/przetargi?firmToken={{ $firm->access_token }}">https://mega1000.pl/firms/przetargi?firmToken={{ $firm->access_token }}</a>
<br>
<br>
A więc kliknij i dołącz do przetargu.w
