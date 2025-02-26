<template>
  <div v-if="showLoader" class="loader-container">
    <div class="loader">
      <div class="styrofoam-cube">
        <div v-for="face in ['front', 'back', 'right', 'left', 'top', 'bottom']" :key="face" :class="['face', face]">
          <div class="inner-face"></div>
        </div>
      </div>
    </div>
    <div class="loader-text">
      <h2 class="title">Ładowanie...</h2>
      <p class="subtitle">Przygotowujemy coś wyjątkowego dla Ciebie!</p>
      <p class="fact" :key="currentFact">{{ currentFact }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  showLoader: {
    type: Boolean,
    required: true,
  },
});

const facts = [
  "Czy wiesz, że styropian składa się w 98% z powietrza? To jak nadmuchana kanapka izolacyjna!",
  "Styropian jest tak odporny na wilgoć, że mógłby przetrwać potop… gdyby tylko nie był taki lekki!",
  "Styropian można poddać recyklingowi w 100%. To jak Feniks materiałów budowlanych - zawsze się odrodzi!",
  "Styropian został wynaleziony przypadkowo w 1941 roku. Ktoś chciał zrobić gumę, a stworzył 'powietrzną poduszkę'!",
  "Styropian jest tak dobrym izolatorem dźwięku, że nawet sąsiad grający na perkusji o 3 nad ranem staje się znośny!",
  "Gdyby pingwiny znały styropian, Antarktyda byłaby pełna przytulnych igloo!",
  "Styropian jest tak lekki, że gdyby zrobić z niego łódkę, mogłaby pływać po chmurach!",
  "Styropian rozszerza się i kurczy wraz z temperaturą. To jak oddychający sweter dla twojego domu!",
  "Gdyby styropian był superbohaterem, jego supermoce to: lekkość, izolacja i niezniszczalność!",
  "Styropian jest tak wszechstronny, że można z niego zrobić wszystko: od kubków po elementy rakiet kosmicznych. Dosłownie - NASA go używa!",
  "Styropian to ulubiony materiał filmowców do tworzenia 'kamiennych' dekoracji. Następnym razem, gdy zobaczysz bohatera rzucającego głazem, pomyśl: 'Ale lekki ten kamień!'",
  "Gdyby ryby odkryły styropian, podwodne miasta wyglądałyby jak pływające torty weselne!",
  "Styropian jest tak dobrym izolatorem, że gdyby pingwiny go odkryły, Antarktyda zamieniłaby się w tropikalny raj!",
  "Styropian jest odporny na bakterie. To jak naturalny antybiotyk dla twojego domu!",
  "Gdyby domy były z styropianu, trzęsienia ziemi byłyby jak kołysanka dla budynków!"
];

const currentFact = ref(facts[0]);
let factInterval;

onMounted(() => {
  let index = 0;
  factInterval = setInterval(() => {
    index = (index + 1) % facts.length;
    currentFact.value = facts[index];
  }, 5000);
});

onUnmounted(() => {
  clearInterval(factInterval);
});
</script>

<style scoped>
.loader-container {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #f0fff4 0%, #e6fffa 100%);
  font-family: 'Arial', sans-serif;
  z-index: 9999;
}

.loader {
  width: 150px;
  height: 150px;
  position: relative;
  perspective: 1000px;
  margin-bottom: 2rem;
}

.styrofoam-cube {
  width: 100%;
  height: 100%;
  position: relative;
  transform-style: preserve-3d;
  animation: rotate 8s ease-in-out infinite;
}

.face {
  position: absolute;
  width: 150px;
  height: 150px;
  background: rgba(255, 255, 255, 0.9);
  border: 2px solid #10b981;
  box-shadow: 0 0 10px rgba(16, 185, 129, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.inner-face {
  width: 80%;
  height: 80%;
  background: linear-gradient(45deg, #ecfdf5, #ffffff);
  border-radius: 10px;
  box-shadow: inset 0 0 15px rgba(16, 185, 129, 0.2);
  transform: rotate(45deg);
}

.front  { transform: rotateY(0deg) translateZ(75px); }
.back   { transform: rotateY(180deg) translateZ(75px); }
.right  { transform: rotateY(90deg) translateZ(75px); }
.left   { transform: rotateY(-90deg) translateZ(75px); }
.top    { transform: rotateX(90deg) translateZ(75px); }
.bottom { transform: rotateX(-90deg) translateZ(75px); }

@keyframes rotate {
  0%, 100% { transform: rotate3d(1, 1, 1, 0deg); }
  25% { transform: rotate3d(1, 1, 1, 90deg); }
  50% { transform: rotate3d(1, 1, 1, 180deg); }
  75% { transform: rotate3d(1, 1, 1, 270deg); }
}

.loader-text {
  text-align: center;
  max-width: 80%;
}

.title {
  font-size: 3rem;
  color: #047857;
  margin-bottom: 0.5rem;
  text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
}

.subtitle {
  font-size: 1.5rem;
  color: #065f46;
  margin-bottom: 1rem;
}

.fact {
  font-style: italic;
  font-size: 1.25rem;
  color: #059669;
  background: rgba(255, 255, 255, 0.8);
  padding: 1rem;
  border-radius: 0.5rem;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  transition: all 0.5s ease;
  animation: fadeInOut 5s ease-in-out infinite;
}

@keyframes fadeInOut {
  0%, 100% { opacity: 0; transform: translateY(10px); }
  50% { opacity: 1; transform: translateY(0); }
}
</style>
