<template>
  <section class="py-10 px-4 md:py-20 bg-gradient-to-r from-emerald-500 to-teal-600">
    <div class="container mx-auto max-w-4xl text-center">
      <h2 class="text-3xl md:text-5xl font-extrabold mb-6 text-white drop-shadow-lg">
        Dziękujemy za skorzystanie z naszej giełdy styropianu!
      </h2>
      <div class="bg-white rounded-lg shadow-2xl p-6 md:p-10">
        <img src="/favicon.ico" alt="Sukces" class="w-24 h-24 mx-auto mb-6" />

        <p class="text-xl mb-4">Twoje zapytanie zostało przyjęte. Wkrótce otrzymasz najlepsze oferty!</p>

        <div class="mb-6">
          <p class="font-bold text-lg">Co dalej?</p>
          <ul class="list-disc list-inside text-left">
            <li>Sprawdź swoją skrzynkę e-mail w ciągu 24 godzin</li>
            <li>Przejrzyj otrzymane oferty od najlepszych dostawców</li>
            <li>Wybierz najkorzystniejszą dla siebie propozycję</li>
          </ul>
        </div>

        <div class="bg-emerald-100 border-l-4 border-emerald-500 p-4 mb-6">
          <p class="font-bold">Bonus: Twój darmowy poradnik</p>
          <p>Kliknij poniższy przycisk, aby pobrać "10 sposobów na efektywne ocieplenie domu"</p>
          <a href="/poradnik.pdf" class="inline-block mt-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2 px-4 rounded">
            Pobierz poradnik
          </a>
        </div>

        <a href="/" class="text-emerald-600 hover:text-emerald-700 font-bold">Powrót na stronę główną</a>
      </div>

      <div class="mt-10 text-white">
        <p class="font-bold text-xl mb-4">Polecaj i zarabiaj!</p>
        <p class="mb-2">Poleć nas znajomym i otrzymaj 30 zł za każde zrealizowane zamówienie</p>
        <a class="bg-yellow-400 hover:bg-yellow-500 text-emerald-800 font-bold py-2 px-4 rounded" href="/polec-znajomego">
          Program poleceń
        </a>
      </div>
    </div>
  </section>
</template>
