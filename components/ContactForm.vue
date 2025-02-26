<template>
  <section class="py-10 px-4 md:py-20 bg-gradient-to-r from-emerald-500 to-teal-600">
    <div class="container mx-auto max-w-4xl">
      <h2 class="text-3xl md:text-5xl font-extrabold mb-4 text-center text-white drop-shadow-lg">
        Znajdź <span class="bg-yellow-400 text-emerald-800 px-2 rounded">NAJNIŻSZE CENY</span> styropianu na naszej giełdzie!
      </h2>
      <p class="text-xl text-white text-center mb-8">Pierwsza w Polsce giełda styropianu - porównujemy oferty, Ty oszczędzasz do 30%!</p>
<div class="bg-white rounded-lg shadow-2xl p-6 md:p-10 transform hover:scale-105 transition-all duration-300">
<div class="mb-6 text-center">
  <p class="text-lg font-bold text-red-600">Uwaga: Aktualne oferty ważne jeszcze przez:</p>
  <div id="countdown" class="text-3xl font-bold text-red-600"></div>
</div>

<form @submit.prevent="handleSubmit" class="space-y-4">
  <div v-if="step === 1">
    <label for="zip" class="block text-lg font-semibold mb-2 text-gray-700">Podaj kod pocztowy dostawy:</label>
    <input type="text" id="zip" v-model="form.zip" required class="w-full px-4 py-3 border-2 border-emerald-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition" placeholder="Np. 00-001" />
    <button @click="nextStep" type="button" class="mt-4 w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-opacity-50">
      Sprawdź dostępne oferty
    </button>
  </div>

  <div v-if="step === 2">
    <div class="mb-4">
      <label for="email" class="block text-lg font-semibold mb-2 text-gray-700">Email:</label>
      <input type="email" id="email" v-model="form.email" required class="w-full px-4 py-3 border-2 border-emerald-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition" placeholder="twoj@email.com" />
    </div>
    <div class="mb-4">
      <label for="phone" class="block text-lg font-semibold mb-2 text-gray-700">Telefon:</label>
      <input type="tel" id="phone" v-model="form.phone" required class="w-full px-4 py-3 border-2 border-emerald-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition" placeholder="+48 123 456 789" />
    </div>
    <div>
      <label for="message" class="block text-lg font-semibold mb-2 text-gray-700">Zapytanie:</label>
      <textarea id="message" v-model="form.message" class="w-full px-4 py-3 border-2 border-emerald-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition h-32" placeholder="Opisz swoje potrzeby, a my znajdziemy dla Ciebie najlepsze oferty!"></textarea>
    </div>
    <button type="submit" class="mt-4 w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-opacity-50">
      Otrzymaj najlepsze oferty + DARMOWY poradnik
    </button>
  </div>
</form>

<div class="mt-8 text-center">
  <p class="text-sm text-gray-600">Dołącz do grona <span class="font-bold text-emerald-600">2968 zadowolonych klientów</span>, którzy znaleźli najlepsze oferty na styropian!</p>
  <div class="flex justify-center items-center mt-4">
    <span class="text-yellow-500 text-2xl">★★★★★</span>
    <span class="ml-2 text-gray-700 font-semibold">4.9/5 (512 opinii)</span>
  </div>
</div>

<div class="mt-6 p-4 bg-gray-100 rounded-lg">
  <p class="font-semibold text-gray-700">"Dzięki giełdzie zaoszczędziłem ponad 2000 zł na ociepleniu domu! Polecam każdemu." - Paweł K., Warszawa</p>
</div>
</div>

