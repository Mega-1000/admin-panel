<script setup>
import { Modal } from "flowbite";

const props = defineProps({
  'addressType': {
    type: String,
    required: true
  }
})

const { $shopApi } = useNuxtApp();

const modal = ref(null);

const addresses = ref([]);

const emit = defineEmits(["selectAddress"]);

onMounted(async () => {
  const response = await $shopApi.get('/api/user/orders');
  addresses.value = response.data.filter((address) => address.adress.type === props.addressType);

  const $targetEl = document.getElementById('modal');

  const options = {
    backdrop: "dynamic",
    backdropClasses: "bg-gray-900 bg-opacity-50 fixed inset-0 z-40",
    closable: true,
  };

  modal.value = new Modal($targetEl, options);
});

const submitOption = (id) => {
  const address = addresses.value.find((address) => address.id === id);

  if (address) {
    address.type = props.addressType;
    emit("selectAddress", { addresses: address });
    modal.value.hide();
  }
};
</script>

<template>
  <SubmitButton @click="showModal = true">
    Wyświetl dane z kilku ostatnich zamówień
  </SubmitButton>

  <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-x-hidden overflow-y-auto bg-gray-900 bg-opacity-50"
       v-if="showModal">
    <div class="relative w-full max-w-xl sm:max-w-3xl md:max-w-5xl lg:max-w-7xl h-auto">
      <div class="relative bg-white rounded-lg shadow">
        <div class="flex items-start justify-between p-4 border-b rounded-t">
          <h3 class="text-xl font-semibold text-gray-900">
            Wybierz adres
          </h3>
          <button type="button"
                  class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
                  @click="showModal = false">
            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                 xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd"></path>
            </svg>
            <span class="sr-only">Zamknij modal</span>
          </button>
        </div>
        <div class="p-4 overflow-x-auto">
          <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
              <th scope="col" class="px-6 py-3">
                Numer oferty
              </th>
              <th scope="col" class="px-6 py-3">
                Imię
              </th>
              <th scope="col" class="px-6 py-3">
                Nazwisko
              </th>
              <th scope="col" class="px-6 py-3">
                Ulica/wioska
              </th>
              <th scope="col" class="px-6 py-3">
                Miasto
              </th>
              <th scope="col" class="px-6 py-3">
                Kod pocztowy
              </th>
              <th scope="col" class="px-6 py-3">
                email
              </th>
              <th scope="col" class="px-6 py-3">
                telefon
              </th>
              <th scope="col" class="px-6 py-3">
                akcje
              </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="address in addresses" class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
              <td class="px-6 py-4">
                {{ address.adress?.order_id }}
              </td>
              <td class="px-6 py-4">
                {{ address.adress?.firstname }}
              </td>
              <td class="px-6 py-4">
                {{ address.adress?.lastname }}
              </td>
              <td class="px-6 py-4">
                {{ address.adress?.address }}
              </td>
              <td class="px-6 py-4">
                {{ address.adress?.city }}
              </td>
              <td class="px-6 py-4">
                {{ address?.adress?.postal_code }}
              </td>
              <td class="px-6 py-4">
                {{ address.adress?.email }}
              </td>
              <td class="px-6 py-4">
                {{ address.adress?.phone }}
              </td>
              <td class="px-6 py-4">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                        @click="submitOption(address.id)">
                  Wybierz
                </button>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
