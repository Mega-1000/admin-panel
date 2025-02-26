<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import MagasinesMap from '~/components/MagasinesMap.vue';
import { loadFull } from "tsparticles";
import Swal from 'sweetalert2'

const showZipCodeModal = !localStorage.getItem('zipCode');
const { $shopApi: shopApi } = useNuxtApp();
const description = ref('');
const isLoading = ref(false);
const iframeSrc = 'https://admin.mega1000.pl/auctions/display-prices-table?zip-code=' + localStorage.getItem('zipCode');
const tutorialVideo = ref(null);
const productCarousel = ref(null)
let carouselInterval = null
const show = ref(false);
const phoneNumber = ref('');
const loading = ref(false);
const testimonials = [
  {
    name: "Anna Kowalska",
    location: "Warszawa",
    quote: "Dzięki przetargowi na Mega1000 zaoszczędziłam ponad 20% na styropianie do mojego domu. Polecam!",
    rating: 5,
    avatar: "/11.jpg"
  },
  {
    name: "Piotr Nowak",
    location: "Kraków",
    quote: "Proces był prosty i szybki. Otrzymałem wiele konkurencyjnych ofert w ciągu kilku godzin.",
    rating: 4.5,
    avatar: "/33.jpeg"
  },
  {
    name: "Marta Wiśniewska",
    location: "Wrocław",
    quote: "Świetna obsługa klienta i najlepsze ceny na rynku. Na pewno skorzystam ponownie!",
    rating: 5,
    avatar: "/22.jpeg"
  }
];

onMounted(async () => {
  if (typeof window !== 'undefined' && window.gtag) {
    window.gtag('event', 'page_view', {
      page_path: window.location.pathname,
      page_title: 'Important Page',
      event_category: 'Important',
      event_label: 'User entered an important page',
    });
  }

  const data = await shopApi.get('https://admin.mega1000.pl/api/categories/details/search?category=102');
  description.value = data.data.description;

  window.addEventListener('message', handleIframeMessage);
  window.addEventListener('navbar-tutorial-ended', playTutorialVideo);

  if (productCarousel.value && window.innerWidth < 768) {
    let switchCount = 0;
    const maxSwitches = 2;
    const scrollDistance = 300;

    carouselInterval = setInterval(() => {
      if (switchCount < maxSwitches) {
        productCarousel.value.scrollBy({
          left: scrollDistance,
          behavior: 'smooth',
        });
        switchCount++;
      } else {
        productCarousel.value.scrollTo({
          left: 0,
          behavior: 'smooth',
        });
        switchCount = 0;
      }
    }, 6000);
  }
});

onUnmounted(() => {
  window.removeEventListener('message', handleIframeMessage);
  window.removeEventListener('navbar-tutorial-ended', playTutorialVideo);
  if (carouselInterval) {
    clearInterval(carouselInterval);
  }
});

const handleIframeMessage = (event) => {
  if (event.data && event.data.url) {
    window.location.href = event.data.url;
  }
};

const submitForm = async () => {
  loading.value = true;
  if (phoneNumber.value) {
    await shopApi.post('api/contact-approach/create', {
      phone_number: phoneNumber.value,
      referred_by_user_id: 57352,
    });
  }

  localStorage.setItem('formSubmitted', 'true');
  loading.value = false;

  Swal.fire('Pomyślnie wysłano formularz! Konsultant który został ci przydzielony to: 00137', '', 'success');
}

const onIframeLoad = () => {
  isLoading.value = false;
};

const playTutorialVideo = () => {
  if (tutorialVideo.value) {
    tutorialVideo.value.contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', '*');
  }
};

const onIframeError = () => {
  isLoading.value = false;
  alert('Failed to load the iframe content.');
};

