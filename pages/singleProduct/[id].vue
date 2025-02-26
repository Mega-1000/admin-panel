<script setup>
import { Modal, ModalOptions } from "flowbite";
import emitter from "~/helpers/emitter";
import Swal from "sweetalert2";

const router = useRouter();
const item = ref({});
const modal = ref(null);
const route = useRoute();
const { $shopApi: shopApi } = useNuxtApp();
const productsCart = useProductsCart();
const productAmount = useProductAmount();
const currentItem = useCurrentItem();
const isModalShown = ref(false)

const setupModals = () => {
  // set the modal menu element
  const $targetEl = document.getElementById("calculatorModal");

  // options with default values
  const options = {
    placement: "center",
    backdrop: "dynamic",
    backdropClasses: "bg-gray-900 bg-opacity-50 fixed inset-0 z-40",
    closable: true,
  };

  modal.value = new Modal($targetEl, options);
};

onMounted(async () => {
  setupModals();

  window.addEventListener('currentItemChanged', () => {
    isModalShown.value = true;
  })

  const {data: response} = await shopApi.get(`api/get-product/${route.params.id}`);
  item.value = response;
});

onUnmounted(() => {
  modal.value?.hide();
})
const handleCart = () => {
  const { cart: _cart, ...product } = item.value;
  productsCart.value.addToCart(JSON.parse(localStorage.getItem('currentItem')), productAmount.value);
  isModalShown.value = false;
  emitter.emit("cart:change");

  Swal.fire({
    title: "Dodano produkt do koszyka!",
    text: "Czy chcesz zakończyć składanie zapytania ofertowego, czy może wolisz kontynuować zakupy?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#808080", // Change this line
    confirmButtonText: "Przejdź do koszyka",
    cancelButtonText: "Kontynuuj zakupy"
  }).then((result) => {
    if (result.isConfirmed) {
      router.push('/koszyk.html');
    } else {
      goBack();
    }
  });
};

const handleCloseModal = () => {
  isModalShown.value = false;
  modal.value?.hide();
}

const goBack = () => {
  window.history.back();
};

</script>


<template>
  <div class="mt-12">
    <div class="mb-8 md:w-2/3 mx-auto md:space-x-5 space-y-3 md:space-y-0">
      <SubmitButton @click="goBack">
        Kliknij aby wrócić do przeglądania w poprzedniej stronie
      </SubmitButton>
    </div>

    <ProductItem
        v-if="item?.id"
        :item="item"
        :modal="modal"
        :setupModals="setupModals"
        class="w-full md:w-2/3 mx-auto"
        :isStaff="false"
        :sub-page="true"
    />

    <div
        id="calculatorModal"
        tabindex="-1"
        class="top-0 fixed z-50 w-auto hidden md:p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal w-[100%] md:h-full"
    >
      <div
          class="relative w-full h-full max-w-xl sm:max-w-3xl md:max-w-5xl lg:max-w-7xl md:h-auto"
      >
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow w-full">
          <!-- Modal header -->
          <div
              class="flex items-start justify-between p-4 border-b rounded-t"
          >
            <h3 class="text-xl font-semibold text-gray-900">
              Kalkulator cenowy
            </h3>
            <button
                type="button"
                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
                data-modal-hide="calculatorModal"
                @click="handleCloseModal"
            >
              <svg
                  aria-hidden="true"
                  class="w-5 h-5"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                  xmlns="http://www.w3.org/2000/svg"
              >
                <path
                    fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                ></path>
              </svg>
              <span class="sr-only">Close modal</span>
            </button>
          </div>
          <!-- Modal body -->
          <div class="p-2 md:p-6 space-y-6 w-[72%] md:w-full">
            <CalculatorModal v-if="isModalShown" />
          </div>
          <!-- Modal footer -->
          <div
              class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b"
          >
            <button
                @click="handleCart"
                type="button"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
            >
              Dodaj do koszyka
            </button>
            <button
                @click="handleCloseModal"
                type="button"
                class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
            >
              Zamknij kalkulator
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <RecomendationSlider
      v-if="item.similarProducts"
      :products="item.similarProducts"
  />

  <div class="mt-4 md:w-2/3 mx-auto" v-for="item in item.opinions">
    <figure class="max-w-screen-md">
      <div class="flex items-center mb-4">
        <div class="star-rating">
                <span v-for="i in 5" :key="i" class="star" v-bind:class="{ active: i <= item.rating ?? 0 }">
                  ★
                </span>
          <p>Ocena: {{ item.rating?.toFixed(1) ?? 0 }} / 5</p>
        </div>
      </div>

      <blockquote>
        <p class="text-2xl font-semibold text-gray-900">"{{ item.text }}"</p>
      </blockquote>
    </figure>
  </div>

  <create-rating :productId="route.params.id" class="mt-6"></create-rating>
</template>
