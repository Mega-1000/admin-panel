<template>
  <div class="w-full sm:w-[90%] md:w-[80%] lg:w-[70%] mx-auto my-8">
    <div class="bg-gradient-to-br from-red-500 to-pink-600 rounded-2xl shadow-2xl overflow-hidden">
      <div class="p-8 sm:p-10">
        <!-- <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6 leading-tight">
          Nie wiesz ile paczek potrzebujesz?
        </h2>
        <p class="text-xl text-white mb-8 opacity-90">
          Skorzystaj z naszego kalkulatora styropianu i oblicz dokładną ilość potrzebnego materiału.
        </p>
        <button
            @click="openCalculator"
            class="group flex items-center space-x-3 bg-white text-red-600 hover:bg-red-100 transition-colors duration-300 font-semibold py-3 px-6 rounded-full shadow-lg hover:shadow-xl"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 group-hover:animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
          </svg>

          <span @click="showCalculator = true">Otwórz Kalkulator</span>
        </button> -->
      </div> 
    </div>
  </div>

  <div class="w-full sm:w-[90%] md:w-[80%] lg:w-[70%] mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6 mt-12">
      <div ref="parent" class="space-y-4">
        <div v-for="(selection, index) in selections" :key="index" class="flex flex-col sm:flex-row items-center gap-2">
          <div class="flex flex-col w-full sm:w-1/3">
            <StyroTypeModal v-model="selection.value" />
          </div>

          <TextInput type="number"  @input="selection.quantity = $event" :value="selection.quantity" label="Podaj ilość paczek" class="w-full sm:w-1/3" />

          <div class="w-[100%] md:w-[40%]">
            <label class="block text-gray-700 text-sm font-bold mb-2">
              Grubość styropianu
            </label>
            <select v-model="selection.thickness" class="px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
              <option v-for="n in 26" :key="n-1" :value="n-1">{{ n-1 }}</option>
            </select>
          </div>

          <SubmitButton @click="showQuotes(selection)" :disabled="loading || !selection.value" class="w-full sm:w-1/3 md:mt-7">
            <span v-if="!loading">Pokaż aktualne ceny podstawowe</span>
            <span v-else>Ładowanie...</span>
          </SubmitButton>

          <button @click="deleteSelection(index)" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 transition-colors duration-300 md:mt-7" :disabled="loading">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
      </div>

      <button @click="addSelection" class="bg-green-500 text-white font-bold py-2 px-4 rounded mt-8">
        Dodaj kolejny produkt
      </button>

      <div v-if="hasMultipleSelections" class="mt-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
        <p class="font-bold">Uwaga:</p>
        <p>Dodawaj więcej niż jeden produkt tylko jeśli jesteś pewien, że potrzebujesz różnych typów styropianu. W przeciwnym razie firmy mogą uznać, że Twoje zamówienie jest większe niż w rzeczywistości.</p>
      </div>

      <!-- Calculator Modal -->
      <div v-if="showCalculator" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
          <h3 class="text-2xl font-bold mb-4">Kalkulator styropianu</h3>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Grubość styropianu (cm)</label>
              <select v-model="calculator.thickness" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option v-for="n in 30" :key="n" :value="n">{{ n }} cm</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Wykończenie krawędzi</label>
              <select v-model="calculator.edgeFinish" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="proste">Proste</option>
                <option value="frez">Frez</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Powierzchnia do ocieplenia (m²)</label>
              <input v-model="calculator.area" type="number" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="bg-gray-100 p-4 rounded-lg">
              <p class="font-medium">Wyniki:</p>
              <p>Objętość: <strong>{{ (typeof calculatedVolume === 'number' ? calculatedVolume.toFixed(2) : '0.00') }} m³</strong></p>
              <p>Powierzchnia: <strong>{{ (typeof calculatedArea === 'number' ? calculatedArea.toFixed(2) : '0.00') }} m²</strong></p>
              <p>Ilość opakowań: <strong>{{ Math.ceil(calculatedPackages || 0) }} op.</strong></p>
            </div>
          </div>
          <div class="mt-6 flex justify-between">
            <button @click="applyCalculatorResults" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg transition-colors">
              Zastosuj wyniki
            </button>
            <button @click="showCalculator = false" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition-colors">
              Zamknij
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-6">
      <SubmitButton @click="saveAuction" :disabled="loading" class="bg-gradient-to-r from-green-400 to-green-600 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 w-full sm:w-auto">
        <span v-if="!loading">Stwórz przetarg + darmowy poradnik</span>
        <span v-else>Ładowanie...</span>
      </SubmitButton>
    </div>

