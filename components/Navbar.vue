<script setup>
import { getToken, removeCookie } from "~~/helpers/authenticator";
import Cart from "~~/utils/Cart";
import Cookies from "universal-cookie";
import { ref, onMounted, onBeforeUnmount, computed } from 'vue';
import ouibounce from 'ouibounce';
import Popup from '~/components/Popup.vue';

const { $shopApi: shopApi } = useNuxtApp();
const productsCart = useProductsCart();
const router = useRouter();
const route = useRoute();
const isVisibilityLimited = ref(false);
const userToken = ref('');
const showMenu = ref(false);
const searchQuery = ref('');
const searchResults = ref([]);
const noResultsMessage = ref('');

const navigationLink = ref(null);
const profileLink = ref(null);
const settingsLink = ref(null);

const tutorialActive = ref(false);
const tutorialTitle = ref('');
const tutorialDescription = ref('');
const tutorialHighlightStyle = reactive({});
const tutorialNextButtonText = ref('Next');
const tutorialStep = ref(0);
const popupRef = ref(null);

const showShopLink = computed(() => {
  return route.path !== '/';
});

useSeoMeta({
  title: 'EPH Polska - Hurtownia Styropianu, Systemy Elewacyjne, Ocieplenia | Gwarancja Najniższej Ceny',
  ogTitle: 'EPH Polska - Hurtownia Styropianu, Systemy Elewacyjne, Ocieplenia | Gwarancja Najniższej Ceny',
  description: 'EPH Polska - hurtownia styropianu oferująca systemy ociepleniowe i elewacyjne z gwarancją najniższej ceny. Sprawdź naszą ofertę już dziś!',
})

onMounted(async () => {
  // showTutorial();
  const cookies = new Cookies();
  userToken.value = cookies.get("token");

  isVisibilityLimited.value = localStorage.getItem('noAllegroVisibilityLimit') != 'true';

  const cart = new Cart();
  cart.init();
  productsCart.value = cart;

  window.addEventListener('token-refreshed', checkUserLoggedIn);
});

onBeforeUnmount(() => {
  window.removeEventListener('token-refreshed', checkUserLoggedIn);
});

const checkUserLoggedIn = () => {
  const cookies = new Cookies();
  userToken.value = cookies.get("token");

  if (userToken.value) {
    console.log("User logged in, token refreshed.");
  } else {
    console.log("User not logged in or session expired.");
  }
}

const buildCustomLink = (pageId) => `/custom/${pageId}`;

const logOut = () => {
  removeCookie();
  userToken.value = getToken();

  window.dispatchEvent(new CustomEvent('token-refreshed'));
};

const toggleMenu = () => {
  showMenu.value = !showMenu.value;
}

const searchProduct = async () => {
  const response = await shopApi.get(`/api/searchProduct/${searchQuery.value}`);
  searchResults.value = response.data;
  if (searchResults.value.length === 0) {
    noResultsMessage.value = 'Brak wyników dla podanej frazy. Spróbuj użyć innych słów kluczowych.';

    setTimeout(() => {
      noResultsMessage.value = '';
    }, 3000)
  } else {
    noResultsMessage.value = '';
  }
}

const showTutorial = () => {
  if (localStorage.getItem('navbarTutorialEnded')) {
    return;
  }

  tutorialActive.value = true;

  switch (tutorialStep.value) {
    case 0:
      tutorialTitle.value = 'Witamy w EPH Polska!';
      tutorialDescription.value = 'W tym tutorialu pokażemy ci kilka funkcji naszego portalu.';
      tutorialNextButtonText.value = 'Chce dowiedzieć się jak działacie!';
      break;
    case 1:
      tutorialTitle.value = 'Wyszukiwanie';
      tutorialDescription.value = 'Jeśli poszukujesz konktetnego produktu wpisz to tutaj a my pokażemy ci wyniki.';
      const navigationLinkRect = navigationLink.value.getBoundingClientRect();
      tutorialHighlightStyle.top = navigationLinkRect.top + window.pageYOffset + 'px';
      tutorialHighlightStyle.left = navigationLinkRect.left + window.pageXOffset + 'px';
      tutorialHighlightStyle.width = navigationLinkRect.width + 'px';
      tutorialHighlightStyle.height = navigationLinkRect.height + 'px';
      tutorialNextButtonText.value = 'Następny krok';
      break;
    case 2:
      tutorialTitle.value = 'Koszyk';
      tutorialDescription.value = 'W tym miejscu możesz wejść do koszyka i wysłać zapytanie ofertowe.';
      const profileLinkRect = profileLink.value.getBoundingClientRect();
      tutorialHighlightStyle.top = profileLinkRect.top + window.pageYOffset + 'px';
      tutorialHighlightStyle.left = profileLinkRect.left + window.pageXOffset + 'px';
      tutorialHighlightStyle.width = profileLinkRect.width + 'px';
      tutorialHighlightStyle.height = profileLinkRect.height + 'px';
      break;
    case 3:
      tutorialTitle.value = 'Sklep';
      tutorialDescription.value = 'Jeśli chcesz wrócić do sklepu i dodać inne produkty wejdź tutaj!';
      const settingsLinkRect = settingsLink.value.getBoundingClientRect();
      tutorialHighlightStyle.top = settingsLinkRect.top + window.pageYOffset + 'px';
      tutorialHighlightStyle.left = settingsLinkRect.left + window.pageXOffset + 'px';
      tutorialHighlightStyle.width = settingsLinkRect.width + 'px';
      tutorialNextButtonText.value = 'Wszystko jasne przechodzę do strony.';
      break;
    case 4:
      localStorage.setItem('navbarTutorialEnded', true);
      window.dispatchEvent(new Event('navbar-tutorial-ended'))
      tutorialActive.value = false;
  }
};

