<template>
  <div class="logo-slider">
    <vue-slick-carousel :arrows="true" :autoplay="true" :autoplaySpeed="3000" :infinite="true">
      <div v-for="(logo, index) in logos" :key="index" class="logo">
        <img :src="logo.src" :alt="logo.alt" />
      </div>
    </vue-slick-carousel>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'
import VueSlickCarousel from 'vue-slick-carousel'
import 'slick-carousel/slick/slick.css'
import 'slick-carousel/slick/slick-theme.css'

const props = defineProps({
  logos: {
    type: Array,
    required: true
  }
})
</script>

<style scoped>
.logo-slider .logo img {
  width: 100%;
  padding: 10px; /* Adjust spacing around images */
}
</style>
