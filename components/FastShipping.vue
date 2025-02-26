<template>
  <section class="py-16 px-4 bg-gradient-to-r from-emerald-600 to-emerald-800 text-white overflow-hidden relative animate-fade-in-up">
    <div class="container mx-auto text-center relative z-10">
      <h2 class="text-4xl md:text-6xl font-extrabold mb-8 animate-fade-in-up didact-gothic-regular">
        Dostawa do <pm>7 dni</pm> na terenie całej Polski!
      </h2>
      <p class="text-xl md:text-2xl mb-12 animate-fade-in-up animation-delay-300 max-w-3xl mx-auto">
        Dzięki naszej sieci magazynów w całej Polsce dostawa styropianu do Twojego domu jest szybka i bezpieczna!
      </p>
      <div class="flex flex-col md:flex-row gap-4 justify-center items-center animate-fade-in-up animation-delay-600">
        <NuxtLink
            v-for="(link, index) in links"
            :key="index"
            :to="link.href"
            class="bg-white text-emerald-700 font-bold py-3 px-6 rounded-full transition-all duration-300 hover:bg-yellow-300 hover:text-emerald-800 hover:scale-105 shadow-lg hover:shadow-xl w-full md:w-auto"
        >
          {{ link.text }}
        </NuxtLink>
      </div>
    </div>
    <div class="absolute inset-0 bg-emerald-600 opacity-20 animate-wave"></div>
  </section>
</template>

<script setup>
const links = [
  { href: "/100styropiany-elewacyjne/143", text: "Styropian elewacyjny" },
  { href: "/10styropiany/91", text: "Styropian posadzkowy" },
  { href: "/10styropiany-wodoodporne/5", text: "Styropian fundamentowy" }
];
</script>

<style scoped>
pm {
  background: linear-gradient(-180deg, #f99d9d 15%, #f5d3d3 94%);
  padding: 2px 6px;
  font-style: normal;
  color: #343a40;
  border-radius: 4px;
  overflow: hidden;
}

.animate-fade-in-up {
  animation: fadeInUp 0.6s ease-out forwards;
  opacity: 0;
}

.animation-delay-300 {
  animation-delay: 300ms;
}

.animation-delay-600 {
  animation-delay: 600ms;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-wave {
  animation: wave 8s linear infinite;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
  background-size: 200% 100%;
}

@keyframes wave {
  0% { background-position: 100% 0; }
  100% { background-position: -100% 0; }
}
</style>
