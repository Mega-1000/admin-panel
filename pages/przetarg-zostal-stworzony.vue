<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-400 via-green-500 to-teal-600 flex items-center justify-center overflow-hidden relative">
    <div class="absolute inset-0 bg-pattern opacity-10"></div>
    <div class="text-center z-10 px-4 max-w-4xl mx-auto">
      <h1 class="text-9xl font-extrabold text-white mb-8 tracking-tight animate-title-reveal">
        Sukces!
      </h1>
      <p class="text-4xl font-light text-green-100 mb-12 animate-fade-in-up">
        Twój przetarg jest <span class="font-bold underline decoration-wavy decoration-yellow-300">gotowy do podboju rynku!</span> Wysłaliśmy aż 32 wiadomości do fakbryk z prośbą o wycenę.
      </p>

      <div class="relative mb-16 animate-fade-in-up" style="animation-delay: 0.5s;">
        <div class="absolute inset-0 animate-ping-slow">
          <svg class="w-40 h-40 mx-auto text-green-200 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <svg class="w-40 h-40 mx-auto text-white relative animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
      </div>

      <div class="mb-12 animate-fade-in-up" style="animation-delay: 1s;">
        <p class="text-2xl text-white mb-4">Szczegóły aukcji zostały wysłane na Twój adres email:</p>
        <div class="relative inline-block">
          <span class="text-3xl font-semibold text-yellow-300">{{ email }}</span>
          <div class="absolute -top-16 -right-16 animate-fly-out">
            <svg class="w-16 h-16 text-white transform rotate-45" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
            </svg>
          </div>
        </div>
      </div>

      <div class="flex flex-wrap justify-center gap-6 animate-fade-in-up mb-8" style="animation-delay: 1.5s;">
        <NuxtLink href="/account" class="action-button group">
          <span class="relative z-10 group-hover:text-white transition-colors duration-300">Przejdź do konta</span>
          <div class="button-bg"></div>
          <div class="button-sparkle"></div>
        </NuxtLink>

        <a :href="`http://admin.mega1000.pl/auctions/${orderId}/end`" class="action-button group">
          <span class="relative z-10 group-hover:text-white transition-colors duration-300">Tabela wycen live</span>
          <div class="button-bg"></div>
          <div class="button-sparkle"></div>
        </a>

        <a :href="`http://admin.mega1000.pl/chat/${chatAccessToken}`" class="action-button group">
          <span class="relative z-10 group-hover:text-white transition-colors duration-300">Dyskusja</span>
          <div class="button-bg"></div>
          <div class="button-sparkle"></div>
        </a>
      </div>
    </div>

    <div class="absolute bottom-0 left-0 w-full h-1/3 bg-gradient-to-t from-green-900 to-transparent opacity-20"></div>

    <div class="absolute top-10 right-10 animate-float">
      <svg class="w-32 h-32 text-green-200 opacity-20" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" d="M18 3a1 1 0 00-1.447-.894L8.763 6H5a3 3 0 000 6h.28l1.771 5.316A1 1 0 008 18h1a1 1 0 001-1v-4.382l6.553 3.276A1 1 0 0018 15V3z" clip-rule="evenodd"></path>
      </svg>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();
const email = ref('');
const chatAccessToken = ref('');
const orderId = ref('');

onMounted(() => {
  email.value = route.query.email || 'uzytkownik@example.com';
  chatAccessToken.value = route.query.chatToken;
  orderId.value = route.query.orderId;
});

const goToAuction = () => {
  router.push('/aukcja');
};
</script>

<style scoped>
@keyframes gradientAnimation {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

.bg-gradient {
  background: linear-gradient(-45deg, #4ade80, #22c55e, #14b8a6, #0d9488, #22c55e);
  background-size: 400% 400%;
  animation: gradientAnimation 15s ease infinite;
}

.bg-pattern {
  background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239CE6B4' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

@keyframes float {
  0%, 100% { transform: translateY(0) rotate(0deg); }
  50% { transform: translateY(-20px) rotate(5deg); }
}

@keyframes sparkle {
  0%, 100% { opacity: 0.25; }
  50% { opacity: 0.5; }
}

@keyframes titleReveal {
  0% { opacity: 0; transform: translateY(-50px) scale(0.8); }
  100% { opacity: 1; transform: translateY(0) scale(1); }
}

@keyframes flyOut {
  0% { opacity: 0; transform: translate(0, 0) rotate(45deg); }
  50% { opacity: 1; }
  100% { opacity: 0; transform: translate(100px, -100px) rotate(45deg); }
}

.animate-float { animation: float 6s ease-in-out infinite; }
.animate-sparkle { animation: sparkle 2s ease-in-out infinite; }
.animate-ping-slow { animation: ping 3s cubic-bezier(0, 0, 0.2, 1) infinite; }
.animate-title-reveal { animation: titleReveal 1.5s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards; }
.animate-fade-in-up { animation: fadeInUp 1s ease-out forwards; }
.animate-fly-out { animation: flyOut 3s ease-out infinite; }

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.action-button {
  @apply px-10 py-5 bg-white text-green-600 rounded-full font-bold text-2xl shadow-2xl hover:shadow-lg transform hover:scale-105 transition duration-300 ease-in-out overflow-hidden relative;
}

.button-bg {
  @apply absolute inset-0 bg-gradient-to-r from-yellow-400 via-green-500 to-teal-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 ease-out origin-left;
}

.button-sparkle {
  @apply absolute inset-0 bg-white opacity-25 animate-sparkle;
}
</style>
