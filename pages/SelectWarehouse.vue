<template>
  <div class="w-2/3 mx-auto">
    <div ref="mapContainer" class="map-container mt-10"></div>

    <form @submit.prevent="submitForm">
      <div class="mt-3" v-for="warehouse in warehouses" :key="warehouse.id">
        <input type="radio" :value="warehouse" v-model="selectedWarehouse" :id="warehouse.id">
        <label :for="warehouse.id">{{ warehouse.symbol }}</label>
        <a :href="`https://www.google.com/maps/search/?api=1&query=${warehouse.adresString}`" target="_blank" class="ml-2 text-blue-500">Zobacz punkt odbioru na mapie</a>
      </div>

      <SubmitButton :disabled="loading" class="mt-4">
        Zapisz punkt odbioru i przejdź do następnego kroku
      </SubmitButton>
    </form>

    <div v-if="loading" class="fixed top-0 left-0 w-screen h-screen flex justify-center items-center bg-gray-500 bg-opacity-50 z-100">
      <div class="bg-white rounded p-5">
        <div class="flex justify-center items-center">
          <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
          </svg>
          <span class="text-gray-900 text-lg">Ładowanie...</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import Swal from 'sweetalert2'
import { onMounted, ref, watch } from 'vue'
import { useNuxtApp, useRouter, useRoute } from '#imports'

const { $shopApi: shopApi } = useNuxtApp();
const route = useRoute();
const router = useRouter();
const mapContainer = ref(null);
const selectedWarehouse = ref(null);
const warehouses = ref([]);
const loading = ref(false);

onMounted(async () => {
  const map = L.map(mapContainer.value).setView([52.1, 19.4], 6)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution:
        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
  }).addTo(map)

  const { data: response } = await shopApi.get(`/api/orders/get-warehouses-for-order/${route.query.token}`);
  warehouses.value = response[0];

  warehouses.value.forEach(warehouse => {
    const coordinates = JSON.parse(warehouse.cordinates);
    const marker = L.marker([coordinates.lat, coordinates.lng]).addTo(map);
    marker.bindPopup(`<b>Magazyn odbioru: ${warehouse.symbol}</b>`);

    marker.on('click', () => {
      selectedWarehouse.value = warehouse;
    });
  });

  Swal.fire(
      'Uwaga!',
      'Prosimy o wybranie punktu odbioru w którym odbierzecie Państwo swoje zamówienie.',
      'info'
  );
});

watch(selectedWarehouse, (newVal) => {
  if (!newVal) return;
});

const submitForm = async () => {
  if (!selectedWarehouse.value?.id) {
    await Swal.fire('Nie wybrano punktu odbioru zamówienia!', '', 'error');
    return;
  }

  loading.value = true;
  await shopApi.post(`/api/set-warehouse/${selectedWarehouse.value.id}/${route.query.token}`);
  loading.value = false;

  await Swal.fire('Pomyślnie zapisano magazyn odbioru', 'Teraz możesz wykonać płatność', 'success');
  let total = parseFloat(route.query.total) + 50; // Adjust total if necessary
  await router.push(`/payment?token=${route.query.token}&total=${total}`);
}
</script>

<style scoped>
.map-container {
  height: 500px;
}
</style>
