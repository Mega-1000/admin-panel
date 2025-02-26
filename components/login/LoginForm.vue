<script setup lang="ts">
import { getToken, setCookie } from "~~/helpers/authenticator";

let emailInput = "";
let passwordInput = "";

const loading = useState(() => false);
const errorMessage = useState(() => "");

const router = useRouter();
const config = useRuntimeConfig().public;

const { $shopApi: shopApi } = useNuxtApp();
const userToken = useUserToken();
const emit = defineEmits(["loginFailed"]);

const handleSubmit = async (e: Event) => {
  e.preventDefault();
  loading.value = true;

  const params = {
    grant_type: "password",
    client_id: config.AUTH_CLIENT_ID,
    client_secret: config.AUTH_CLIENT_SECRET,
    username: emailInput,
    password: passwordInput,
    scope: "",
  };
  try {
    const res = await shopApi.post("oauth/token", params);
    setCookie(res.data);
    window.dispatchEvent(new CustomEvent('token-refreshed'));

    const redirect = router.currentRoute.value.query.redirect;
    await router.push(`${redirect ?? "/account"}`);
    userToken.value = getToken();
  } catch (err: any) {
    emit("loginFailed", {
      email: emailInput,
      password: passwordInput
    });
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div
    class="w-screen max-w-sm md:max-w-md lg:max-w-2xl xl:max-w-4xl p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-6 md:p-8"
  >
    <form class="space-y-6" @submit="handleSubmit">
      <h5 class="text-xl xl:text-2xl font-medium text-gray-900">
        Zaloguj się na swoje konto
      </h5>
      <p>
        Uwaga! Jeżeli złożyłeś już zapytanie, możesz zalogować się na swoje konto. Jako hasło użyj numeru telefonu podanego przy składaniu zamówienia
      </p>
      <div>
        <label
          for="email"
          class="block mb-2 text-sm lg:text-lg font-medium text-gray-900"
          >Email</label
        >
        <input
          type="email"
          name="email"
          id="email"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm lg:text-lg rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
          placeholder="name@company.com"
          required
          v-model="emailInput"
          :disabled="loading"
        />
      </div>
      <div>
        <label
          for="password"
          class="block mb-2 text-sm lg:text-lg font-medium text-gray-900"
          >Hasło (w domyśle numer telefonu)</label
        >
        <input
          type="password"
          name="password"
          id="password"
          placeholder="•••••••••"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm lg:text-lg rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
          required
          v-model="passwordInput"
          :disabled="loading"
        />
      </div>
      <p class="mt-2 text-sm text-red-600">
        {{ errorMessage }}
      </p>
      <button
        type="submit"
        :disabled="loading"
        class="w-full text-white bg-cyan-400 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm lg:text-lg px-5 py-2.5 text-center"
      >
        Zaloguj się
      </button>
    </form>
  </div>
</template>