<!--    <HowAuctionsWork />-->


    <OpinionStars class="mt-3 text-black" />
    <div class="mt-12">
      <span class="font-bold text-lg">
        Dostaniesz wyceny między innmi od:
      </span>
      <section>
        <div class="mx-auto py-10">
          <NuxtLink class="md:grid flex md:gap-8 justify-between text-gray-400 grid-cols-12 gap-5 mx-1" href="/styropiany">
            <div class="flex justify-center items-center hover:scale-105 transition-transform duration-300 hidden md:flex">
              <img src="/genderka.webp" alt="Genderka - Lider rynku" class="md:w-[70%]">
            </div>
            <div href="#" class="flex justify-center items-center hover:scale-105 transition-transform duration-300 hidden md:flex">
              <img src="/swisspor.webp" alt="Swisspor - Szwajcarska precyzja" class="md:w-[70%]">
            </div>
            <div href="#" class="flex justify-center items-center hover:scale-105 transition-transform duration-300">
              <img src="/images (13).jpeg" alt="Termoorganika - Naturalnie ciepły dom" class="md:w-[70%]">
            </div>
            <div href="#" class="flex justify-center items-center hover:scale-105 transition-transform duration-300">
              <img src="/arsanit.webp" alt="Arsanit - Komfort na lata" class="md:w-[70%]">
            </div>
            <div href="#" class="flex justify-center items-center hover:scale-105 transition-transform duration-300">
              <img src="/austroterm.webp" alt="Austroterm - Najwyższa jakość" class="md:w-[70%]">
            </div>
            <div href="#" class="flex justify-center items-center hover:scale-105 transition-transform duration-300">
              <img src="/yetico.webp" alt="Yetico - Energia oszczędności" class="md:w-[70%]">
            </div>
            <div href="#" class="flex justify-center items-center hover:scale-105 transition-transform duration-300">
              <img src="/images (4).png" alt="Ciepły dom to szczęśliwy dom" class="md:w-[70%]">
            </div>
            <div href="#" class="flex justify-center items-center hover:scale-105 transition-transform duration-300">
              <img src="/unnamed.png" alt="Zaufana marka, komfortowa izolacja" class="md:w-[70%]">
            </div>
            <div href="#" class="flex justify-center items-center hover:scale-105 transition-transform duration-300">
              <img src="/knauf.png" alt="Knauf - Eksperci izolacji" class="md:w-[70%]">
            </div>
            <div href="#" class="flex justify-center items-center hover:scale-105 transition-transform duration-300 hidden md:flex">
              <img src="/polstyr_logo_without_background.png" alt="Polstyr - Polska jakość" class="md:w-[70%]">
            </div>
            <div href="#" class="flex justify-center items-center hover:scale-105 transition-transform duration-300 hidden md:flex">
              <img src="/images.png" alt="Polstyr - Polska jakość" class="md:w-[70%]">
            </div>
            <div href="#" class="flex justify-center items-center hover:scale-105 transition-transform duration-300 hidden md:flex">
              <img src="/images%20(14).jpeg" alt="Polstyr - Polska jakość" class="md:w-[70%]">
            </div>
          </NuxtLink>
        </div>
      </section>
    </div>

    <!-- Modals -->
    <div class="modal-backdrop" v-if="modalData">
      <div class="modal-content">
        <div class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full h-full" style="background-color: rgba(0, 0, 0, 0.50)">
          <div class="relative p-4 w-full max-w-2xl max-h-full mx-auto">
            <div class="relative bg-white rounded-lg shadow">
              <div class="flex items-center justify-between p-4 border-b rounded-t">
                <h3 class="text-xl font-semibold text-gray-900">
                  Wycena dostępnych firm dla tego styropianu
                </h3>

                <button type="button" @click="modalData = false" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                  <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                  </svg>
                  <span class="sr-only">Close modal</span>
                </button>
              </div>

              <h3 class="mx-4 mt-4 text-red-500 font-bold">
                !! Aby stworzyć przetarg zamknij to okienko i kliknij przycisk stwórz przetarg !!
              </h3>

              <div class="p-4 space-y-4">
                <table class="w-full">
                  <thead>
                  <tr class="bg-gray-100">
                    <th class="py-2 px-4 font-semibold text-left">Producent</th>
                    <th class="py-2 px-4 font-semibold text-left">Cena jednostkowa netto</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-for="item in modalData" :key="item.id" class="border-b">
                    <td class="py-2 px-4">{{ item.manufacturer }}</td>
                    <td class="py-2 px-4">{{ item.price.net_purchase_price_basic_unit }} PLN</td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
    >
      <div v-if="showUserInfoModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
          <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
          <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                  <h3 class="text-2xl leading-6 font-bold text-gray-900 mb-6" id="modal-title">
                    Powiedz nam trochę o sobie...
                  </h3>
                  <div class="mt-4 space-y-6">
                    <TextInput
                        @input="userInfo.email = $event"
                        label="Email"
                        placeholder="jan.kowalski@example.com"
                        type="email"
                    />
                    <TextInput
                        @input="userInfo.phone = $event"
                        label="Numer telefonu"
                        placeholder="123 456 789"
                        type="tel"
                    />
                    <TextInput
                        @input="userInfo.zipCode = $event"
                        :value="defaultZipCode"
                        label="Kod pocztowy"
                        placeholder="00-000"
                    />
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <SubmitButton
                  @click="confirmAuction"
                  :disabled="loading"
                  class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm"
              >
                <span v-if="!loading">Zatwierdź i otrzymaj wyceny na maila</span>
                <span v-else class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Ładowanie...
              </span>
              </SubmitButton>
              <button
                  @click="showUserInfoModal = false"
                  type="button"
                  class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              >
                Anuluj
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
    <div v-if="loading" class="fixed top-0 left-0 w-screen h-screen flex justify-center items-center bg-gray-500 bg-opacity-50">
      <Loader :showLoader="loading" />
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed, onBeforeUnmount } from 'vue';
import { useAutoAnimate } from '@formkit/auto-animate/vue';
import SubmitButton from "../components/SubmitButton.vue";
import Swal from "sweetalert2";
import Cookies from "universal-cookie/cjs/Cookies";
import StyroTypeModal from "~/components/StyroTypeModal.vue";
import {trackEvent} from "~/utils/trackEvent";

