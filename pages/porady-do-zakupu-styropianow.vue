<script setup>
import { ref } from 'vue';

const isLoading = ref(true);
const currentZipCode = localStorage.getItem('zipCode');
const description = ref('');
const { $shopApi: shopApi } = useNuxtApp();

// Function to call when the iframe has finished loading
function onIframeLoad() {
  isLoading.value = false;
}

onMounted(async () => {
  const data = await shopApi.get(`https://admin.mega1000.pl/api/categories/details/search?category=102`);
  description.value = data.data.description;
});
</script>

<template>
  <AskUserForZipCodeStyrofoarms v-if="!currentZipCode" />
  <div class="sm:w-full md:w-[70%] mx-auto text-lg">
    <div class="text-4xl">
      Jeśli chcesz wiedzieć jaki styropian wybrać lub nie wiesz jak go zamówić sprawdź tekst na dole lub kliknij <a style="color: #1e40af" href="#porady">tutaj</a>
    </div>
  <div style="position: relative; height: 80vh;">
    <!-- Loader displayed while isLoading is true -->
    <div v-if="isLoading" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; justify-content: center; align-items: center; background-color: rgba(255, 255, 255, 0.8); z-index: 100; font-weight: bold; font-size: larger">
      <span>Ładowanie tabeli, proszę czekać...</span>
    </div>

    <!-- IFrame with the load event listener -->
    <iframe :src="`https://admin.mega1000.pl/auctions/display-prices-table?zip-code=${currentZipCode}`" style="width: 100%; height: 100%;" @load="onIframeLoad"></iframe>

    <section class="py-20 px-4 bg-white">
      <div class="container mx-auto">
        <h2 class="text-4xl md:text-5xl font-bold mb-10 text-center">
          Zobacz mapę punktów odbioru w całej polsce
        </h2>
        <MagasinesMap />
      </div>
    </section>
  </div>

    <div id="porady" class="mt-4" v-html="description"></div>
  </div>
</template>
