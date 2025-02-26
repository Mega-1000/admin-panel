<template>
  <div class="hidden md:block  mx-auto my-8">
    <div class="bg-gradient-to-r from-green-600 to-emerald-700  rounded-xl shadow-lg overflow-hidden">
      <div class="p-6 sm:p-8">
        <h2 class="text-3xl font-bold mb-6 text-white text-center">
          Jak to <span class="text-yellow-300">działa?</span>
        </h2>
        <div class="flex flex-col sm:flex-row justify-between items-stretch space-y-4 sm:space-y-0 sm:space-x-4">
          <div
              v-for="(step, index) in steps"
              :key="index"
              class="flex-1 bg-white bg-opacity-10 rounded-lg p-4 flex flex-col items-center text-center transition-all duration-300 hover:bg-opacity-20 hover:transform hover:scale-105"
          >
            <div class="bg-gradient-to-r from-yellow-400 to-orange-500 p-2 rounded-full mb-3">
              <component :is="step.icon" class="h-6 w-6 text-white" />
            </div>
            <h3 class="font-semibold text-lg mb-1 text-white">{{ step.title }}</h3>
            <p class="text-xs text-indigo-200">{{ step.description }}</p>
            <span class="text-2xl font-bold text-white opacity-20 mt-2">0{{ index + 1 }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { FileText, Send, Mail, ShoppingCart } from 'lucide-vue-next'

const steps = ref([
  { icon: FileText, title: "Tworzysz przetarg", description: "Określaś potrzeby do twojej budowy" },
  { icon: Send, title: "Wysyłamy zapytania", description: "Do ponad 50 fabryk" },
  { icon: Mail, title: "Otrzymujesz wyceny", description: "Porównaj oferty od większości producentów w polsce" },
  { icon: ShoppingCart, title: "Składasz zamówienie", description: "Gwarantujemy najniższą cenę na rynku a jak znajdziesz taniej zwrócimy ci różnicę" }
])
</script>