const { $shopApi: shopApi } = useNuxtApp();

const styrofoamTypes = ref([]);
const selections = reactive([{ value: null, quantity: '', thickness: '' }]);
const modalData = ref(false);
const userInfo = ref({ email: '', phone: '', zipCode: '' });
const showUserInfoModal = ref(false);
const loading = ref(false);
const defaultZipCode = ref('');
const router = useRouter();

const [parent] = useAutoAnimate();

const hasMultipleSelections = computed(() => selections.length > 1);

const showCalculator = ref(false);

const calculator = reactive({
  styrofoamType: null,
  thickness: 1,
  edgeFinish: 'proste',
  area: 0,
});

const calculatedVolume = computed(() => {
  return (calculator.area * calculator.thickness) / 100;
});

const calculatedArea = computed(() => {
  return calculator.area;
});

const calculatedPackages = computed(() => {
  return calculatedVolume.value / 0.3;
});

onMounted(async () => {
  defaultZipCode.value = localStorage.getItem('zipCode');
  userInfo.value.zipCode = defaultZipCode.value;
  try {
    loading.value = true;
    const types = await shopApi.get('/auctions/get-styrofoam-types');
    styrofoamTypes.value = types.data;
  } finally {
    loading.value = false;
  }

  window.addEventListener('beforeunload', handlePageUnload);
});

onBeforeUnmount(() => {
  window.removeEventListener('beforeunload', handlePageUnload);
});