const nextTutorialStep = () => {
  if (tutorialStep.value === 4) {
    tutorialActive.value = false;
  } else {
    tutorialStep.value++;
    // showTutorial();
  }
};

const endTutorial = () => {
  tutorialActive.value = false;
  localStorage.setItem('navbarTutorialEnded', true);
}
</script>

<template>
  <Popup />
  <nav class="bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-20">
        <!-- Logo -->
        <div class="flex items-center">
          <NuxtLink href="/" class="flex-shrink-0">
            <img src="/logo.webp" alt="EPH Polska" class="w-12 h-12 object-contain">
          </NuxtLink>
        </div>

        <!-- Desktop Navigation Links -->
        <div class="hidden md:flex items-center space-x-8">
          <div ref="settingsLink" v-if="showShopLink">
            <NuxtLink v-if="!isVisibilityLimited" href="/" class="nav-link">Sklep</NuxtLink>
          </div>
          <NuxtLink v-if="userToken && !isVisibilityLimited" href="/account" class="nav-link">Konto</NuxtLink>
          <NuxtLink href="/przetargi-styropianow" class="nav-link">Stwórz przetarg</NuxtLink>
          <NuxtLink v-if="userToken && !isVisibilityLimited" href="/" @click.prevent="logOut" class="nav-link">Wyloguj</NuxtLink>
          <NuxtLink v-else href="/login" class="nav-link">Zaloguj</NuxtLink>
          <NuxtLink href="/faq" class="nav-link">FAQ</NuxtLink>
          <NuxtLink href="/regulamin" class="nav-link">Regulamin</NuxtLink>
          <a href="tel:691 801 594" class="nav-link font-bold text-red-600">Infolinia 7/24 691 801 594</a>
        </div>

        <!-- Search Bar and Cart -->
        <div class="flex items-center space-x-6">
          <div class="relative" ref="navigationLink">
            <input type="search" v-model="searchQuery" @input="searchProduct()" class="search-input" placeholder="Wyszukaj produkt" />
            <Icon name="heroicons:magnifying-glass" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
          </div>

          <div ref="profileLink">
            <NuxtLink href="/koszyk.html" class="relative inline-flex items-center">
              <Icon name="heroicons:shopping-cart" size="28" class="text-gray-600 hover:text-cyan-500 transition-colors duration-200" />
              <span v-if="productsCart.products.length > 0" class="cart-count">
                {{ productsCart.products.length > 9 ? '9+' : productsCart.products.length }}
              </span>
            </NuxtLink>
          </div>
        </div>

        <!-- Mobile Menu Toggle -->
        <div class="md:hidden">
          <button @click="toggleMenu" class="mobile-menu-toggle">
            <Icon :name="showMenu ? 'heroicons:x-mark' : 'heroicons:bars-3'" size="28" class="text-gray-600" />
          </button>
        </div>
      </div>

      <!-- Mobile Menu -->
      <transition name="slide-fade">
        <div v-if="showMenu" class="md:hidden mt-2 pb-3 space-y-1">
          <NuxtLink v-if="!isVisibilityLimited && showShopLink" href="/" class="mobile-nav-link">Sklep</NuxtLink>
          <NuxtLink v-if="userToken && !isVisibilityLimited" href="/account" class="mobile-nav-link">Konto</NuxtLink>
          <NuxtLink href="/Complaint" class="mobile-nav-link">Zgłoś reklamację</NuxtLink>
          <NuxtLink v-if="userToken && !isVisibilityLimited" href="/" @click.prevent="logOut" class="mobile-nav-link">Wyloguj</NuxtLink>
          <NuxtLink v-else href="/login" class="mobile-nav-link">Zaloguj</NuxtLink>
          <NuxtLink href="/faq" class="mobile-nav-link">FAQ</NuxtLink>
          <NuxtLink href="/regulamin" class="mobile-nav-link">Regulamin</NuxtLink>
          <a href="tel:691 801 594" class="mobile-nav-link font-bold text-red-600">Infolinia 7/24 691 801 594</a>
        </div>
      </transition>
    </div>

    <!-- Search Results Modal -->
    <transition name="fade">
      <div v-if="searchResults.length > 0" class="search-results-modal" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div class="absolute inset-0 overflow-hidden">
          <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
          <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
            <div class="relative w-screen max-w-md">
              <div class="h-full flex flex-col py-6 bg-white shadow-xl overflow-y-scroll">
                <div class="px-4 sm:px-6">
                  <div class="flex items-start justify-between">
                    <h2 class="text-lg font-medium text-gray-900" id="slide-over-title">Wyniki wyszukiwania</h2>
                    <button @click="searchResults = []" class="close-button">
                      <Icon name="heroicons:x-mark" size="24" class="text-gray-400 hover:text-gray-500" />
                    </button>
                  </div>
                  <input type="search" class="search-input mt-4" v-model="searchQuery" @input="searchProduct()" placeholder="Wyszukaj produkt" />
                </div>

                <div class="mt-6 relative flex-1 px-4 sm:px-6">
                  <ul class="space-y-4">
                    <li v-for="result in searchResults" :key="result.id" class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                      <NuxtLink class="flex items-center p-4" :href="`/singleProduct/${result.id}`" @click="searchResults = []">
                        <img :src="`https://admin.mega1000.pl${result.url_for_website}`" class="h-16 w-16 object-cover rounded-md mr-4" />
                        <div>
                          <p class="text-sm font-medium text-gray-900">{{ result.name }}</p>
                          <p class="text-xs text-gray-500">{{ result.symbol }}</p>
                          <p class="text-sm font-semibold text-cyan-600 mt-1">{{ result.price.gross_price_of_packing }} PLN</p>
                          <div class="flex items-center mt-1">
                            <div class="flex">
                              <Icon v-for="i in 5" :key="i" :name="i <= Math.round(result.meanOpinion ?? 0) ? 'heroicons:star-solid' : 'heroicons:star'" class="w-4 h-4" :class="i <= Math.round(result.meanOpinion ?? 0) ? 'text-yellow-400' : 'text-gray-300'" />
                            </div>
                            <p class="ml-2 text-xs text-gray-600">{{ (result.meanOpinion ?? 0).toFixed(1) }} / 5</p>
                          </div>
                        </div>
                      </NuxtLink>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!-- No Results Message -->
    <transition name="fade">
      <div v-if="noResultsMessage" class="fixed inset-x-0 top-20 flex justify-center">
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-md">
          <p>{{ noResultsMessage }}</p>
        </div>
      </div>
    </transition>

    <div class="ml-4 flex-shrink-0">
      <NavbarShipmentCostTable />
    </div>
  </nav>
  <SocialProof />
  <!-- Tutorial Overlay -->
  <transition name="fade">
    <div v-if="tutorialActive" class="tutorial-overlay">
      <div class="tutorial-modal-container">
        <div class="tutorial-highlight" :style="tutorialHighlightStyle"></div>
        <div class="tutorial-modal">
          <h3 class="text-xl font-bold mb-2">{{ tutorialTitle }}</h3>
          <p class="mb-4">{{ tutorialDescription }}</p>
          <div class="flex justify-between">
            <button @click="endTutorial" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors duration-200">Pomiń</button>
            <button @click="nextTutorialStep" class="px-4 py-2 bg-cyan-500 text-white rounded hover:bg-cyan-600 transition-colors duration-200">{{ tutorialNextButtonText }}</button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<style scoped>
