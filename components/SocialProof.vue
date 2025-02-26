<template>
  <Transition name="fade">
    <div class="social-proof" v-show="showNotification">
      <div class="notification">
        <div class="notification-content">
          <div class="notification-image">
            <img src="/favicon.ico" alt="User Avatar" />
          </div>
          <div class="notification-text">
            <p class="name">{{ customerName }} z {{ location }}</p>
            <p class="action">{{ actionName }}</p>
            <p class="time">{{ timePeriod }}</p>
          </div>
        </div>
        <button class="close-button" @click="closeNotification" aria-label="Close">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

const showNotification = ref(false);
const customerName = ref('');
const location = ref('');
const actionName = ref('');
const timePeriod = ref('');
const randomImage = ref('');

const minInterval = 180000; // 3 minutes
const maxInterval = 900000; // 15 minutes
const visibilityTime = 10000; // 10 seconds

let popBackup = null;
let toggleVar = null;

const names = [
  "Anonimowy", "Ktoś", "Marek", "Piotrek", "Walerian", "Saszka2213", "henio", "Marta", "Jeremiasz",
  "Anna", "Karolina", "Tomasz", "Michał", "Ewa", "Zofia", "Krzysztof", "Magdalena", "Jakub", "Aleksandra",
  "Bartosz", "Natalia", "Wojciech", "Kamila", "Paweł", "Agnieszka", "Łukasz", "Katarzyna", "Marcin",
  "Monika", "Grzegorz", "Joanna", "Damian", "Weronika", "Robert", "Alicja", "Rafał", "Dominika"
];

const towns = [
  "Wrocław", "Poznań", "Katowice", "Warszawa", "Kalisz", "Nowa Wieś", "Gorzów Wielkopolski",
  "Zielona Góra", "Kłodawka", "Lublin", "Skierniewice", "Babimost", "Kraków", "Gdańsk", "Szczecin",
  "Łódź", "Bydgoszcz", "Białystok", "Częstochowa", "Radom", "Sosnowiec", "Toruń", "Kielce",
  "Rzeszów", "Olsztyn", "Bielsko-Biała", "Ruda Śląska", "Rybnik", "Tychy", "Dąbrowa Górnicza",
  "Płock", "Elbląg", "Opole", "Wałbrzych", "Włocławek", "Tarnów", "Chorzów", "Koszalin", "Legnica"
];

const recentActions = [
  "właśnie zamówił(a) 40.23m³ styropianu",
  "właśnie zamówił(a) 13m³ styropianu",
  "właśnie zamówił(a) 75.5m³ styropianu",
  "właśnie zamówił(a) 200m³ styropianu",
  "właśnie zapytał(a) o cenę 50m³ styropianu",
  "właśnie porównuje oferty na styropian",
  "właśnie zaoszczędził(a) 15% na zamówieniu styropianu",
  "właśnie skorzystał(a) z promocji na styropian",
  "właśnie zamówił(a) próbkę styropianu",
  "właśnie zapisał(a) się na newsletter",
  "właśnie skontaktował(a) się z doradcą",
  "właśnie dodał(a) styropian do koszyka",
  "właśnie przeczytał(a) artykuł o izolacji",
  "właśnie obejrzał(a) film instruktażowy",
  "właśnie polecił(a) nas znajomemu"
];

const avatars = [
  "/api/placeholder/50/50?text=👤1",
  "/api/placeholder/50/50?text=👤2",
  "/api/placeholder/50/50?text=👤3",
  "/api/placeholder/50/50?text=👤4",
  "/api/placeholder/50/50?text=👤5",
  "/api/placeholder/50/50?text=👤6",
  "/api/placeholder/50/50?text=👤7",
  "/api/placeholder/50/50?text=👤8",
  "/api/placeholder/50/50?text=👤9",
  "/api/placeholder/50/50?text=👤10"
];

const updateSocialProofData = () => {
  const selectedName = names[Math.floor(Math.random() * names.length)];
  const selectedTown = towns[Math.floor(Math.random() * towns.length)];
  const selectedAction = recentActions[Math.floor(Math.random() * recentActions.length)];
  const selectedAvatar = avatars[Math.floor(Math.random() * avatars.length)];

  customerName.value = selectedName;
  location.value = selectedTown;
  actionName.value = selectedAction;
  timePeriod.value = 'Właśnie teraz';
  randomImage.value = selectedAvatar;

  showNotification.value = true;

  popBackup = setTimeout(() => {
    showNotification.value = false;
  }, visibilityTime);
};

const closeNotification = () => {
  clearTimeout(popBackup);
  showNotification.value = false;
};

const scheduleNextNotification = () => {
  const interval = Math.floor(Math.random() * (maxInterval - minInterval + 1)) + minInterval;
  toggleVar = setTimeout(() => {
    updateSocialProofData();
    scheduleNextNotification();
  }, interval);
};

onMounted(() => {
  setTimeout(() => {
    updateSocialProofData();
    scheduleNextNotification();
  }, 30000); // Start first notification after 5 seconds
});

onBeforeUnmount(() => {
  clearTimeout(popBackup);
  clearTimeout(toggleVar);
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');

.social-proof {
  position: fixed;
  bottom: 24px;
  left: 24px;
  z-index: 9999;
  font-family: 'Inter', sans-serif;
}

.notification {
  width: 320px;
  background-color: #ffffff;
  border-radius: 12px;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1), 0 2px 4px rgba(0, 0, 0, 0.06);
  oflow: hidden;
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.notification:hover {
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15), 0 4px 8px rgba(0, 0, 0, 0.1);
  transform: translateY(-4px);
}

.notification-content {
  display: flex;
  align-items: center;
  padding: 16px;
}

.notification-image img {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  object-fit: cover;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.notification-text {
  margin-left: 16px;
  flex: 1;
}

.name {
  font-size: 15px;
  font-weight: 600;
  color: #1a202c;
  margin: 0 0 4px;
}

.action {
  font-size: 14px;
  color: #4a5568;
  margin: 0 0 4px;
  line-height: 1.4;
}

.time {
  font-size: 13px;
  color: #718096;
  margin: 0;
}

.close-button {
  position: absolute;
  top: 12px;
  right: 12px;
  background: none;
  border: none;
  padding: 0;
  cursor: pointer;
  transition: opacity 0.2s ease;
  opacity: 0.5;
}

.close-button:hover {
  opacity: 1;
}

.close-button svg {
  width: 20px;
  height: 20px;
  color: #718096;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s ease, transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: translateY(20px) scale(0.95);
}
</style>
