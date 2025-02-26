<script setup>
import { useAutoAnimate } from '@formkit/auto-animate/vue'
import { checkIfUserIsLoggedIn, loginFromGetParams } from "~/helpers/authenticationCheck";

const { $shopApi: shopApi } = useNuxtApp();

const questions = ref([]);
const category = ref('');
const answer = ref('');
const questionsTree = ref([]);
const categories = ref([]);
const showFaq = ref(true);
const route = useRoute();
const router = useRouter();
const questionsState = ref([]);

const categoryQuestions = computed(() => {
  return questions.value[category.value];
});

onMounted(async () => {
  // await loginFromGetParams(false);

  // await router.push({ query: { showFaq: true } });
  // await checkIfUserIsLoggedIn('Aby połączyć się z konsultanmem konieczne jest posiadanie konta, jeśli go nie posiadasz wypełnij pola poniżej to ci je założymy');

  showFaq.value = true;

  const { data } = await shopApi.get("/api/faqs/get");
  questions.value = data;
  categories.value = Object.keys(data);

  ({data: categories.value} = await shopApi.get("/api/faqs/categories"));

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
});

const selectQuestion = async (question) => {
  const { data } = await shopApi.get(`/api/faqs/${question.id}`);

  answer.value = data.questions[0].answer;
  questionsTree.value = data.questions[0].questions;

  questionsState.value = data.questions;
};

const selectQuestionFromTree = (q) => {
  questionsTree.value = q.questions;
  answer.value = q.answer;

  questionsState.value.push(q);
};

const [parent] = useAutoAnimate()
</script>

<template>
  <div v-if="!showFaq" class="w-[70%] mx-auto mt-8 text-lg">
    Jeśli chcesz rozmawiać w temacie już istniejącej oferty wciśnij przycisk "rozpocznij dyskusję" i przeniesiemy cię do twojego konta gdzie wybierzesz numer odpowiedniej oferty.

    <nuxt-link class="px-4 py-2 rounded bg-blue-500 text-white block" href="/account">
      Przenieś mnie na konto i rozpocznij dyskusję
    </nuxt-link>
  </div>
  <div class="w-[70%] mx-auto mt-8" v-else>
    <div>
      <h1 class="text-4xl">Prosimy wybrać interesujący państwa temat rozmowy</h1>
    </div>

    <span v-for="(v, k) in questionsState">
      {{ v.question }} <span v-if="k <= questionsTree.length"> -> </span>
    </span>

    <div class="flex">

      <div class="w-[15%]">
        <button v-for="name in categories" @click="category = name; answer = ''"
                class="bg-gray-200 w-full hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-l block mt-6 ">
          {{ name }}
        </button>
      </div>

      <div ref="parent" class="m-5">

        <div v-if="answer" class="rounded p-4 bg-slate-300 my-5" ref="parent">
          <span>{{ answer }}</span>


          <div v-if="questionsTree.length !== 0">
            <div v-for="q in questionsTree">
              <button @click="selectQuestionFromTree(q)"
                      class="pointer bg-gray-200 w-full hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-l block mt-6">
                <h2 class="text-lg">{{ q.question }}</h2>
              </button>
            </div>
          </div>

          <faqContactForm :questions-tree="questionsState" v-else />
        </div>
        <div v-for="question in categoryQuestions" :key="question.id" @click="selectQuestion(question)">
          <div
              class="pointer bg-gray-200 w-full hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-l block mt-6">
            <h2 class="text-lg">{{ question.question }}</h2>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
