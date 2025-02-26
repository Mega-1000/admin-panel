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
        :showModal="true"
    />
    <CalculatorModal />
  </div>
</template>
