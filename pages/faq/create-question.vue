<script setup>
const { $shopApi: shopApi } = useNuxtApp();

const router = useRouter();

const categories = ref([]);
const question = ref({
  questions: [{ question: '', answer: '', questions: [], withForm: false }],
  category: 0
});

onMounted(async () => {
  await fetchCategories();
});

const fetchCategories = async () => {
  const { data } = await shopApi.get('/api/faqs/categories');
  categories.value = data;
};

const save = async () => {
  await shopApi.post('/api/faqs', question.value);

  router.push('/faq/create');
};
</script>

<template>
  <div class="w-[70%] mx-auto mt-8">
    <div>
      <h1 class="text-3xl">
        Dodaj pytanie
      </h1>
    </div>

    <div class="w-100">
      <form @submit.prevent="save">
        <label class="block">
          <span class="text-gray-700">Kategoria</span>
          <select v-model="question.category" class="form-select mt-1 block w-full border rounded" required>
            <option v-for="category in categories" :value="category">
              {{ category }}
            </option>
          </select>
        </label>

        <label class="block">
          <span class="text-gray-700">Pytanie</span>
          <input v-model="question.questions[0].question" class="form-input mt-1 block w-full p-1 border rounded" placeholder="Pytanie" required>
        </label>

        <label class="block mt-4">
          <span class="text-gray-700">Odpowiedź</span>
          <textarea v-model="question.questions[0].answer" class="form-input mt-1 h-[300px] block w-full p-1 border rounded" placeholder="Odpowiedź" required></textarea>
        </label>
        
        <button class="bg-green-500 rounded text-white px-4 py-2 mt-4" type="submit">
          Zapisz
        </button>
      </form>
    </div>
  </div>
</template>