/* Navigation Links */
.nav-link {
  @apply inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-600 hover:text-gray-900 hover:border-cyan-500 focus:outline-none focus:text-gray-900 focus:border-cyan-500 transition duration-150 ease-in-out;
}

/* Search Input */
.search-input {
  @apply block w-full pl-4 pr-10 py-2 border border-gray-300 rounded-full text-sm focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition duration-150 ease-in-out;
}

/* Cart Count */
.cart-count {
  @apply absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center;
}

/* Mobile Menu Toggle */
.mobile-menu-toggle {
  @apply inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-cyan-500;
}

/* Mobile Navigation Links */
.mobile-nav-link {
  @apply block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 focus:outline-none focus:text-gray-900 focus:bg-gray-50 transition duration-150 ease-in-out;
}

/* Search Results Modal */
.search-results-modal {
  @apply fixed inset-0 overflow-hidden z-50;
}

/* Close Button */
.close-button {
  @apply bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500;
}

/* Tutorial Styles */
.tutorial-overlay {
  @apply fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50;
}

.tutorial-modal-container {
  @apply relative;
}

.tutorial-highlight {
  @apply absolute bg-yellow-200 bg-opacity-20 rounded-md;
  animation: pulse 2s infinite;
}

.tutorial-modal {
  @apply bg-white p-6 rounded-lg shadow-xl max-w-md mx-auto z-10;
}

@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(250, 204, 21, 0.4);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(250, 204, 21, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(250, 204, 21, 0);
  }
}

/* Transitions */
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.3s ease;
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateY(-20px);
  opacity: 0;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
