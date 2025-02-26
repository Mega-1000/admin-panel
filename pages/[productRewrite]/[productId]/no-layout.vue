<script setup lang="ts">
import findActiveMenu from "~~/helpers/findActiveMenu";
import { defaultImgSrc } from "~~/helpers/buildImgRoute";
import { Modal, ModalOptions } from "flowbite";
import Cookies from "universal-cookie";
import emmiter from "~/helpers/emitter";
import AskUserForZipCodeStyrofoarms from "~/components/AskUserForZipCodeStyrofoarms.vue";
import {integer} from "vscode-languageserver-types";

const { $shopApi: shopApi, $buildImgRoute: buildImgRoute } = useNuxtApp();

const currentItem = useCurrentItem();

const { params, query } = useRoute();
const { productId } = params;
const page = ref(parseInt((query.page as string) || "1"));
const isStaff = ref(false);
const askUserForZipCode = ref(false);
const categoryFirmId = ref<integer|null>(null);
const isMainStyrofoamLobby = ref<bool>(false);

const { data: currentProduct, pending: pending1 } = await useAsyncData(
    async () => {
      try {
        const res = await shopApi.get("/api/products/categories");
        const currentProduct = findActiveMenu(res.data, productId as string);
        let product = { ...currentProduct };
        let categoryTree = [currentProduct];
        while (product.parent_id && parseInt(product.parent_id) !== 0) {
          product = findActiveMenu(res.data, product.parent_id);
          categoryTree = [product, ...categoryTree];
        }

        return {
          currentProduct,
          categories: res.data,
          categoryTree,
        };
      } catch (err) {}
    }
);

const { data: categoryData, pending: pending2 } = await useAsyncData(
    async () => {
      try {
        const res = await shopApi.get(
            `/api/categories/details/search?category=${productId}`
        );
        return res.data;
      } catch (err) {}
    }
);

const { data: itemsData, pending: pending3 } = await useAsyncData(
    async () => {
      try {
        const currentPage = query?.page as string ?? 1;
        const zipCode = localStorage.getItem('zipCode');

        const res = await shopApi.get(
            `/api/products/categories/get?page=${currentPage}&per_page=10&category_id=${productId}&zipCode=${zipCode}`
        );
        return res.data;
      } catch (e) {
        console.log(e);
        return [];
      }
    },
    { watch: [page] }
);

const buildLink = ({ rewrite, id, name }: { rewrite: string; id: number, name: string }) =>
    name !== 'porady na temat zakupu styropianu' ? `/${rewrite}/${id}` : '/Styrofoarm-generate-table';

const modal = ref<Modal | null>(null);

const contactModal = ref<Modal | null>(null);

const setupModals = () => {
  // set the modal menu element
  const $targetEl = document.getElementById("calculatorModal");

  // options with default values
  const options: ModalOptions = {
    placement: "center",
    backdrop: "dynamic",
    backdropClasses: "bg-gray-900 bg-opacity-50 fixed inset-0 z-40",
    closable: true,
  };

  modal.value = new Modal($targetEl, options);

  const $contactTargetEl = document.getElementById("contactModal");

  // options with default values
  const contactOptions: ModalOptions = {
    placement: "center",
    backdrop: "dynamic",
    backdropClasses: "bg-gray-900 bg-opacity-50 fixed inset-0 z-40",
    closable: true,
  };

  contactModal.value = new Modal($contactTargetEl, contactOptions);
};

onMounted(async () => {
  setupModals();

  if (productId === '103' && !localStorage.getItem('zipCode')) {
    askUserForZipCode.value = true;
    isMainStyrofoamLobby.value = true;
  }

  const data:any = await shopApi.get('/api/staff/isStaff');
  if (data.data) {
    await handleStaffUser();
  }
});
watch([itemsData], setupModals);

const handleStaffUser = async () => {
  isStaff.value = true;

  const categoryFirmName: string = currentProduct.value?.currentProduct?.name;
  const matched = categoryFirmName.match(/-+([a-zA-Z]+-?[a-zA-Z]+ ?[a-zA-Z]*)/);
  let result = matched ? matched[1] : null;

  const categoryFirm: any = await shopApi.get(`/api/firm/${result}`);
  categoryFirmId.value = categoryFirm?.data?.id
}

const handleCloseModal = () => {
  modal.value?.hide();
  currentItem.value = null;
};

const productsCart = useProductsCart();
const productAmount = useProductAmount();

const handleCart = () => {
  const { cart: _cart, ...product } = currentItem.value;
  productsCart.value.addToCart(product, productAmount.value);
  modal.value?.hide();

  emmiter.emit("cart:change");
};

let emailInput = "";
let phoneInput = "";
let postalCodeInput = "";

const loading = ref(false);
const errorMessage = ref<string | null>(null);

const selectedMediaId = useSelectedMediaId();

const handleSubmit = async (e: Event) => {
  e.preventDefault();
  loading.value = true;

  try {
    const res = await shopApi.post("/api/chat/getUrl", {
      mediaId: selectedMediaId.value,
      postCode: postalCodeInput,
      email: emailInput,
      phone: phoneInput,
    });

    let req = JSON.parse(res.config.data);
    const cookies = new Cookies();
    cookies.set("email", req.email);
    cookies.set("post_code", req.postCode);
    cookies.set("phone", req.phone);
    errorMessage.value = null;
    window.open(res.data.url, "_blank");
    contactModal.value?.hide();
  } catch (err: any) {
    errorMessage.value =
        err?.response?.data?.errorMessage ||
        "Wystąpil błąd. Spróbuj ponownie później";
  } finally {
    loading.value = false;
  }
};

