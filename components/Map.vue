<template>
  <div ref="mapContainer" class="map-container mt-10 md:w-2/3 md:mx-auto"></div>
</template>

<script setup>
import 'leaflet/dist/leaflet.css'
import L from 'leaflet'
import { onMounted, ref, watchEffect } from 'vue'

const props = defineProps({
  points: {
    type: Array,
    required: true,
  },
})

const mapContainer = ref(null)

onMounted(() => {
  const map = L.map(mapContainer.value).setView([52.1, 19.4], 6)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution:
        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
  }).addTo(map)

  watchEffect(() => {
    props.points.forEach(point => {
      const marker = L.marker([point.lat, point.lng]).addTo(map)
      marker.bindPopup(`
        <a href="${point.link}">
            <b>Magazyn odbioru: ${point.symbol} - Kliknij aby zobaczyć produkty dostępne w tym punkcie</b>
        </a>
      `)
    })
  })
})
</script>

<style scoped>
.map-container {
  height: 500px;
}
</style>
