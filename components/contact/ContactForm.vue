<script setup lang="ts">
const router = useRouter();

const cart = useCart();
const user = useUser();

const loading = useState(() => false);
const errorMessage = useState(() => "");

let files: any[] = [];

let emailInput = user?.value?.email || "";
let postalCodeInput = user?.value?.postal_code || "";
let cityInput = user?.value?.city || "";
let additionalNoticesInput = "";
let abroadInput = false;
let rulesInput = false;

const emit = defineEmits(["submit"]);

const areFilesValid = (files: any[]) => {
  const availableFileExtensions = ["png", "jpg", "jpeg", "pdf", "tif", "gif"];

  for (let i = 0; i < files.length; i++) {
    const ext = files[i].name.substring(files[i].name.lastIndexOf(".") + 1);
    if (!availableFileExtensions.includes(ext)) return false;
  }

  return true;
};

const handleSubmit = async (e: Event) => {
  e.preventDefault();
  loading.value = true;

  if (files.length > 0 && !areFilesValid(files)) {
    errorMessage.value = "Nieprawidłowe pliki";
    loading.value = false;
    return;
  }

  const params = {
    customer_login: emailInput,
    phone: user.value.phone,
    customer_notices: additionalNoticesInput,
    delivery_address: {
      city: cityInput,
      postal_code: postalCodeInput,
    },
    shipping_abroad: abroadInput,
    is_standard: true,
    files,
    cart_token: cart.value,
    rewrite: 1,
  };


  loading.value = false;
  emit("submit");
  await router.push({
    path: "/contact/confirmation",
    query: {
      params: JSON.stringify(params),
    },
  });
};
</script>

<template>
  <div
    class="w-full max-w-sm md:max-w-md lg:max-w-2xl xl:max-w-4xl p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-6 md:p-8">
    <form class="space-y-6" @submit="handleSubmit">
      <h5 class="text-xl xl:text-2xl font-medium text-gray-900">
        prosimy podać email kontaktowy
      </h5>
      <div>
        <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
        <input type="email" name="email" id="email"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
          required :disabled="loading" v-model="emailInput" />
      </div>
      <div class="flex items-start">
        <div class="flex items-center h-5">
          <input id="rules" type="checkbox" required v-model="rulesInput"
            class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300" />
        </div>
        <label for="rules" class="ml-2 text-sm font-medium text-gray-900">Zapoznałem się z <nuxt-link class="text-blue" href="https://mega1000.pl/custom/5">regulaminem</nuxt-link></label>
      </div>
      <primaryButton :disabled="loading">Wyślij</primaryButton>
    </form>
  </div>
</template>
