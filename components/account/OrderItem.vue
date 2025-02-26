<script setup lang="ts">
import { Modal, ModalOptions } from "flowbite";
import { dowloadInvoices } from "~/helpers/invoices";
import Swal from "sweetalert2"

interface Props {
  item: any;
}

const productsCart = useProductsCart();
const router = useRouter();
const props = defineProps<Props>();
const emit = defineEmits(["refresh"]);
const isVisiblitityLimited = ref(false);
const defaultError = "Wystąpil błąd. Spróbuj ponownie później";
const route = useRoute();

const editCart = (items: any[]) => {
  productsCart.value.removeAllFromCart();

  items.map((product) => {
    const prodPacking = product.product.packing;
    const prodPrice = product.product.price;

    delete product.product.packing;
    delete product.product.price;

    const prodMain = product.product;
    delete product.product;

    const productFinal = {
      ...product,
      ...prodMain,
      ...prodPacking,
      ...prodPrice,
    };
    productsCart.value.addToCart(productFinal, productFinal.quantity);
  });

  router.push("/koszyk.html?isEdition=true");
};

const config = useRuntimeConfig().public;

const { $shopApi: shopApi } = useNuxtApp();

const downloadInvoice = async (invoice: any) => {
  const response = await shopApi.get("api/invoices/get/" + invoice?.id, {
    responseType: "blob",
  });
  const url = window.URL.createObjectURL(new Blob([response.data]));
  const link = document.createElement("a");
  link.href = url;
  link.setAttribute("download", invoice.gt_invoice_number + ".pdf");
  document.body.appendChild(link);
  link.click();
  link.remove();
};

const proofUploaded = ref(false);

const modal = ref<Modal | null>(null);

onMounted(() => {
  isVisiblitityLimited.value = localStorage.getItem("allegroVisibilityLimit") === "true";
  const $targetEl = document.getElementById(`modal-${props.item.id}`);

  // options with default values
  const options: ModalOptions = {
    placement: "center",
    backdrop: "dynamic",
    backdropClasses: "bg-gray-900 bg-opacity-50 fixed inset-0 z-40",
    closable: true,
  };

  modal.value = new Modal($targetEl, options);

  if (route.query.attachtransferconfirmation == props.item.id) {
    modal.value.show();
  }
});

const onFileChange = (e: any) => {
  const files = e.target.files || e.dataTransfer.files;

  if (!files.length) return;

  proofUploaded.value = files;
}

