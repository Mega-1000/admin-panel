<template>
  <div class="bg-gray-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-3xl font-extrabold text-gray-900 mb-8">Zobacz także inne produkty z tej kategori</h2>
      <div class="relative">
        <div class="overflow-hidden">
          <div class="product-slider flex space-x-6 animate-slideX" ref="slider">
            <!-- Product 1 -->
            <nuxt-link :href="`/singleProduct/${product.id}`" v-for="product in products" :key="product.id" class="flex-shrink-0 bg-white shadow-md rounded-lg overflow-hidden">
              <img :src="`https://admin.mega1000.pl${product.url_for_website}`" :alt="product.name" class="h-48 w-full object-cover">
              <div class="px-4 py-3">
                <h3 class="text-lg font-semibold text-gray-900">{{ product.name }}</h3>
                <p class="text-gray-500">{{ product.price.gross_selling_price_calculated_unit }}PLN/M3</p>
              </div>
            </nuxt-link>
          </div>
        </div>
        <button class="absolute top-1/2 -translate-y-1/2 left-2 bg-white rounded-full p-2 shadow-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" @click="prevSlide">
          <svg class="h-6 w-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        <button class="absolute top-1/2 -translate-y-1/2 right-2 bg-white rounded-full p-2 shadow-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" @click="nextSlide">
          <svg class="h-6 w-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const slider = ref(null);
const sliderWidth = ref(0);
const currentSlide = ref(0);

const props = defineProps({
  'products': Array
})

const products = props.products;

const nextSlide = () => {
  currentSlide.value++;
  if (currentSlide.value > products.length - Math.floor(sliderWidth.value / 256)) {
    currentSlide.value = 0;
  }
  slider.value.style.transform = `translateX(-${currentSlide.value * 256}px)`;
};

const prevSlide = () => {
  currentSlide.value--;
  if (currentSlide.value < 0) {
    currentSlide.value = products.length - Math.floor(sliderWidth.value / 256);
  }
  slider.value.style.transform = `translateX(-${currentSlide.value * 256}px)`;
};

const handleResize = () => {
  sliderWidth.value = slider.value.offsetWidth;
};

onMounted(() => {
  sliderWidth.value = slider.value.offsetWidth;
  window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
  window.removeEventListener('resize', handleResize);
});
</script>