const handlePageUnload = (event) => {
  event.preventDefault();
  event.returnValue = '';
};

const addSelection = () => {
  if (selections.length < 5) {
    selections.push({ value: null, quantity: '', thickness: '' });
  }
};

const saveAuction = async () => {
  try {
    const auctionData = selections.filter(selection => selection.value !== null && selection.quantity !== '').map(selection => ({
      styrofoamType: selection.value,
      quantity: parseInt(selection.quantity, 10),
      thickness: selection.thickness
    }));

    if (auctionData.length === 0) {
      Swal.fire('Musisz dodać ilość styropianu', '', 'info');
      return;
    }

    const totalQuantity = auctionData.reduce((sum, item) => sum + item.quantity, 0);
    if (totalQuantity < 66) {
      Swal.fire('Ilość końcowa musi być większa niż 66 paczek', 'Jeśli potrzebujesz ilości mniejszej niż 66 paczek musisz odebrać zamówienie osobiście! W takim przypadku stwórz zamówienie przez sklep.', 'info');
      return;
    }

    showUserInfoModal.value = true;
  } catch (error) {
    console.error('Error saving auction:', error);
    Swal.fire('Wystąpił błąd po naszej stronie, prosimy o kontakt pod numer 691 801 594.', '', 'error');
  }
};

const confirmAuction = async () => {
  try {
    loading.value = true;
    const auctionData = selections.filter(selection => selection.value !== null && selection.quantity !== '').map(selection => ({
      styrofoamType: selection.value,
      quantity: selection.quantity,
      thickness: selection.thickness
    }));
    showUserInfoModal.value = false;

    const res = await shopApi.post('/api/auctions/save', { auctionData, userInfo: userInfo.value });
    const cookies = new Cookies();
    await cookies.set("token", res.data.access_token);

    window.dispatchEvent(new CustomEvent('token-refreshed'));

    await trackEvent('conversion_event_request_quote', 'styropian', 'Stworzenie przetargu', 5);
    await router.push('/przetarg-zostal-stworzony?email=' + userInfo.value.email + '&chatToken=' + res.data.access_token + '&orderId=' + res.data.id);

    selections.length = 0;
  } catch (error) {
    if (error.response.status === 400) {
      window.location.href = error.response.data.error;
    } else {
      console.error('Error saving auction:', error);
      alert('Wystąpił błąd podczas zapisywania przetargu. Proszę spróbować ponownie.');
    }

  } finally {
    loading.value = false;
  }
};

const deleteSelection = (index) => {
  selections.splice(index, 1);
};

const updateSelection = (index, newValue) => {
  if (index === selections.length - 1 && selections.length < 5) {
    // addSelection();
  }
};

const showQuotes = async (selection) => {
  const zipCode = localStorage.getItem('zipCode');

  try {
    loading.value = true;
    const { data: request } = await shopApi.get(`/auctions/get-quotes-by-styrofoarm-type/${selection.value}?zipCode=${zipCode}`);
    modalData.value = Object.values(request).sort((a, b) => {
      return a.price.net_purchase_price_basic_unit - b.price.net_purchase_price_basic_unit;
    });

  } catch (error) {
    console.error('Error fetching quotes:', error);
  } finally {
    loading.value = false;
  }
};

const applyCalculatorResults = () => {
  const calculatedQuantity = Math.ceil(calculatedPackages.value);

  // Find or create a selection with the calculated styrofoam type
  let selection = selections.find(s => s.value === calculator.styrofoamType);
  if (!selection) {
    selection = { value: calculator.styrofoamType, quantity: '', thickness: '' };
    selections.push(selection);
  }

  // Update the selection with calculated values
  selection.quantity = calculatedQuantity.toString();
  selection.thickness = calculator.thickness.toString();

  showCalculator.value = false;
};
</script>

<style>
em {
  background: -webkit-gradient(linear, left top, left bottom, color-stop(15%, #c1f99d), color-stop(94%, #e0f5d3));
  background: linear-gradient(-180deg, #c1f99d 15%, #e0f5d3 94%);
  padding: 2px;
  font-style: normal;
  color: #343a40;
  border-radius: 4px;
  overflow: hidden;
}
</style>