const router = useRouter();

const goToPage = async (val: number) => {
  page.value = val;

  router.push({
    query: {
      page: val,
    },
  });

  window.location.reload();
  window.scrollTo(0, 0);
};
</script>

<template>
  <AskUserForZipCodeStyrofoarms v-if="askUserForZipCode" />

  <div
      class="md:flex justify-items-center mb-40 mx-15"
      v-if="![pending1, pending2, pending3].some((el) => el)"
  >
      <div
          v-if="
          currentProduct?.currentProduct?.children &&
          currentProduct?.currentProduct.children.length > 0 &&
          (!itemsData.data || !(itemsData.data.length > 0))
        "
          class="grid max-w-8xl grid-cols-1 gap-6 px-6 pt-6 pb-40 sm:grid-cols-2 xl:grid-cols-3 mb-30"
      >
        <article
            v-for="product in currentProduct.currentProduct.children"
            class="w-full h-full rounded-xl bg-white p-3 shadow-lg hover:shadow-xl hover:transform hover:scale-105 duration-300"
        >
          <NuxtLink
              :href="buildLink(product)"
              class="flex flex-col justify-between h-full"
          >
            <div class="overflow-hidden rounded-xl">
              <img
                  :src="buildImgRoute(product.img)"
                  alt="Photo"
                  loading="lazy"
                  @error="(e: any) => (e.target!.src = defaultImgSrc)"
                  class="h-full w-full"
              />
            </div>

            <div class="mt-1 p-2">
              <h2 class="text-gray-900 font-medium">
                {{ product.name }}
              </h2>
            </div>
          </NuxtLink>
        </article>
      </div>

      <div
          v-else-if="
          !categoryData?.chimney_attributes ||
          !(categoryData?.chimney_attributes.length > 0)
        "
      >
        <section class="py-10">
          <div class="grid w-full grid-cols-1 gap-6 p-6 mb-10">
            <ProductItem
                v-for="item in itemsData.data"
                :item="item"
                :modal="modal"
                :contactModal="contactModal"
                :setupModals="setupModals"
                class="w-full"
                :isStaff="isStaff"
            />
            <nav
                aria-label="Page navigation example"
                v-if="itemsData.last_page > 1"
                class="mx-auto"
            >
              <ul class="inline-flex items-center -space-x-px">
                <li>
                  <button
                      :disabled="page < 2"
                      @click="() => goToPage(page - 1)"
                      class="block px-3 py-2 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-700"
                  >
                    <span class="sr-only">Previous</span>
                    <svg
                        aria-hidden="true"
                        class="w-6 h-6"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                          fill-rule="evenodd"
                          d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                          clip-rule="evenodd"
                      ></path>
                    </svg>
                  </button>
                </li>
                <li v-for="i in itemsData.last_page">
                  <button
                      :disabled="page === i"
                      @click="() => goToPage(i)"
                      :class="`text-xl px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 ${
                      page === i && `bg-gray-100`
                    } hover:bg-gray-100 hover:text-gray-700`"
                  >
                    {{ i }}
                  </button>
                </li>
                <li>
                  <button
                      :disabled="page === itemsData.last_page"
                      @click="() => goToPage(page + 1)"
                      class="block px-3 py-2 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-700"
                  >
                    <span class="sr-only">Next</span>
                    <svg
                        aria-hidden="true"
                        class="w-6 h-6"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                          fill-rule="evenodd"
                          d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                          clip-rule="evenodd"
                      ></path>
                    </svg>
                  </button>
                </li>
              </ul>
            </nav>
          </div>
        </section>
        <!-- Main modal -->
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
              <div class="p-6 space-y-6 w-auto">
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

        <div
            id="contactModal"
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
                <h5 class="text-xl xl:text-2xl font-medium text-gray-900">
                  Uzupełnij dane do kontaktu
                </h5>
                <button
                    type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
                    data-modal-hide="calculatorModal"
                    @click="contactModal?.hide"
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
              <div class="p-6 space-y-6 w-auto">
                <form class="space-y-6">
                  <div>
                    <label
                        for="email"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >Email</label
                    >
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required
                        :disabled="loading"
                        v-model="emailInput"
                    />
                  </div>
                  <div>
                    <label
                        for="phone"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >Phone</label
                    >
                    <input
                        :disabled="loading"
                        v-model="phoneInput"
                        type="phone"
                        name="phone"
                        id="phone"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required
                    />
                  </div>
                  <div>
                    <label
                        for="postal-code"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >Kod Pocztowy</label
                    >
                    <input
                        name="postal-code"
                        id="postal-code"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required
                        :disabled="loading"
                        v-model="postalCodeInput"
                    />
                  </div>
                  <p class="mt-2 text-sm text-red-600">
                    {{ errorMessage }}
                  </p>
                </form>
              </div>
              <!-- Modal footer -->
              <div
                  class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b"
              >
                <button
                    @click="handleSubmit"
                    type="button"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                >
                  Wyślij
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>

