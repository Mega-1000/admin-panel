<script setup>
  const { $shopApi: shopApi } = useNuxtApp();

  const question = ref({});
  const route = useRoute();
  const router = useRouter();
  
  onMounted(async () => {
    console.log(route);
    await fetchQuestion();
  });

  const fetchQuestion = async() => {
    const { data } = await shopApi.get(`/api/faqs/${route.params.id}`);
    question.value = data;
  }

  const save = async () => {
    await shopApi.put(`/api/faqs/${route.params.id}`, question.value);

    router.push('/faq/create');
  }
</script>

<template>
  <div class="w-[70%] mx-auto mt-8" v-if="question.questions">
    <div class="flex justify-between">
      <h1 class="text-3xl">
        Edytuj pytanie
      </h1>

      <nuxt-link class="bg-blue-500 rounded text-white px-4 py-2" href="/faq/create">
        Wróć
      </nuxt-link>
    </div>

    <div class="w-100">
      <form @submit.prevent="save">
        <label class="block">
          <span class="text-gray-700">Pytanie</span>
          <input v-model="question.questions[0].question" class="form-input mt-1 block w-full" placeholder="Pytanie">
        </label>

        <label class="block mt-4">
          <span class="text-gray-700">Odpowiedź</span>
          <textarea v-model="question.questions[0].answer" class="form-input mt-1 h-[300px] block w-full" placeholder="Odpowiedź"></textarea>
        </label>
        
        <button class="bg-green-500 rounded text-white px-4 py-2 mt-4" type="submit">
          Zapisz
        </button>
      </form>
    </div>
  </div>
</template>