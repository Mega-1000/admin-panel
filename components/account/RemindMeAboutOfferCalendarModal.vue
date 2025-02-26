<script setup>
  import { Modal } from "flowbite";

  const props = defineProps({
    offerId: {
      type: Number,
      required: true,
    },
  });

  const modal = ref();
  const selectedDateTime = ref(null);
  const { $shopApi: shopApi } = useNuxtApp();

  onMounted(() => {
    const $targetEl = document.getElementById(`modal`);

    const options = {
      backdrop: "dynamic",
      backdropClasses: "bg-gray-900 bg-opacity-50 fixed inset-0 z-40",
      closable: true,
    };

    modal.value = new Modal($targetEl, options);
  })

  const sendReminderData = async () => {
    await shopApi.post(`/api/orders/remind-about-offer/${props.offerId}`, {
      dateTime: selectedDateTime.value.replace("T", " "),
    })

    window.location.reload();
  }
</script>

<template>
  <button @click="modal?.show()" class="bg-orange-500 px-4 py-2 rounded text-white">
    Przypomnij mi o tej ofercie w dniu
  </button>


  <div id="modal" tabindex="-1"
       class="top-0 fixed z-50 w-auto hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-full h-full max-w-xl sm:max-w-3xl md:max-w-5xl lg:max-w-7xl md:h-auto">
      <!-- Modal content -->
      <div class="relative bg-white rounded-lg shadow">
        <!-- Modal header -->
        <div class="flex items-start justify-between p-4 border-b rounded-t">
          <h3 class="text-xl font-semibold text-gray-900">
            Wybierz datę
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
          <form @submit.prevent="sendReminderData">
            <label for="datetime-input" class="block text-sm font-medium text-gray-700">Wybierz dzień i godzinę:</label>
            <div class="mt-1">
              <input type="datetime-local" id="datetime-input" v-model="selectedDateTime" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
            </div>

            <submitButton class="mt-8" :disabled="selectedDateTime === null">
              Przypomnij mi o tej ofercie w dniu {{ selectedDateTime }}
            </submitButton>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