const products = [
  {
    id: 99546,
    name: 'Neotherm fasada 033',
    gross_selling_price_calculated_unit: 190,
    url_for_website: '/storage/products/neotherm_fasada_033_1.jpg',
    purchases: 5
  },
  {
    id: 112915,
    name: 'Justyr fasada 038',
    gross_selling_price_calculated_unit: 186,
    url_for_website: '/images/products/1My1BsmA51',
    purchases: 7
  },
  {
    id: 109074,
    name: 'Neotherm EPS 100 036',
    gross_selling_price_calculated_unit: 231.24,
    url_for_website: '/storage/products/neotherm_fasada_033_1.jpg',
    purchases: 3
  },
  {
    id: 109074,
    name: 'Izoterm fasada 045',
    gross_selling_price_calculated_unit: 156.25,
    url_for_website: '/izoterm_fasada_045_1.jpg',
    purchases: 2
  },
]

const playVideo  = () => {
  const video = document.getElementById('tutorialVideo');
  const src = video.src;
  video.src = src.includes('?') ? `${src}&autoplay=1` : `${src}?autoplay=1`;
}
</script>
<template>
  <div v-if="loading" style="z-index: 1000" class="fixed top-0 left-0 w-screen h-screen flex justify-center items-center bg-gray-500 bg-opacity-50">
    <Loader :showLoader="loading" />
  </div>

  <div class="font-sans">
    <AskUserForZipCodeStyrofoarms v-if="showZipCodeModal" />
    <main>
      <!-- Hero Section -->
      <section class="relative py-10 overflow-hidden bg-gradient-to-br from-emerald-600 to-emerald-900">
        <div class="absolute inset-0">
          <svg class="absolute bottom-0 left-0 w-full" viewBox="0 0 1440 320" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-opacity="0.1" fill="#fff" d="M0,192L48,197.3C96,203,192,213,288,229.3C384,245,480,267,576,250.7C672,235,768,181,864,181.3C960,181,1056,235,1152,234.7C1248,235,1344,181,1392,154.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
          </svg>
        </div>

        <div class="container mx-auto px-4 relative z-10">
          <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6 animate-fade-in-down didact-gothic-regular">
              Przez ostatnie 12 miesięcy <span class="text-yellow-300 animate-pulse"><pm>2968</pm></span> użytkowników stworzyło <em class="not-italic bg-emerald-700 px-2 py-1 rounded animate-bounce">przetarg</em> i oszczędziło na styropianie!
            </h1>
            <p class="text-xl md:text-2xl text-emerald-100 mb-10 animate-fade-in-up animation-delay-300">
              Dołącz do zadowolonych klientów i skorzystaj z <em class="not-italic font-bold underline decoration-yellow-300 decoration-2 underline-offset-2">DARMOWEJ DOSTAWY</em> na wszystkie zamówienia!
            </p>
            <NuxtLink
                href="/przetargi-styropianow"
                class="inline-block bg-white text-emerald-800 font-bold py-4 px-8 rounded-full transition-all duration-300 hover:bg-yellow-300 hover:text-emerald-900 transform hover:scale-105 shadow-lg hover:shadow-xl animate-pulse"
            >
              <span class="flex items-center gap-2">
                Stwórz przetarg teraz!
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </span>
            </NuxtLink>
            <p class="text-sm md:text-base mt-6 text-emerald-200 animate-fade-in-up animation-delay-600">
              Już ponad 5000 klientów zaoszczędziło dzięki naszym przetargom!
            </p>
          </div>
          <OpinionStars class="animate-fade-in-up animation-delay-900 mt-8 text-white" />
        </div>
      </section>


      <!-- Popular Products Section -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 bg-gradient-to-b from-gray-50 to-white">
        <!-- <h2 class="text-4xl md:text-6xl font-bold text-gray-900 mb-16 text-center leading-tight">
          Nie chcesz robić przegargu? Zobacz <span class="text-emerald-600">najczęściej</span> kupowane produkty 🔥
        </h2>

        <div class="relative">
          <div class="flex overflow-x-auto snap-x snap-mandatory scrollbar-hide space-x-8 pb-12" ref="productCarousel">
            <div v-for="product in products" :key="product.id" class="snap-start flex-shrink-0 w-72 md:w-80">
              <a :href="`/singleProduct/${product.id}`" class="group block bg-white rounded-2xl shadow-xl hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300">
                <div class="relative overflow-hidden rounded-t-2xl">
                  <img :src="`https://admin.mega1000.pl${product.url_for_website}`" :alt="product.name" class="w-full h-56 object-cover transition-transform duration-300 group-hover:scale-110" />
                  <div class="absolute top-0 right-0 bg-red-500 text-white text-sm font-bold px-4 py-2 m-4 rounded-full shadow-lg">
                    HOT
                  </div>
                </div>
                <div class="p-6">
                  <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-emerald-600 transition-colors">{{ product.name }}</h3>
                  <p class="text-emerald-600 font-extrabold text-3xl mb-4">
                    {{ product.gross_selling_price_calculated_unit }} <span class="text-lg font-semibold">PLN/M<sup>3</sup></span>
                  </p>
                  <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500">{{ product.purchases }} zamówień dzisiaj!</span>
                    <svg class="w-6 h-6 text-emerald-500 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                  </div>
                </div>
              </a>
            </div>
          </div>
          <button class="absolute top-1/2 -left-6 -translate-y-1/2 bg-white rounded-full p-3 shadow-lg hover:bg-gray-100 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500" @click="scrollCarousel('left')">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
          </button>
          <button class="absolute top-1/2 -right-6 -translate-y-1/2 bg-white rounded-full p-3 shadow-lg hover:bg-gray-100 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500" @click="scrollCarousel('right')">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          </button>
        </div> -->

        <div class=" text-center">
          <a href="#price-table" class="inline-flex items-center px-10 py-5 text-lg font-bold rounded-full text-white bg-emerald-600 hover:bg-emerald-700 transition-all duration-300 transform hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
            Zobacz tabelę cen
            <svg class="ml-2 -mr-1 w-6 h-6 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </a>
        </div>
      </div>

      <div class="hero bg-gradient-to-r from-emerald-600 to-emerald-700 text-white py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
          <h1 class="text-3xl sm:text-4xl font-bold mb-4 text-center">
            Płatność przy odbiorze! Bez ryzyka!
          </h1>
          <p class="text-lg sm:text-xl mb-8 text-center">
            Zamówienia realizowane są również z opcją płatności przy odbiorze, lub bezpośrednio od fabryki.
            <span class="font-semibold">Nie musisz obawiać się o swoje pieniądze!</span>
          </p>
          <div class="text-center">
            <a href="/przetargi-styropianow" class="inline-block bg-yellow-400 text-emerald-800 font-bold py-3 px-6 rounded-lg transition-all duration-300 hover:bg-yellow-300 transform hover:scale-105 shadow-md hover:shadow-lg">
                    <span class="flex items-center justify-center gap-2">
                        Stwórz przetarg i kup od producenta!
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </span>
            </a>
          </div>
        </div>
      </div>

      <section class="bg-white w-full overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
          <div class="relative bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-500 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] overflow-hidden">
            <!-- Background pattern -->
            <div class="absolute inset-0 opacity-10">
              <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                  <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                    <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                  </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#grid)" />
              </svg>
            </div>

            <div class="relative z-10 px-6 py-12 sm:px-12 sm:py-16 lg:py-20 lg:px-20 text-center">
              <h2 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-6">
                Potrzebujesz pomocy?
              </h2>
              <p class="text-xl sm:text-2xl text-white font-medium mb-10">
                Zostaw numer - oddzwonimy w ciągu <span class="font-bold underline decoration-yellow-400 decoration-4">5 minut</span> z darmową konsultacją!
              </p>

              <form class="max-w-2xl mx-auto mb-12" @submit.prevent="submitForm">
                <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                  <div class="w-full sm:w-2/3">
                    <input type="tel" placeholder="Twój numer telefonu" v-model="phoneNumber" required
                           class="w-full px-6 py-4 text-lg text-gray-900 placeholder-gray-500 bg-white rounded-full focus:outline-none focus:ring-4 focus:ring-yellow-400 transition duration-300 ease-in-out"
                    />
                  </div>
                  <button type="submit"
                          class="w-full sm:w-auto px-8 py-4 bg-yellow-400 text-emerald-800 text-xl font-bold rounded-full hover:bg-yellow-300 transition duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-yellow-500 focus:ring-opacity-50 shadow-lg">
                    Zamów kontakt
                  </button>
                </div>
              </form>

              <div class="flex flex-col sm:flex-row justify-center items-center space-y-6 sm:space-y-0 sm:space-x-12">
                <div class="flex items-center">
                  <svg class="h-10 w-10 text-yellow-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                  <span class="text-2xl font-bold text-white">+48 691 801 594</span>
                </div>
                <div class="text-center sm:text-left">
                  <p class="text-lg text-yellow-200">Godziny pracy:</p>
                  <p class="text-2xl font-bold text-white">6:30 - 22:00</p>
                </div>
              </div>
            </div>

            <!-- Bottom curve -->
