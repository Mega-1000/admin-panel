<script setup>
  import { Modal, ModalOptions } from "flowbite";
  import emmiter from "~/helpers/emitter";

  const item = ref({});
  const modal = ref(null);
  const route = useRoute();
  const { $shopApi: shopApi } = useNuxtApp();
  const productsCart = useProductsCart();
  const productAmount = useProductAmount();

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

    const {data: response} = await shopApi.get(`api/get-product/${route.params.id}`);

    item.value = response;
  });


  const handleCart = () => {
    const { cart: _cart, ...product } = item.value;
    productsCart.value.addToCart(product, productAmount.value);
    modal.value?.hide();

    emmiter.emit("cart:change");
  };
</script>

<template>
  <div>
    <ProductItem
        :item="item"
        :modal="modal"
        :setupModals="setupModals"
        class="w-full"
        :isStaff="false"
    />

    <div
        id="calculatorModal"
        tabindex="-1"
        class="top-0 fixed z-50 w-auto hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full"
    >
      <div
          class="relative w-full h-full max-w-xl sm:max-w-3xl md:max-w-5xl lg:max-w-7xl md:h-auto"
      >
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow">
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
          <div class="p-2 md:p-6 space-y-6 w-auto">
            <CalculatorModal />
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
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
