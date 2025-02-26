<script setup>
const emit = defineEmits(["submit", "cancel"]);

const cancel = () => {
  emit("cancel");
};

const finishRegistration = () => {
  emit("submit");
};
</script>

<template>
  <div
      class="w-screen max-w-sm md:max-w-md lg:max-w-2xl xl:max-w-4xl p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-6 md:p-8"
  >
    Nie istnieje takie konto. Jeśli chcesz stworzyć nowe wypełnij
    <div>
      <div class="mt-4">
        <form @submit.prevent="finishRegistration">
          <div class="flex items-start">
            <div class="flex items-center h-5">
              <input id="rules" type="checkbox" required
                     class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300" />
            </div>
            <label for="rules" class="ml-2 text-sm font-medium text-gray-900">Zapoznałem się z regulaminem 1</label>
          </div>
          <div class="flex items-start">
            <div class="flex items-center h-5">
              <input id="rules-2" type="checkbox" required
                     class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300" />
            </div>
            <label for="rules-2" class="ml-2 text-sm font-medium text-gray-900">Zapoznałem się z regulaminem 2</label>
          </div>
          <div class="flex items-start">
            <div class="flex items-center h-5">
              <input id="rules-3" type="checkbox" required
                     class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300" />
            </div>
            <label for="rules-3" class="ml-2 text-sm font-medium text-gray-900">Zapoznałem się z regulaminem 2</label>
          </div>
          <SubmitButton class="w-full mt-4">Stwórz</SubmitButton>
        </form>
      </div>
      <div class="mt-8">
        <CancelButton class="w-full" @click="cancel">Anuluj</CancelButton>
      </div>
    </div>
  </div>
</template>
