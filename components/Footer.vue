<script setup>
import { ref, onMounted } from 'vue';

const zipCode = ref('');
const zipCodeChangeModalShown = ref(false);

onMounted(() => {
  zipCode.value = localStorage.getItem('zipCode');
});
</script>

<template>
  <footer class="z-20 p-4 w-full bg-slate-100 border-t border-gray-200 shadow">
    <div class="container mx-auto">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="flex flex-col">
          <h3 class="text-lg font-semibold mb-2">O nas</h3>
          <p class="text-sm text-gray-600 mb-2">
            EPH - ELEKTRONICZNA PLATFORMA HANDLOWA
          </p>
          <p class="text-sm text-gray-600">NIP: 9121945342</p>
          <div class="mt-4 flex space-x-4">
            <a href="https://www.linkedin.com/company/eph-polska" target="_blank" class="text-gray-600 hover:text-blue-600">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-linkedin" viewBox="0 0 16 16">
                <path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854zm4.943 12.248V6.169H2.542v7.225zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248S2.4 3.226 2.4 3.934c0 .694.521 1.248 1.327 1.248zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016l.016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225z"/>
              </svg>
            </a>
            <a href="https://www.facebook.com/profile.php?id=61559753676464" target="_blank" class="text-gray-600 hover:text-blue-600">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
                <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/>
              </svg>
            </a>
          </div>
        </div>

        <div class="flex flex-col">
          <h3 class="text-lg font-semibold mb-2">Nasze gwarancje</h3>
          <div class="flex items-center mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="text-sm text-gray-600">Gwarancja najlepszej ceny</span>
          </div>
          <div class="flex items-center mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="text-sm text-gray-600">Certyfikat Rzetelnej Firmy</span>
          </div>
        </div>

        <div class="flex flex-col">
          <h3 class="text-lg font-semibold mb-2">Kontakt</h3>
          <p class="text-sm text-gray-600 mb-2">
            Infolinia: <a href="tel:576205389" class="text-blue-600 hover:underline">691 801 594</a>
          </p>
          <div @click="zipCodeChangeModalShown = true" v-if="zipCode && !zipCodeChangeModalShown" class="cursor-pointer text-sm text-gray-600 hover:text-blue-600">
            Zmień swój kod pocztowy, aktualnie: {{ zipCode }}
          </div>
        </div>
      </div>

      <div class="mt-8 pt-4 border-t border-gray-200 text-center">
        <p class="text text-gray-500">
          <span style="margin-top: 20px">
            © {{ new Date().getFullYear() }} EPH - ELEKTRONICZNA PLATFORMA HANDLOWA. Wszystkie prawa zastrzeżone. Systemy informatyczne są wspierane i rozwijane przez
          </span>
          <a href="https://www.soft-synergy.com/" style="color: #1e40af;">
            Soft Synergy <img src="https://www.soft-synergy.com/assets/images/logo-dark.png" alt="Soft synergy logo" style="height: 30px; display: inline; width: 30px">
          </a>
        </p>
      </div>
    </div>
  </footer>

  <AskUserForZipCodeStyrofoarms v-if="zipCodeChangeModalShown" />
</template>
