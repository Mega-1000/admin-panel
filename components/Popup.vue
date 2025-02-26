<template>
  <!-- <div v-if="show" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center" style="z-index: 999999" @click="hidePopup"></div>
  <div v-if="show" class="fixed inset-0 flex items-center justify-center" style="z-index: 999999">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-md mx-auto relative z-100 text-center">
      <button @click="hidePopup" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
      <h2 class="text-2xl font-bold mb-4">
        Gwarantujemy, że zaoszczędzisz minimalnie <em>1000zł</em>
        <br> Skontaktujemy się z tobą w <pm>23 sekundy</pm>
      </h2>
      <p class="mb-4">Podaj nam swój numer telefonu:</p>
      <input
          type="text"
          v-model="phoneNumber"
          placeholder="Np. 342 234 546"
          class="w-full p-2 mb-4 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
      />
      <button @click="submitPhoneNumber" class="w-full py-2 bg-blue-500 text-white rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Wyślij</button>
    </div>
  </div> -->
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import Swal from 'sweetalert2'

const show = ref(false);
const phoneNumber = ref('');
const { $shopApi: shopApi } = useNuxtApp();

const showPopup = () => {
  show.value = true;
};

const hidePopup = () => {
  if (phoneNumber.value) {
    shopApi.post('api/contact-approach/create', {
      phone_number: phoneNumber.value,
      referred_by_user_id: 57352,
    });
  }

  localStorage.setItem('formSubmitted', 'true');
  show.value = false;
};

const submitPhoneNumber = async () => {
  await shopApi.post('api/contact-approach/create', {
    phone_number: phoneNumber.value,
    referred_by_user_id: 57352,
  });

  Swal.fire('Dziękujemy! Skontaktujemy się z tobą szybko!', '', 'success')
  localStorage.setItem('formSubmitted', 'true');
  hidePopup();
};

const handleMouseLeave = (event) => {
  if (event.clientY <= 0 && !localStorage.getItem('formSubmitted')) {
    showPopup();
  }
};

const handlePopState = (event) => {
  const referrer = document.referrer;
  const targetUrl = "https://mega1000.pl";

  if (referrer.includes(targetUrl) && !localStorage.getItem('formSubmitted')) {
    showPopup();
  }
};

onMounted(() => {
  if (!localStorage.getItem('formSubmitted')) {
    document.addEventListener('mouseleave', handleMouseLeave);
    window.history.pushState(null, null, window.location.pathname);
    window.addEventListener('popstate', handlePopState);
  }
});

onBeforeUnmount(() => {
  document.removeEventListener('mouseleave', handleMouseLeave);
  window.removeEventListener('popstate', handlePopState);
});
</script>


<style scoped>
.popup {
  display: block;
  position: fixed;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  padding: 20px;
  background: white;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  z-index: 1000;
}

.popup-overlay {
  display: block;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 999;
}

.popup h2 {
  margin-top: 0;
}

.close-button {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  font-size: 1.5rem;
  cursor: pointer;
}

em {
  background: -webkit-gradient(linear, left top, left bottom, color-stop(15%, #c1f99d), color-stop(94%, #e0f5d3));
  background: linear-gradient(-180deg, #c1f99d 15%, #e0f5d3 94%);
  padding: 2px;
  font-style: normal;
  color: #343a40;
  border-radius: 4px;
  overflow: hidden;
}

pm {
  background: -webkit-gradient(linear, left top, left bottom, color-stop(15%, #f99d9d), color-stop(94%, #f5d3d3));
  background: linear-gradient(-180deg, #f99d9d 15%, #f5d3d3 94%);
  padding: 2px;
  font-style: normal;
  color: #343a40;
  border-radius: 4px;
  overflow: hidden;
}
</style>