<!--            <div class="absolute bottom-0 inset-x-0">-->
<!--              <svg viewBox="0 0 224 12" fill="white" class="w-full -mb-1 text-white" preserveAspectRatio="none">-->
<!--                <path d="M0,0 C48.8902582,6.27314026 86.2235915,9.40971039 112,9.40971039 C137.776408,9.40971039 175.109742,6.27314026 224,0 L224,12.0441132 L0,12.0441132 L0,0 Z"></path>-->
<!--              </svg>-->
<!--            </div>-->
          </div>
        </div>
      </section>

      <!-- Price Table Section -->
      <section class="py-8 md:px-4 bg-gradient-to-b from-emerald-100 to-white" id="price-table">
        <div class="container mx-auto relative max-w-6xl">
          <h2 class="text-4xl md:text-5xl font-extrabold mb-12 text-center text-emerald-700 animate-fade-in-up">
            Wybierz styropian z tabeli, kliknij cenę - dodasz do koszyka
          </h2>
          <div class="bg-white rounded-3xl shadow-2xl p-4 md:p-8 md:p-12 animate-fade-in-up animation-delay-300 transform hover:scale-105 transition-transform duration-500">
            <p class="text-xl md:text-2xl mb-10 text-gray-700 leading-relaxed">
              Oprócz znalezienia najtańszej hurtowni w Polsce która dostarczy ci ten styropian wraz z gratisowym transportem dokonamy także przetargu dla wszystkich pozostałych 50 producentów dla porównania.
            </p>
            <NuxtLink
                to="/przetargi-styropianow"
                class="create-auction-btn block w-full md:w-fit mx-auto my-10 bg-emerald-600 text-white font-bold py-5 px-10 rounded-full transition-all duration-300 hover:bg-emerald-700 hover:scale-105 shadow-lg hover:shadow-xl text-center text-lg"
            >
              <span class="flex gap-3 items-center justify-center">
                <span>Stwórz przetarg - do niczego nie zobowiązuje!</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </span>
            </NuxtLink>
            <div v-if="isLoading" class="flex justify-center items-center h-40 mt-10">
              <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-emerald-600"></div>
            </div>
            <iframe
                ref="priceTable"
                title="Tabelka cen styropianów"
                :src="iframeSrc"
                loading="lazy"
                :class="['w-full border-2 border-gray-200 rounded-lg shadow-lg transition-all duration-500', isLoading ? 'h-0' : 'h-[600px]']"
                sandbox="allow-scripts allow-same-origin"
                @load="onIframeLoad"
                @error="onIframeError"
            ></iframe>
            <div class="mt text-center mt-8">
              <span class="text-gray-700">Nie wiesz jak korzystać z tabeli cen?</span>
              <NuxtLink class="text-emerald-600 hover:text-emerald-700 ml-2 font-semibold underline" to="/tabela-cen-instrukcje">Kliknij tutaj</NuxtLink>
            </div>
          </div>
        </div>
      </section>

      <section class="px-4 animate-fade-in-up bg-gray-50">
        <div class="mx-auto max-w-screen-xl">
          <LogosSection />
        </div>
      </section>

      <FastShipping />

      <!-- Testimonials Section -->
      <section class="py-12 px-4 bg-emerald-50 animate-fade-in-up">
        <div class="container mx-auto">
          <h2 class="text-4xl md:text-5xl font-bold mb-16 text-center text-emerald-800">
            Co mówią nasi klienci
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
            <div v-for="(testimonial, index) in testimonials" :key="index"
                 class="bg-white rounded-lg shadow-lg p-8 transition-all duration-300 hover:shadow-xl hover:-translate-y-2">
              <div class="flex items-center mb-6">
                <img :src="testimonial.avatar" :alt="testimonial.name" class="w-16 h-16 rounded-full mr-4 border-4 border-emerald-300">
                <div>
                  <h3 class="font-bold text-xl text-emerald-700">{{ testimonial.name }}</h3>
                  <p class="text-emerald-6f00">{{ testimonial.location }}</p>
                </div>
              </div>
              <p class="text-gray-700 italic mb-6 text-lg">"{{ testimonial.quote }}"</p>
              <div class="flex items-center">
                <span v-for="i in 5" :key="i" class="text-yellow-400 mr-1">
                  <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                  </svg>
                </span>
                <span class="font-bold text-emerald-700 ml-2">{{ testimonial.rating }}/5</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <StyroTree />

      <!-- Pickup Points Section -->
      <section class="py-10 px-4 bg-gray-100 animate-fade-in-up">
        <div class="container mx-auto text-center">
          <h2 class="text-3xl md:text-5xl font-bold mb-8 text-emerald-800">
            Odbiór w jednym z <em class="text-emerald-600">100</em> punktów
          </h2>
          <h4 class="text-emerald-500 font-bold text-lg md:text-xl mb-4">
            Kliknij na punkt aby sprawdzić dostępne w nim produkty
          </h4>
          <p class="text-gray-700 mb-12 text-lg">
            Ponad 10,000 zadowolonych klientów skorzystało z naszej sieci punktów odbioru!
          </p>
          <MagasinesMap class="shadow-2xl rounded-lg overflow-hidden" />
        </div>
      </section>

      <!-- Referral Section -->
      <section class="relative py-12 px-4 bg-gradient-to-br from-emerald-600 to-emerald-800 text-white overflow-hidden">
        <div class="absolute inset-0 bg-pattern opacity-10"></div>
        <div class="container mx-auto text-center relative z-10">
          <h2 class="text-5xl md:text-7xl font-extrabold mb-8 tracking-tight animate-fade-in-up">
            Polecaj i <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-yellow-500">oszczędzaj!</span>
          </h2>
          <p class="text-xl md:text-2xl mb-12 max-w-3xl mx-auto leading-relaxed animate-fade-in-up animation-delay-200">
            Zaproś znajomych, a otrzymasz <span class="font-bold text-yellow-300">30 zł zniżki</span> za każdego nowego użytkownika! Proste i korzystne.
          </p>
          <div class="mb-16 animate-fade-in-up animation-delay-400">
      <span class="inline-block bg-emerald-700 rounded-full px-6 py-3 text-lg font-semibold">
        Już ponad <span class="text-yellow-300 font-bold animate-pulse">5000+</span> zadowolonych klientów!
      </span>
          </div>
          <a href="https://mega1000.pl/polec-znajomego" class="group relative inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-emerald-800 bg-white rounded-full overflow-hidden transition-all duration-300 ease-out hover:scale-105 hover:shadow-xl">
            <span class="absolute inset-0 w-full h-full bg-gradient-to-br from-yellow-300 to-yellow-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300 ease-out"></span>
            <span class="relative z-10 flex items-center">
        Sprawdź swój panel poleceń
        <svg class="w-5 h-5 ml-2 transition-transform duration-300 ease-out group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
      </span>
          </a>
        </div>
        <div class="absolute bottom-0 left-0 w-full h-24 bg-gradient-to-t from-emerald-800 to-transparent"></div>
      </section>

      <section class="py-10 px-4 text-black overflow-hidden relative animate-fade-in-up">
        <div class="container mx-auto relative z-10">
          <h2 class="text-4xl md:text-6xl font-bold mb-16 text-center">
            Gwarancja najniższej ceny
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            <div class="bg-white bg-opacity-20 backdrop-filter backdrop-blur-lg rounded-lg p-8 shadow-lg transform hover:scale-105 transition-transform duration-300">
              <div class="text-6xl mb-6">💰</div>
              <h3 class="text-2xl font-semibold mb-4">Oszczędzasz pieniądze</h3>
              <p class="text-lg">Gwarantujemy, że znajdziesz u nas najniższe ceny na rynku. Jeśli znajdziesz taniej, wyrównamy cenę!</p>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-filter backdrop-blur-lg rounded-lg p-8 shadow-lg transform hover:scale-105 transition-transform duration-300">
              <div class="text-6xl mb-6">🛡️</div>
              <h3 class="text-2xl font-semibold mb-4">Pewność zakupu</h3>
              <p class="text-lg">Nasza gwarancja daje Ci pewność, że dokonujesz najlepszego wyboru cenowego na rynku.</p>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-filter backdrop-blur-lg rounded-lg p-8 shadow-lg transform hover:scale-105 transition-transform duration-300">
              <div class="text-6xl mb-6">🤝</div>
              <h3 class="text-2xl font-semibold mb-4">Uczciwe warunki</h3>
              <p class="text-lg">Bez ukrytych kosztów czy haczyków. Nasza gwarancja jest prosta i przejrzysta.</p>
            </div>
          </div>
          <div class="mt-16 text-center">
            <NuxtLink
                to="/przetargi-styropianow"
                class="bg-white text-emerald-700 font-bold py-4 px-10 rounded-full inline-block transition-all duration-300 hover:bg-yellow-300 hover:text-emerald-800 hover:scale-105 shadow-lg hover:shadow-xl text-xl"
            >
              Jeszcze się zastanawiasz? Stwórz przetarg!
            </NuxtLink>
          </div>
        </div>
        <div class="absolute inset-0 bg-emerald-600 opacity-20 animate-wave"></div>
      </section>
    </main>
  </div>
