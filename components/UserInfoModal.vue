<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 w-full max-w-md">
      <h3 class="text-2xl font-bold mb-6">Podaj swoje dane kontaktowe</h3>
      <form @submit.prevent="submitForm" class="space-y-4">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input
              id="email"
              v-model="email"
              type="email"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="twoj@email.com"
          >
        </div>
        <div>
          <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Numer telefonu</label>
          <input
              id="phone"
              v-model="phone"
              type="tel"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="123456789"
          >
        </div>
        <div>
          <label for="zipCode" class="block text-sm font-medium text-gray-700 mb-1">Kod pocztowy</label>
          <input
              id="zipCode"
              v-model="zipCode"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="00-000"
          >
        </div>
        <div class="flex justify-between mt-6">
          <button
              type="button"
              @click="$emit('close')"
              class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg transition-colors duration-300"
          >
            Anuluj
          </button>
          <button
              type="submit"
              class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-300"
          >
            Potwierdź
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const email = ref('');
const phone = ref('');
const zipCode = ref('');

const emit = defineEmits(['confirm', 'close']);

const submitForm = () => {
  emit('confirm', {
    email: email.value,
    phone: phone.value,
    zipCode: zipCode.value
  });
};
</script>
