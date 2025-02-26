<script setup lang="ts">
import { getToken, setCookie } from "~~/helpers/authenticator";

const { $shopApi: shopApi } = useNuxtApp();
const router = useRouter();

const loading = useState(() => false);
const errorMessage = useState(() => "");
const userToken = useUserToken();
const config = useRuntimeConfig().public;

const onConfirm = async () => {
  let params = JSON.parse(router.currentRoute.value.query.params as string);
  try {
    await shopApi.post("/api/new_order", params);
    params = {
      grant_type: "password",
      client_id: config.AUTH_CLIENT_ID,
      client_secret: config.AUTH_CLIENT_SECRET,
      username: params.customer_login,
      password: params.phone,
      scope: "",
    };
    try {
      const res = await shopApi.post("oauth/token", params);
      setCookie(res.data);
      await router.push("/account");
      userToken.value = getToken();
    } catch (err: any) {
      errorMessage.value = "Coś poszło nie tak";
    } finally {
      loading.value = false;
    }
  } catch (err: any) {
    errorMessage.value = err.response.data.error_message || "Wystąpił błąd";
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div
    class="w-screen max-w-sm md:max-w-md lg:max-w-2xl xl:max-w-4xl p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-6 md:p-8 mx-auto mt-12">
    <div class="mt-4 font-bold text-3xl mb-4 text-center">
      !!! Super cena dodatkowy rabat !!!
      <br>
      w przypadku samoobsługi
      <primaryButton :disabled="false" class="mt-4">
        <nuxt-link to="/">Przejdź do sklepu</nuxt-link>
      </primaryButton>
    </div>

    <div class="mt-20 font-bold text-3xl text-center mb-4">
      Proszę o rozmowę z konsultantem
    </div>

    <primaryButton :disabled="loading" @click="onConfirm">
      Rozpocznij rozmowę z konsultantem ( godzina 7 - 22 )
    </primaryButton>
  </div>
</template>
