<template>
<!--  <div class="relative">-->
<!--    <div class="flex transition-transform duration-500" :style="{ transform: `translateX(-${currentSlide * 100}%)` }">-->
<!--      <div v-for="(image, index) in images" :key="index" class="flex-none w-full">-->
<!--        <img :src="`/${image}`" class="w-full object-cover h-64" :alt="`Slide ${index + 1}`">-->
<!--      </div>-->
<!--    </div>-->
<!--    <div class="absolute inset-0 flex justify-between items-center">-->
<!--      <button @click="prevSlide" class="text-white p-2 focus:outline-none hover:opacity-75">Prev</button>-->
<!--      <button @click="nextSlide" class="text-white p-2 focus:outline-none hover:opacity-75">Next</button>-->
<!--    </div>-->
<!--  </div>-->
</template>

<script setup>
import { ref } from 'vue';
import { useIntervalFn } from '@vueuse/core';

const images = ref([
  'promo1.png',
  'promo2.png',
]);
const currentSlide = ref(0);

const { pause, resume } = useIntervalFn(() => {
  nextSlide();
}, 3000, { immediate: true });

function nextSlide() {
  currentSlide.value = (currentSlide.value + 1) % images.value.length;
}

function prevSlide() {
  currentSlide.value = (currentSlide.value - 1 + images.value.length) % images.value.length;
}
</script>
