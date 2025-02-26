<script setup lang="ts">
import { checkIfUserIsLoggedIn } from "~/helpers/authenticationCheck";

const defaultErrorText = "Coś poszło nie tak";

const { $shopApi: shopApi } = useNuxtApp();

const errorMessage = ref<string | null>(null);

onMounted(async () => {
  await checkIfUserIsLoggedIn('');
});

const { data: chatHistory } = await useAsyncData(async () => {
  try {
    const res = await shopApi.get("/api/chat/getHistory");
    return res.data;
  } catch (error: any) {
    errorMessage.value =
      (error.data && error.data.error_message) ?? defaultErrorText;
  }
});
</script>

<template>
  <div class="flex justify-center my-20">
    <div v-if="chatHistory && chatHistory.length > 0">
      <a
        v-for="chat in chatHistory"
        is="button"
        :href="chat.url"
        class="p-5 bg-slate-100 text-xl text-gray-900 border border-gray-200 rounded-l-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700"
      >
        {{ chat.title }}
        {{
          chat.new_message ? "(Nowe wiadomości)" : "(Brak nowych wiadomości)"
        }}
      </a>
      <p v-if="errorMessage" class="text-2xl font-bold text-red-500">
        {{ errorMessage }}
      </p>
    </div>
    <p v-else class="text-3xl">Brak historii czatów</p>
  </div>
</template>