const handleUploadProofOfPayment = async () => {
  const file = proofUploaded.value[0];
  const formData = new FormData();

  if (!file) return false;

  formData.append("id", props.item.id);
  formData.append("file", file);

  try {
    await shopApi.post("api/orders/uploadProofOfPayment", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    modal.value?.hide();
    proofUploaded.value = true;

    if (
      !props.item.dates?.customer_shipment_date_from ||
      !props.item.dates?.customer_shipment_date_to
    )
      window.location.assign(
        `${config.nuxtNewFront}zamowienie/mozliwe-do-realizacji/brak-danych/${props.item.id}`
      );
  } catch (err: any) {
    modal.value?.hide();

    if (err.response && err.response.data) {
      alert(err.response.data.errorMessage ?? defaultError);
    } else {
      alert(defaultError);
    }
  }
};

const markOfferAsInactive = async () => {
  Swal.fire({
    title: "Czy jesteś pewien?",
    text: "Oferta zostanie przeniesiona do nie aktywnych.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Tak",
    cancelButtonText: "Anulluj"
  }).then(async (result) => {
    if (result.isConfirmed) {
      await shopApi.post(`api/orders/move-to-unactive/${props.item.id}`)

      await Swal.fire({
        title: "Przeniesiono oferę do nieaktywnych!",
        text: "",
        icon: "success"
      });

      router.go(0);
    }
  });

};
</script>

<template>
  <div class="relative bg-white rounded-xl shadow-lg p-6 max-w-7xl mx-auto border border-gray-200">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="space-y-2">
        <p class="text-sm font-medium text-gray-500">Nr oferty:</p>
        <p class="text-lg font-semibold">{{ item.id }}</p>
        <p v-if="item.master_order_id" class="text-sm font-medium text-gray-500">Nr zamówienia głównego:</p>
        <p v-if="item.master_order_id" class="text-lg font-semibold">{{ item.master_order_id }}</p>
        <p class="text-sm font-medium text-gray-500">Data stworzenia:</p>
        <p class="text-lg font-semibold">{{ item.created_at.replace('T', ' ').split('.')[0] }}</p>
        <p class="text-sm font-medium text-gray-500">Status:</p>
        <p class="text-lg font-semibold">{{ item.status.name }}</p>
      </div>
      <div class="space-y-2">
        <p v-if="item.employee" class="text-sm font-medium text-gray-500">Osoba obsługująca:</p>
        <p v-if="item.employee" class="text-lg font-semibold">{{ item.employee.name }}</p>
        <p v-if="item.employee" class="text-sm font-medium text-gray-500">Tel. kontaktowy:</p>
        <p v-if="item.employee" class="text-lg font-semibold">{{ item.employee.phone }}</p>
        <p class="text-sm font-medium text-gray-500">Wartość brutto zamówienia:</p>
        <p class="text-lg font-semibold">{{ item.total_sum.toFixed(2) }} PLN</p>
        <p class="text-sm font-medium text-gray-500">Wpłacono:</p>
        <p class="text-lg font-semibold">{{ item.bookedPaymentsSum }} PLN</p>
      </div>
      <div v-if="item.reminder_date" class="space-y-2">
        <p class="text-sm font-medium text-gray-500">Data przypomnienia:</p>
        <p class="text-lg font-semibold">{{ item.reminder_date }}</p>
      </div>
    </div>

    <div v-if="item.auctionCanBeCreated" class="mt-8 bg-green-100 p-4 rounded-lg">
      <p class="text-gray-700">To jest zapytanie na styropian!</p>
      <p class="text-gray-700">Możesz stworzyć przetarg a my zapytamy firmy obsługujące twój kod pocztowy o indywidualną wycenę!</p>
      <a :href="`https://admin.mega1000.pl/auctions/${item.chat.id}/create`" target="_blank" class="mt-4 inline-block bg-green-600 text-white rounded-md px-4 py-2 hover:bg-green-700 transition-colors duration-300">
        Rozpocznij przetarg
      </a>
    </div>

    <div v-if="item.isAuctionCreated" class="mt-8 bg-blue-100 p-4 rounded-lg">
      <p class="text-gray-700" v-if="item?.chat?.auctions[0]?.end_of_auction < Date.now()">
        Przetarg na tę zapytanie został zakończony!
      </p>
      <p v-else>
        Na to zapytanie jest aktywny przetarg!
      </p>
      <p class="text-gray-700">Jeśli chcesz zmienić jego szczegóły kliknij przycisk przejdz do chatu. W tym module jest również możliwość zmiany dat logistycznych.</p>
      <a
          :href="`${config.baseUrl}/auctions/${item.auctionId}/end`"
          target="_blank"
          class="mt-4 inline-block bg-blue-600 text-white rounded-md px-4 py-2 hover:bg-blue-700 transition-colors duration-300"
      >
        Przejdź do tabeli wycen
      </a>

      <a
          :href="`${config.baseUrl}/chat-show-or-new/${item.id}/${item.customer_id}`"
          target="_blank"
          class="ml-4 inline-block bg-yellow-600 text-white rounded-md px-4 py-2 hover:bg-blue-700 transition-colors duration-300"
      >
        Zmień daty logistyczne
      </a>
    </div>

    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
      <a :href="`${config.baseUrl}/auctions/${item?.chat?.auctions[0]?.id}/end`" v-if="item?.chat?.auctions[0]?.id" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-300">
        Zobacz wyniki przetargu
      </a>

      <button v-if="!proofUploaded && (!item?.files || item.files.length === 0) && !isVisiblitityLimited" @click="modal?.show" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-300">
        Podłącz potwierdzenie przelewu
      </button>

      <button v-else-if="!isVisiblitityLimited" disabled class="bg-green-500 text-white font-semibold py-2 px-4 rounded-md">
        Potwierdzenie przelewu podłączone
      </button>

      <a v-if="!(item?.chat?.auctions[0]?.end_of_auction < Date.now())" :href="`${config.baseUrl}/order-proform-pdf/${item.order_offers[0]?.id}`" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-300">
        Faktura proforma
      </a>

<!--      <a v-if="!isVisiblitityLimited && !(item?.chat?.auctions[0]?.end_of_auction < Date.now())" target="_blank" :href="`${config.baseUrl}/order-offer-pdf/${item.order_offers[0]?.id}`" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-300">-->
<!--        Opis oferty-->
<!--      </a>-->

      <accountActionButton type="button" @click="editCart(item.items)" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-300">
        Edytuj zamówienie
      </accountActionButton>

      <accountActionButton target="_blank" type="link" :href="`${config.nuxtNewFront}zamowienie/mozliwe-do-realizacji/brak-danych/${item.id}`" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-300">
        Dane do dostawy i faktury
      </accountActionButton>

      <button v-for="invoice in item?.user_invoices" @click="() => downloadInvoice(invoice)" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-md transition-colors duration-300">
        Faktura: {{ invoice.gt_invoice_number }}
      </button>

      <accountActionButton type="link" target="_blank" :href="`${config.baseUrl}/chat-show-or-new/${item.id}/${item.customer_id}`" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-300" :class="item.isThereUnansweredChat ? 'bg-red-600' : ''">
        Dyskusja i zarządzanie datami
        <br>
        <span class="font-bold">{{ item.isThereUnansweredChat ? 'Masz nową wiadomość!' : ''}}</span>
      </accountActionButton>

      <accountActionButton type="link" :href="`/Complaint?offerId=${item.id}`" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-300">
        Zgłoś reklamację
      </accountActionButton>

      <accountActionButton type="button" target="_blank" @click="dowloadInvoices(item.id)" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-300">
        Faktury
      </accountActionButton>

      <accountActionButton type="button" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-300" @click="markOfferAsInactive">
        Przenieś tą ofertę do nieaktywnych
      </accountActionButton>
    </div>

<!--    <div v-for="buttonGroup in Object.keys(item.buttons)" class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">-->
<!--      <div class="flex items-center space-x-2">-->
<!--        <p class="text-sm font-medium text-gray-500">{{ buttonGroup }}</p>-->
<!--        <div class="flex-1 h-px bg-gray-300"></div>-->
<!--      </div>-->
<!--      <div class="col-span-1 md:col-span-2 lg:col-span-2 flex flex-wrap gap-2">-->
<!--        <a v-for="button in (Object.values(item.buttons[buttonGroup]) as any)" target="_blank" :href="button.url" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-md transition-colors duration-300">-->
<!--          {{ button.description }}-->
<!--        </a>-->
<!--      </div>-->
<!--    </div>-->

<!--    <div v-if="!item.reminder_date" class="mt-8 bg-red-100 p-4 rounded-lg">-->
<!--      <p class="text-gray-700">Wskaż datę przypomnienia lub przenieś do ofert nieaktywnych bo w innym przypadku system będzie codziennie wysyłał powiadomienia na twojego emaila.</p>-->
<!--      <RemindMeAboutOfferCalendarModal :offer-id="item.id" />-->
<!--      <button class="mt-4 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-300" @click="markOfferAsInactive">-->
<!--        Przenieś tą ofertę do nieaktywnych-->
<!--      </button>-->
<!--    </div>-->

    <hr class="my-8 border-gray-300" />

    <div class="overflow-x-auto">
      <table class="w-full text-left text-gray-500" v-if="item.packages && item.packages.length > 0">
        <thead class="bg-gray-100 text-xs uppercase text-gray-700">
        <tr>
          <th scope="col" class="px-6 py-3">Nr paczki</th>
          <th scope="col" class="px-6 py-3">Status</th>
          <th scope="col" class="px-6 py-3">Kurier</th>
          <th scope="col" class="px-6 py-3">Nr listu przewozowego</th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b bg-white" v-for="pack in item.packages">
          <td class="px-6 py-4">{{ pack.id }}</td>
          <td class="px-6 py-4">{{ pack.status }}</td>
          <td class="px-6 py-4">{{ pack.delivery_courier_name }}</td>
          <td class="px-6 py-4">{{ pack.letter_number }}</td>
        </tr>
        </tbody>
      </table>

      <p v-else class="text-lg text-gray-700">Brak paczek</p>
    </div>

    <hr class="my-8 border-gray-300" />
  </div>

  <div :id="`modal-${item.id}`" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
    <div class="relative w-full h-full max-w-2xl md:h-auto">
      <div class="relative bg-white rounded-lg shadow">
        <div class="flex items-start justify-between p-4 border-b rounded-t">
          <h3 class="text-xl font-semibold text-gray-900">
            Podłącz potwierdzenie przelewu
          </h3>
          <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" @click="modal?.hide">
            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
            <span class="sr-only">Zamknij</span>
          </button>
        </div>
        <div class="p-6 space-y-6">
          <template v-if="!item.dates?.customer_shipment_date_from || !item.dates?.customer_shipment_date_to || !item.addresses[0].address">
            <div class="flex p-4 bg-red-100 rounded-lg">
              <h5 class="text-lg font-medium text-red-500 mr-4">
                Uwaga brak danych do dostawy bądź faktury
              </h5>
              <a :href="`${config.nuxtNewFront}zamowienie/mozliwe-do-realizacji/brak-danych/${item.id}`" class="text-center p-2 rounded-md bg-red-600 text-white hover:bg-red-700 transition-colors duration-300">
                Uzupełnij dane
              </a>
            </div>
          </template>
          <p class="text-gray-700">
            Uwaga podłączasz potwierdzenie zapłaty do oferty o numerze [{{ item.id }}].
          </p>
          <p class="text-gray-700">
            Sprawdźcie Państwo czy napewno chcieliście realizować tą ofertę a nie inną, ponieważ po tym zatwierdzeniu zostaje ona przekazana do produkcji i w przypadku błędnego wskazania, wszelkie koszty z tym związane będą obciążać Państwa.
          </p>
          <p class="text-gray-700">
            Zalecane aby otworzyć ją i sprawdzić pod względem asortymentowym, ilościowym oraz sprawdzić dane do dostawy, faktury i daty logistyczne.
          </p>

          <p>

          </p>

          <input type="file" @change="onFileChange" accept=".pdf,image/*" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" />
        </div>
        <div class="flex items-center p-6 space-x-2border-t border-gray-200 rounded-b">
          <SubmitButton type="button" @click="handleUploadProofOfPayment" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-300">
            Jestem pewny poprawności oferty, podłączam potwierdzenie przelewu
          </SubmitButton>
        </div>
      </div>
    </div>
  </div>
</template>

