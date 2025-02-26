<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-semibold text-gray-900">
          Wycena dostępnych firm dla tego styropianu
        </h3>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-900">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <div class="mt-4">
        <table class="w-full">
          <thead>
          <tr class="bg-gray-100">
            <th class="py-2 px-4 text-left">Producent</th>
            <th class="py-2 px-4 text-left">Cena jednostkowa netto</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="quote in quotes" :key="quote.id" class="border-b">
            <td class="py-2 px-4">{{ quote.manufacturer }}</td>
            <td class="py-2 px-4">{{ quote.price.net_purchase_price_basic_unit }} PLN</td>
          </tr>
          </tbody>
        </table>
      </div>

      <div class="mt-6 text-center">
        <button @click="$emit('close')" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
          Zamknij
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  quotes: {
    type: Array,
    required: true
  }
});

defineEmits(['close']);
</script>
