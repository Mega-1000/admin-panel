<body>
<p>W zwiazku z wydaniem towaru do panstwa ze zlecenia nr {{ $orderId }} informujemy iz bedziemy wystawiac w najblizszym czasie fakture i wysylac ja panstwu na e maila.</p>
<p>Aby otrzymac fakture prosimy wejsc w ponizszy link i potwierdzic poprawnosc danych i zgodnoac otrzymanego towaru wzgledem zamowienia.</p>
<p>Jezeli na ten moment nie otrzymaliscie jeszcze towaru to prosimy sie wstrzymac z wypelnianiem  tego formularza do momentu otrzymania towaru i sprawdzenia poprawnosci dostawy.</p>
<p>W przypadku braku zatwierdzenia ponizszych danych informujemy iz system codziennie do 10 dnia nastepnego miesiaca od zakupu towaru bedzie wysylal taki e mail po czym automatycznie po tej dacie wystawi fakture na dane osoby odbierajacej towar i przyjmie jego zgodnosc z oferta.</p>

<br/>
<a href="https://{{env('APP_URL')}}/customer/{{ $orderId }}/confirmation">POTWIERDZ DANE DO FAKTURY</a>
</body>
