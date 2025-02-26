<template>
  <div ref="mapContainer" class="map-container mt-10 md:w-2/3 md:mx-auto">
    <!--    <div ref="overlay" class="overlay">-->
    <!--      Użyj dwóch palców aby wykonać interakcję z mapą-->
    <!--    </div>-->
  </div>
</template>

<script setup>
import 'leaflet/dist/leaflet.css'
import L from 'leaflet'
import { onMounted, ref } from 'vue'
import axios from 'axios'

const mapContainer = ref(null)
// const overlay = ref(null)
const userCoordinates = ref(null)

async function getCoordinatesFromZipcode(zipcode) {
  try {
    const response = await axios.get(`https://nominatim.openstreetmap.org/search?postalcode=${zipcode}&country=Poland&format=json`)
    if (response.data && response.data.length > 0) {
      return {
        lat: parseFloat(response.data[0].lat),
        lng: parseFloat(response.data[0].lon)
      }
    }
  } catch (error) {
    console.error('Failed to get coordinates from zipcode:', error)
  }
  return null
}

onMounted(async () => {
  const userZipcode = localStorage.getItem('zipCode');
  userCoordinates.value = await getCoordinatesFromZipcode(userZipcode);

  const map = L.map(mapContainer.value, {
  }).setView([52.1, 19.4], 6)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution:
        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
  }).addTo(map)

  try {
    const response = await axios.get('https://admin.mega1000.pl/api/styro-warehouses')
    const warehouses = response.data

    warehouses.forEach(warehouse => {
      try {
        const coords = JSON.parse(warehouse.cordinates)
        const marker = L.marker([coords.lat, coords.lng]).addTo(map)
        marker.bindPopup(`
            <a href="${warehouse.link}">
                <b>Magazyn odbioru: ${warehouse.symbol} - Kliknij aby zobaczyć produkty dostępne w tym punkcie</b>
            </a>
        `)
      } catch (error) {
        console.error('Failed to load warehouse data:', error)
      }
    })

    // Add user location marker if coordinates are available
    if (userCoordinates.value) {
      const userMarker = L.marker([userCoordinates.value.lat, userCoordinates.value.lng], {
        icon: L.divIcon({
          className: 'user-location-marker',
          html: '<div style="background-color: orange; width: 10px; height: 10px; border-radius: 50%;"></div>'
        })
      }).addTo(map)
      userMarker.bindTooltip("Wpisany przez ciebie kod pocztowy", {
        permanent: true,
        direction: 'top'
      }).openTooltip()

      // Center the map on user location
      map.setView([userCoordinates.value.lat, userCoordinates.value.lng], 8)
    }
  } catch (error) {
    console.error('Failed to load data:', error)
  }
});
</script>

<style scoped>
.map-container {
  position: relative;
  height: 500px;
}

.overlay {
  display: flex;
  justify-content: center;
  align-items: center;
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.8);
  color: #333;
  font-size: 18px;
  font-weight: bold;
  z-index: 1000;
  pointer-events: none;
}

.user-location-marker {
  background-color: orange;
  border-radius: 50%;
}
</style>
