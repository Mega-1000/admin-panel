<script setup>
import { checkIfUserIsLoggedIn } from "~/helpers/authenticationCheck";
import swal from "sweetalert2";

const refereedPhoneNumber = ref('');
const currentUser = ref({});
const userIdEncoded = ref('');
const { $shopApi: shopApi } = useNuxtApp();
const activeReferrals = ref([]);

onMounted(async () => {
  currentUser.value = await checkIfUserIsLoggedIn();
  userIdEncoded.value = btoa(currentUser.value.id);

  await loadReferrals();
});

const submitForm = async () => {
  const {data: response} = await shopApi.post('api/contact-approach/create', {
    phone_number: refereedPhoneNumber.value,
    referred_by_user_id: currentUser.value.id,
  });

  if (response.success === false) {
    return await swal.fire('Ten numer telefonu jest już w bazie!', 'Nie możemy dodać tego numeru jako polecenie!', 'info');
  }

  if (response) {
    loadReferrals();
    await swal.fire('Pomyślnie wysłano numer telefonu!', 'Skontaktujemy się z twoim znajomym i powiadomimy cię jeśli dojdzie do zakupu!', 'success');
  }
};

const loadReferrals = async () => {
  const {data: response} = await shopApi.get(`/api/contact-approach/${currentUser.value.id}`);
  activeReferrals.value = response;
}

const copyReferralLink = async () => {
  const referralLink = `https://mega1000.pl/sklep?ref=${userIdEncoded.value}`;
  try {
    await navigator.clipboard.writeText(referralLink);
    await swal.fire('Skopiowano!', 'Twój link referencyjny został skopiowany do schowka.', 'success');
  } catch (err) {
    console.error('Failed to copy: ', err);
    await swal.fire('Błąd', 'Nie udało się skopiować linku.', 'error');
  }
};
</script>
<template>
  <div class="lg:pl-20 max-w-[90%] mb-40 mt-4">
    <h1 class="font-bold text-4xl">
      Zgarnij 30 zł polecając nas znajomym!
    </h1>
    <br>
    <p>
      Mamy świetną wiadomość: zaproś do nas dowolną ilość znajomych, którzy jeszcze nie kupowali styropianów na naszej stronie, i odbierz 30 zł na każde zamówienie nowego użytkownika! Twoje bonusy automatycznie zamienią się na zniżki przy kolejnych zakupach lub przelejemy ci na konto.
      <br>
      <br>
      Wystarczy, że wpiszesz tutaj numer telefonu twojego znajomego lub wyślesz mu twój link referencyjny.
    </p>
    <div class="my-5">
      Twój link referencyjny to: https://mega1000.pl/sklep?ref={{ userIdEncoded }}
      <button @click="copyReferralLink" class="ml-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700 transition duration-300">
        Kopiuj link
      </button>
    </div>
    <form @submit.prevent="submitForm">
      <TextInput :value="refereedPhoneNumber" @input="refereedPhoneNumber = $event" placeholder="Wpisz numer telefonu" />

      <SubmitButton class="mt-4">
        Wyślij numer
      </SubmitButton>
    </form>

    <div class="referrals-section my-10">
      <h2 class="font-bold text-3xl mb-4">Twoje polecone kontakty</h2>
      <div v-if="activeReferrals.length > 0">
        <ul>
          <li v-for="(referral, index) in activeReferrals" :key="index" class="mb-2">
            Numer telefonu: {{ referral.phone_number }}, Status: <span :class="{'text-green-500': referral.done, 'text-red-500': !referral.done}">{{ referral.done ? 'Zakończony' : 'W trakcie' }}</span>
          </li>
        </ul>
      </div>
      <div v-else>
        <p>Nie masz jeszcze żadnych poleconych kontaktów.</p>
      </div>
    </div>
  </div>
</template>
