<template>
  <section>
  
  </section>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'

const logos = [
  '/genderka.webp',
  '/swisspor.webp',
  '/kubala.webp',
  '/arsanit.webp',
  '/austroterm.webp',up styropian w najniższych
  '/yetico.webp',
  '/knauf.png',
  '/polstyr_logo_without_background.png',
  '/unnamed.png',
  '',
  '/austroterm.webp',
  '/yetico.webp',
]

const slider = ref(null)
const visibleLogos = ref(6) // Default number of visible logos
const displayLogos = computed(() => {
  if (logos.length <= visibleLogos.value) return logos
  return [...logos, ...logos.slice(0, visibleLogos.value)]
})

onMounted(() => {
  updateSliderWidth()
  window.addEventListener('resize', updateSliderWidth)
})

watch(displayLogos, updateSliderWidth)

function updateSliderWidth() {
  if (slider.value) {
    const sliderWidth = slider.value.scrollWidth
    slider.value.style.setProperty('--slider-width', `${sliderWidth / 2}px`)
  }
}
</script>

<style scoped>
@keyframes slide {
  0% {
    transform: translateX(0);
  }
  100% {
    transform: translateX(calc(-1 * var(--slider-width)));
  }
}

.animate-slide {
  animation: slide 30s linear infinite;
}
</style>
