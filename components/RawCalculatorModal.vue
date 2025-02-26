<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
      <h3 class="text-2xl font-bold mb-4">Kalkulator styropianu</h3>
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Grubość styropianu (cm)</label>
          <select v-model="thickness" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option v-for="n in 30" :key="n" :value="n">{{ n }} cm</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Wykończenie krawędzi</label>
          <select v-model="edgeFinish" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="proste">Proste</option>
            <option value="frez">Frez</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Powierzchnia do ocieplenia (m²)</label>
          <input v-model.number="area" type="number" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="bg-gray-100 p-4 rounded-lg">
          <p class="font-medium">Wyniki:</p>
          <p>Objętość: <strong>{{ calculatedVolume.toFixed(2) }} m³</strong></p>
          <p>Powierzchnia: <strong>{{ calculatedArea.toFixed(2) }} m²</strong></p>
          <p>Ilość opakowań: <strong>{{ Math.ceil(calculatedPackages) }} op.</strong></p>
        </div>
      </div>
      <div class="mt-6 flex justify-between">
        <button @click="$emit('close')" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition-colors">
          Zamknij
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const emit = defineEmits(['close'])

const thickness = ref(1)
const edgeFinish = ref('proste')
const area = ref(0)

const calculatedVolume = computed(() => (thickness.value / 100) * area.value)
const calculatedArea = computed(() => area.value)
const calculatedPackages = computed(() => calculatedVolume.value / 0.3) // Assuming 0.3 m³ per package
</script>
