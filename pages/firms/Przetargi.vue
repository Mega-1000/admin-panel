<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import { Clock, TrendingUp, Calendar, Truck, Edit } from 'lucide-vue-next';

const auctions = ref<any>([]);
const { $shopApi: shopApi } = useNuxtApp();
const route = useRoute();
const currentFirm = ref<any>(null);
const haveToFillPrices = ref<boolean>(false);

// Pagination
const currentPage = ref(1);
const itemsPerPage = ref(5); // Adjust as needed
const totalPages = ref(0);
const loading = ref(false);

onMounted(async () => {
  loading.value = true;
  await fetchAuctions();
  loading.value = false;
});

const fetchAuctions = async () => {
  const firmToken = route.query.firmToken as string;
  const { data: response } = await shopApi.get(`/api/get-auctions/${firmToken}?page=${currentPage.value}&perPage=${itemsPerPage.value}`) as any;

  currentFirm.value = response[0];
  auctions.value = response[1].data;
  haveToFillPrices.value = response[2].haveToFillPrices;
  totalPages.value = Math.ceil(response[1].total / itemsPerPage.value);

  auctions.value.forEach((auction: any) => {
    auction.offersExpanded = false;
    auction.activeOffers = auction.offers.slice(0, 3);
  });
};

const toggleOffers = (auction: any) => {
  auction.offersExpanded = !auction.offersExpanded;
  auction.activeOffers = auction.offersExpanded ? auction.offers : auction.offers.slice(0, 3);
};

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleString('pl-PL', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const goToPage = (page: number) => {
  currentPage.value = page;
  fetchAuctions();
};

const paginationRange = computed(() => {
  const range = [];
  for (let i = Math.max(1, currentPage.value - 2); i <= Math.min(totalPages.value, currentPage.value + 2); i++) {
    range.push(i);
  }
  return range;
});
</script>

<template>
  <div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      <div class="text-center mb-12">
        <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight lg:text-6xl">
          Witaj, {{ currentFirm?.name }}!
        </h1>
        <p class="mt-5 max-w-xl mx-auto text-xl text-gray-500">
          Zarządzaj swoimi aukcjami i ofertami w jednym miejscu.
        </p>
      </div>

      <div class="mb-12">
        <a
            :href="`https://new.mega1000.pl/magazyn/aktualizacja-cen/${currentFirm?.id}/zaktualizuj?isByFirm=true`"
            target="_blank"
            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
            :class="{ 'bg-red-600 hover:bg-red-700 focus:ring-red-500': haveToFillPrices }"
        >
          <Edit class="mr-2 h-5 w-5" />
          Zaktualizuj ceny podstawowe styropianów
        </a>
      </div>

      <div class="space-y-8">
        <div v-for="auction in auctions" :key="auction.id" class="bg-white shadow overflow-hidden sm:rounded-lg">
          <!-- Auction details (same as before) -->
          <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              Numer przetargu: {{ auction?.chat?.order?.id }}
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
              Szczegóły i zarządzanie przetargiem
            </p>
          </div>
          <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500 flex items-center">
                  <Clock class="mr-2 h-5 w-5 text-gray-400" />
                  Koniec przetargu
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  {{ formatDate(auction.end_of_auction) }}
                </dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500 flex items-center">
                  <Truck class="mr-2 h-5 w-5 text-gray-400" />
                  Data dostawy
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  {{ auction.date_of_delivery }}
                </dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500 flex items-center">
                  <TrendingUp class="mr-2 h-5 w-5 text-gray-400" />
                  Udział ceny/jakości
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  {{ auction.price }}% / {{ auction.quality }}%
                </dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500 flex items-center">
                  <Calendar class="mr-2 h-5 w-5 text-gray-400" />
                  Data rozpoczęcia
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  {{ formatDate(auction.created_at) }}
                </dd>
              </div>
            </dl>
          </div>
          <div class="px-4 py-5 sm:px-6">
            <a :href="auction.editPricesLink" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
              <Edit class="mr-2 h-5 w-5" />
              Zmień swoje ceny w tym przetargu
            </a>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <nav aria-label="Page navigation" class="flex justify-center mt-8">
        <ul class="inline-flex items-center -space-x-px">
          <li :class="{ 'opacity-50': currentPage === 1 }">
            <button
                class="py-2 px-3 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 rounded-l-lg"
                @click="currentPage > 1 && goToPage(currentPage - 1)"
                :disabled="currentPage === 1"
            >
              Poprzednia
            </button>
          </li>
          <li v-for="page in paginationRange" :key="page" :class="{ 'bg-blue-500 border-blue-500 text-white': currentPage === page, 'border-gray-300 text-gray-500 hover:bg-gray-100': currentPage !== page }">
            <button
                class="py-2 px-3 leading-tight bg-white border hover:text-gray-700"
                @click="goToPage(page)"
            >
              {{ page }}
            </button>
          </li>
          <li :class="{ 'opacity-50': currentPage === totalPages }">
            <button
                class="py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 rounded-r-lg"
                @click="currentPage < totalPages && goToPage(currentPage + 1)"
                :disabled="currentPage === totalPages"
            >
              Następna
            </button>
          </li>
        </ul>
      </nav>
    </div>
  </div>

  <div v-if="loading" class="fixed top-0 left-0 w-screen h-screen flex justify-center items-center bg-gray-500 bg-opacity-50">
    <Loader :showLoader="loading" />
  </div>
</template>