<div class="mt-10 text-center text-white">
<p class="font-bold text-xl mb-4">Jak działa nasza giełda styropianu?</p>
<p class="text-lg mb-4">Zbieramy oferty od najlepszych dostawców, Ty wybierasz najkorzystniejszą!</p>
<div class="flex justify-center space-x-4">
  <div class="flex gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-patch-check-fill" viewBox="0 0 16 16">
      <path d="M10.067.87a2.89 2.89 0 0 0-4.134 0l-.622.638-.89-.011a2.89 2.89 0 0 0-2.924 2.924l.01.89-.636.622a2.89 2.89 0 0 0 0 4.134l.637.622-.011.89a2.89 2.89 0 0 0 2.924 2.924l.89-.01.622.636a2.89 2.89 0 0 0 4.134 0l.622-.637.89.011a2.89 2.89 0 0 0 2.924-2.924l-.01-.89.636-.622a2.89 2.89 0 0 0 0-4.134l-.637-.622.011-.89a2.89 2.89 0 0 0-2.924-2.924l-.89.01zm.287 5.984-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7 8.793l2.646-2.647a.5.5 0 0 1 .708.708"/>
    </svg>
    <div class="font-bold">
      Bezpieczne transakcje
    </div>
  </div>
  <div class="flex gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-patch-check" viewBox="0 0 16 16">
      <path fill-rule="evenodd" d="M10.354 6.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7 8.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
      <path d="m10.273 2.513-.921-.944.715-.698.622.637.89-.011a2.89 2.89 0 0 1 2.924 2.924l-.01.89.636.622a2.89 2.89 0 0 1 0 4.134l-.637.622.011.89a2.89 2.89 0 0 1-2.924 2.924l-.89-.01-.622.636a2.89 2.89 0 0 1-4.134 0l-.622-.637-.89.011a2.89 2.89 0 0 1-2.924-2.924l.01-.89-.636-.622a2.89 2.89 0 0 1 0-4.134l.637-.622-.011-.89a2.89 2.89 0 0 1 2.924-2.924l.89.01.622-.636a2.89 2.89 0 0 1 4.134 0l-.715.698a1.89 1.89 0 0 0-2.704 0l-.92.944-1.32-.016a1.89 1.89 0 0 0-1.911 1.912l.016 1.318-.944.921a1.89 1.89 0 0 0 0 2.704l.944.92-.016 1.32a1.89 1.89 0 0 0 1.912 1.911l1.318-.016.921.944a1.89 1.89 0 0 0 2.704 0l.92-.944 1.32.016a1.89 1.89 0 0 0 1.911-1.912l-.016-1.318.944-.921a1.89 1.89 0 0 0 0-2.704l-.944-.92.016-1.32a1.89 1.89 0 0 0-1.912-1.911z"/>
    </svg>
    <div class="font-bold">
      Gwarancja jakości
    </div>
  </div>
</div>
</div>

<div class="mt-8 bg-white rounded-lg p-6">
<h3 class="text-2xl font-bold mb-4 text-center text-emerald-700">Często zadawane pytania</h3>
<div class="space-y-4">
  <div>
    <h4 class="font-semibold text-lg mb-2">Czy jesteście sprzedawcą styropianu?</h4>
    <p>Nie, jesteśmy giełdą styropianu. Zbieramy oferty od różnych dostawców, dzięki czemu możesz łatwo porównać ceny i wybrać najlepszą dla siebie i kupić bezpośrednio u producenta.</p>
  </div>
</div>
</div>

<div class="mt-8 text-center">
<img src="/favicon.ico" alt="Giełda styropianu" class="mx-auto h-24 mb-4" />
<p class="text-white text-lg">EPH Polska pierwsza w Polsce giełda styropianu - oszczędzaj mądrze!</p>
</div>
</div>
</section>
</template>
<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useRouter } from 'nuxt/app'
import axios from "axios";

const form = reactive({
  zip: '',
  email: '',
  phone: '',
  message: ''
})

const router = useRouter()
const step = ref(1)

const nextStep = () => {
  step.value = 2;
}

const handleSubmit = async () => {
  const {data: response} = await axios.post('https://admin.mega1000.pl/api/submit-offer-ask-form', form);


  await router.push('/thank-you')
}

onMounted(() => {
  // Countdown timer logic
  const countdown = document.getElementById('countdown')
  let time = 1800 // 30 minutes in seconds
  const timer = setInterval(() => {
    const minutes = Math.floor(time / 60)
    let seconds = time % 60
    seconds = seconds < 10 ? '0' + seconds : seconds
    countdown.innerHTML = `${minutes}:${seconds}`
    time--
    if (time < 0) {
      clearInterval(timer)
      countdown.innerHTML = "Oferty zaktualizowane!"
    }
  }, 1000)
})
</script>