</template>

<style>
body {
  scroll-behavior: smooth;
}

.animate-fade-in-up {
  animation: fadeInUp 0.8s ease-out forwards;
  opacity: 0;
}

.animation-delay-300 {
  animation-delay: 300ms;
}

.animation-delay-600 {
  animation-delay: 600ms;
}

.animation-delay-900 {
  animation-delay: 900ms;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-wave {
  animation: wave 10s linear infinite;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
  background-size: 200% 100%;
}

@keyframes wave {
  0% { background-position: 100% 0; }
  100% { background-position: -100% 0; }
}

.create-auction-btn {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
  }
  70% {
    box-shadow: 0 0 0 15px rgba(255, 255, 255, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
  }
}

pm {
  background: linear-gradient(-180deg, #f99d9d 15%, #f5d3d3 94%);
  padding: 2px 6px;
  font-style: normal;
  color: #343a40;
  border-radius: 4px;
  overflow: hidden;
  display: inline-block;
  transform: skew(-5deg);
  box-shadow: 2px 2px 4px rgba(0,0,0,0.1);
}

@keyframes gradient-x {
  0%, 100% {
    background-size: 200% 200%;
    background-position: left center;
  }
  50% {
    background-size: 200% 200%;
    background-position: right center;
  }
}

.animate-gradient-x {
  animation: gradient-x 3s ease infinite;
}

@keyframes blob {
  0% {
    transform: translate(0px, 0px) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }
  100% {
    transform: translate(0px, 0px) scale(1);
  }
}

.animate-blob {
  animation: blob 7s infinite;
}

.bg-pattern {
  background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fade-in-up 0.6s ease-out forwards;
}

.animation-delay-200 {
  animation-delay: 0.2s;
}

.animation-delay-400 {
  animation-delay: 0.4s;
}
</style>
