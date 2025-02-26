<script setup lang="ts">
import { getToken, setCookie } from "~~/helpers/authenticator";

let emailInput = "";
let passwordInput = "";
let phoneNumberInput = "";

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
    client_id: config.AUTH_CLIENT_ID,
    username: emailInput,
    password: passwordInput,
    phone: phoneNumberInput
  };
  try {
    const res = await shopApi.post("/api/register", params);
    setCookie(res.data);

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
  <div class="flex py-20 xl:py-40">
    <div class="m-auto">

        <div
            class="w-screen max-w-sm md:max-w-md lg:max-w-2xl xl:max-w-4xl p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-6 md:p-8"
        >
          <form class="space-y-6" @submit="handleSubmit">
            <h5 class="text-xl xl:text-2xl font-medium text-gray-900">
              Zarejestruj się do naszego sklepu
            </h5>
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
                  placeholder="nazwa@domena.com"
                  required
                  v-model="emailInput"
                  :disabled="loading"
              />
            </div>
            <div>
              <label
                  for="phone"
                  class="block mb-2 text-sm lg:text-lg font-medium text-gray-900"
              >Nr telefonu</label>
              <input
                  type="phone"
                  name="phone"
                  id="password"
                  placeholder="+12 345 678 901"
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm lg:text-lg rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                  required
                  v-model="phoneNumberInput"
                  :disabled="loading"
              />
            </div>
            <div>
              <label
                  for="password"
                  class="block mb-2 text-sm lg:text-lg font-medium text-gray-900"
              >Hasło</label>
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

            <label for="rules" class="ml-2 text-sm font-medium text-gray-900">Zapoznałem się z <nuxt-link class="text-blue" href="https://mega1000.pl/custom/5">regulaminem</nuxt-link></label>

            <button
                type="submit"
                :disabled="loading"
                class="w-full text-white bg-cyan-400 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm lg:text-lg px-5 py-2.5 text-center"
            >
              Zarejestruj się
            </button>
          </form>
        </div>
    </div>
  </div>
</template>
