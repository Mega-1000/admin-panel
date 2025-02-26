<script setup>
  const { params } = useRoute();
  const { $shopApi: shopApi } = useNuxtApp();
  const router = useRouter();

  const question = ref({});

  onMounted(async () => {
    await fetchQuestion();
  });

  const fetchQuestion = async () => {
    const { data } = await shopApi.get(`/api/faqs/${params.id}`);
    question.value = data;

    question.value.questions.push({ question: '', answer: '', questions: [], withForm: false });
  };

  const lastQuestionIndex = computed(() => {
    return question.value.questions.length - 1;
  });

  const save = async () => {
    await shopApi.put(`/api/faqs/${params.id}`, question.value);

    router.push('/faq');
  };
</script>

<template>
  <div class="w-[70%] mx-auto mt-8">
    <div>
      <h1 class="text-3xl">
        Dodaj pytanie
      </h1>
    </div>
    {{ question.questions }}

    <div class="w-100" v-if="question.questions">
      <form @submit.prevent="save">
        <label class="block">
          <span class="text-gray-700">Pytanie</span>
          <input v-model="question.questions[lastQuestionIndex].question" class="form-input mt-1 block w-full p-1 border rounded" placeholder="Pytanie" required>
        </label>

        <label class="block mt-4">
          <span class="text-gray-700">Odpowiedź</span>
          <textarea v-model="question.questions[lastQuestionIndex].answer" class="form-input mt-1 h-[300px] block w-full p-1 border rounded" placeholder="Odpowiedź" required></textarea>
        </label>
        
        <button class="bg-green-500 rounded text-white px-4 py-2 mt-4" type="submit">
          Zapisz
        </button>
      </form>
    </div>
  </div>
</template>