<script setup>
import { useAutoAnimate } from '@formkit/auto-animate/vue'
import { Modal } from "flowbite";
import draggable from 'vuedraggable';

const { $shopApi: shopApi } = useNuxtApp();

const config = useRuntimeConfig().public;

const questions = ref([]);
const category = ref(0);
const answer = ref('');
const newCategoryName = ref('');
const categories = ref([]);
const categoryQuestions = ref([]);

const modal = ref(null);
const router = useRouter();

onMounted(async () => {
  fetchQuestions();

  const $targetEl = document.getElementById(`modal`);

  const options = {
    placement: "center",
    backdrop: "dynamic",
    backdropClasses: "bg-gray-900 bg-opacity-50 fixed inset-0 z-40",
    closable: true,
  };

  modal.value = new Modal($targetEl, options);
});

const fetchQuestions = async () => {
  const { data } = await shopApi.get("/api/faqs/get");
  questions.value = data;
  categories.value = Object.keys(data);

  ({ data: categories.value } = await shopApi.get("/api/faqs/categories"));

  Object.values(data).forEach((k, v) => {
    k.sort((a, b) => {
      if (a.index === null && b.index !== null) {
        return 1; // a should be after b
      } else if (a.index !== null && b.index === null) {
        return -1; // a should be before b
      } else {
        return a.index - b.index;
      }
    });
  });
};

const createCategory = async () => {
  await shopApi.post("/api/faqs", {
    category: newCategoryName.value,
    questions: [{ question: "0", answer: "0", questions: [], withForm: false }],
  });
  modal.value.hide();

  fetchQuestions();
};

const syncQuestionsPositions = () => {
  shopApi.post("/api/faqs/questions-positions", { categories: categoryQuestions.value });
}

const deleteQuestion = async (id) => {
  await shopApi.delete(`/api/faqs/${id}`);

  categoryQuestions.value = '';

  fetchQuestions();
};

const syncCategoriesPositions = () => {
  shopApi.post("/api/faqs/categories-positions", { categories: categories.value });
}

const [parent] = useAutoAnimate();
</script>

<template>
  <div class="w-[70%] mx-auto mt-8">
    <div>
      <h1 class="text-4xl">Prosimy wybrać interesujący państwa temat rozmowy</h1>
    </div>

    <div class="rounded bg-slate-500 p-4 mt-4">
      Wybierz temat kontaktu.
    </div>

    <div class="lg:flex mb-20">
      <div class="lg:w-[15%]">
        <draggable @change="syncCategoriesPositions" v-model="categories" tag="ul">
          <template #item="{ element: c }">
            <button @click="category = c; answer = ''; categoryQuestions = questions[category]"
              class="bg-gray-200 w-full hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-l block mt-6 ">
              {{ c }}
            </button>
          </template>
        </draggable>

        <button class="bg-green-500 rounded text-white px-4 py-2 mt-4" @click="modal?.show">
          Stwórz nową kategorię
        </button>
      </div>
      <div class="m-5" ref="parent">
        <div v-if="answer" class="my-5">
          <p>{{ answer }}</p>
        </div>
        <draggable v-model="categoryQuestions" tag="ul" @change="syncQuestionsPositions">
          <template #item="{ element: question }" @click="answer = question.answer">
            <div
              class="pointer bg-gray-200 w-full hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-l block mt-6">
              <h2 class="text-lg">{{ question.question }}</h2>

              <button class="bg-red-500 rounded text-white px-4 py-2 mt-2" @click.prevent="deleteQuestion(question.id)">
                Usuń
              </button>

              <a class="bg-blue-500 rounded text-white px-4 py-2 mt-2 ml-4"
                :href="`${config.nuxtNewFront}faq/create/${question.id}`" target="_blank">
                Edytuj
              </a>
            </div>
          </template>
        </draggable>

        <nuxt-link href="/faq/create-question" class="bg-green-500 rounded text-white px-4 py-2 mt-6">Nowe
          pytanie</nuxt-link>
      </div>
    </div>
  </div>


  <div :id="`modal`" tabindex="-1"
    class="top-0 fixed z-50 w-auto hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-full h-full max-w-xl sm:max-w-3xl md:max-w-5xl lg:max-w-7xl md:h-auto">
      <!-- Modal content -->
      <div class="relative bg-white rounded-lg shadow">
        <!-- Modal header -->
        <div class="flex items-start justify-between p-4 border-b rounded-t">
          <h3 class="text-xl font-semibold text-gray-900">
            Dodaj nową kategorię
          </h3>
          <button type="button"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
            data-modal-hide="modal" @click="modal?.hide">
            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd"></path>
            </svg>
            <span class="sr-only">Zamknij modal</span>
          </button>
        </div>
        <!-- Modal body -->
        <div class="p-6 space-y-6 w-auto">
          <!-- form input form name -->
          <div class="flex flex-col">
            <label for="name" class="text-sm font-medium text-gray-700">
              Nazwa kategorii
            </label>
            <input class="border border-gray-200 mt-4 h-12 rounded p-1" v-model="newCategoryName" />
          </div>
        </div>
        <!-- Modal footer -->
        <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b">
          <button type="button" @click="async () => await createCategory()"
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
            Zapisz
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
