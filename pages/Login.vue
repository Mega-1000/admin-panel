<script setup lang="ts">
  import FinishRegistrationForm from "~/components/login/FinishRegistrationForm.vue";
  import {getToken, setCookie} from "~/helpers/authenticator";

  interface Credentials {
    email: string;
    password: string;
  }

  const { $shopApi: shopApi } = useNuxtApp();
  const registering = ref(false);
  let credentials: Credentials = reactive({} as Credentials);
  const router = useRouter();
  const config = useRuntimeConfig().public;

  const showFinishRegistration = (e: Credentials) => {
    registering.value = true;

    credentials = e;
  };

  const register = async () => {
    await shopApi.post("/api/register", {
      login: credentials.email,
      password: credentials.password,
    });

    const params = {
      grant_type: "password",
      client_id: config.AUTH_CLIENT_ID,
      client_secret: config.AUTH_CLIENT_SECRET,
      username: credentials.email,
      password: credentials.password,
      scope: "",
    };

    const res = await shopApi.post("oauth/token", params);
    setCookie(res.data);

    const redirect = router.currentRoute.value.query.redirect;
    router.push(`${redirect ?? "/account"}`);

    registering.value = false;

    window.dispatchEvent(new CustomEvent('token-refreshed'));
  };

  const cancelRegistration = () => {
    registering.value = false;
  };
</script>

<template>
  <div class="flex py-10">
    <div class="m-auto">
      <LoginForm class="max-w-[90vw]" @login-failed="showFinishRegistration($event)" v-if="registering === false"  />
      <FinishRegistrationForm class="max-w-[90vw]" @submit="register" @cancel="cancelRegistration" v-else />
    </div>
  </div>
</template>
