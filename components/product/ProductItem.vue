<script setup lang="ts">
import { Modal } from "flowbite";
import swal from 'sweetalert2';
import EditProductSection from "~/components/product/EditProductSection.vue";
import emitter from "~/helpers/emitter";

interface Props {
  item: any;
  modal: Modal | null;
  contactModal: Modal | null;
  setupModals: () => void;
  isStaff: boolean;
  showModal: boolean;
  subPage: boolean | null;
  styro: boolean | null;
}

const props = defineProps<Props>();

const { $shopApi: shopApi, $buildImgRoute: buildImgRoute } = useNuxtApp();
const config = useRuntimeConfig().public;
const currentItem = useCurrentItem();
const computedReload = ref<boolean>(false);
const items = ref();
const fastAddToCartValue = ref(0);
const route = useRoute();
const router = useRouter();

onBeforeMount(() => {
  const cart = new Cart();
  cart.init();
  items.value = cart.products.filter((item: any) => item.delivery_type === props.item.delivery_type);

  if (route.query.fastAddToCart) {
    swal.fire('Dodano do koszyka', '', 'success');
    router.replace({
      query: {
        ...route.query,
        fastAddToCart: null,
      }
    });
  }
});

onMounted(() => {
  props.setupModals();

  window.addEventListener('cart:change', () => {
    computedReload.value = !computedReload.value;

    const cart = new Cart();
    cart.init();
    items.value = cart.products.filter((item: any) => item.delivery_type === props.item.delivery_type);
  });

  // if (props.item.variation_group == 'styropiany') {
    handleShowModal(props.item);
  // }
});
const handleShowModal = async (item: any, isSubProduct = false) => {
  if (!props.subPage) {
    return;
  }

  if (!isSubProduct) {
    if (subProducts.value.length > 0) {
      subProducts.value = [];
      return;
    }

    const newSubProducts = await getSubProducts();
    if (newSubProducts.length > 0) {
      subProducts.value = newSubProducts;
      return;
    }
  }

  localStorage.setItem('currentItem', JSON.stringify(item));

  setTimeout(() => {
    props.modal?.show();
    emitter.emit('currentItemChanged');
  }, 100)
};

const subProducts = ref<any[]>([]);

const getSubProducts = async () => {
  const res = await shopApi.get("api/products/get-hidden", {
    params: {
      product: props.item.id,
      per_page: 25,
    },
  });

  return res.data;
};

const getPriceString = (priceType: any) => {
  priceType = priceType.toString().toLowerCase();
  let price: any = false;
  let unit = false;
  if (priceType === "p") {
    price = props.item.gross_selling_price_basic_unit;
    unit = props.item.unit_basic;
  } else if (priceType === "h") {
    price = props.item.gross_price_of_packing;
    unit = props.item.unit_commercial;
  } else if (priceType === "o") {
    price = props.item.gross_selling_price_calculated_unit;
    unit = props.item.calculation_unit;
  }
  if (Math.ceil(price) === 0 || !unit) {
    return null;
  }
  return `${price} PLN / ${unit}`;
};

const daysOfStock = computed(() => {
  return props?.item?.stock?.quantity / ((props.item.selledInLastWeek === 0 ? 1 : props.item.selledInLastWeek) * 7);
});

const daysOfStockText = computed(() => {
  const days = daysOfStock.value;

  if (localStorage.getItem('isAdmin') == 'true') {
    return props.item?.stock?.quantity;
  }

  if (days < 2) {
    return "Bardzo mały stan magazynowy";
  } else if (days < 7) {
    return "Mały stan magazynowy";
  } else if (days < 14) {
    return "Duży stan magazynowy";
  } else {
    return "Bardzo duży stan magazynowy";
  }
});

const daysOfStockColor = computed(() => {
  return daysOfStock.value < 7 ? "text-red-500" : "text-green-500";
});

const saveDescription = () => {
  shopApi.post(`api/products/${props.item.id}`, {
    description: props.item.description,
    name: props.item.name,
    save_name: props.item.save_name,
    save_image: props.item.save_image,
  });
};

const ShipmentCostItemsLeftText = computed(() => {
  const itemPackageQuantity = props.item.assortment_quantity;
  let itemsLeft;

  if (items.value.length > 0) {
    const itemsQuantity = Math.round((items.value.reduce((acc: any, item: any) => acc + item.amount / item.assortment_quantity, 0) % 1) * 100) / 100;
    itemsLeft = Math.floor((1 - itemsQuantity) / (1 / itemPackageQuantity));
  } else {
    itemsLeft = itemPackageQuantity;
  }

  return `Możesz dodać do przesyłki jeszcze ${itemsLeft} ${props.item.unit_commercial} tego produktu aby uzupełnić do pełna paczkę i nie ponosić dodatkowych kosztów transportu.`;
});

const fastAddToCart = () => {
  let cart: any = new Cart();
  cart.init();
  const cartInstance = cart;
  cart = cart.products;

  const { cart: _cart, ...product } = props.item;
  cartInstance.addToCart(product, fastAddToCartValue.value);

  const url = new URL(window.location.href);
  url.searchParams.set("fastAddToCart", "true");
  window.history.replaceState({}, "", url.toString());

  window.location.reload();
};

const decreaseFastAddToCartValue = () => {
  if (fastAddToCartValue.value > 0) {
    fastAddToCartValue.value--;
  }
};
</script>

<template>

      <div v-if="props.item.blured" class="absolute inset-0 z-10 flex justify-center items-center bg-white bg-opacity-50">
        <span class="text-red-500 font-semibold">Produkt niedostępny dla podanego kodu pocztowego</span>
      </div>

      <div
          class="w-full md:w-1/3 bg-white grid place-items-center md:place-items-start"
      >
        <img
            :src="buildImgRoute(props.item.url_for_website)"
            alt="Photo"
            loading="lazy"
            class="rounded-xl"
        />`
      </div>
      <div
        class="w-full md:w-[170%] bg-white flex flex-col space-y-2 p-3 grid md:place-items-end"
      >
        <h3 class="font-black text-gray-800 md:text-3xl text-xl" style="margin-right: auto;">
          <span v-if="!isStaff">
            {{ item.name }}
            <span class="text-left w-full font-light text-sm">
              {{ item.symbol }}
            </span>
          </span>

          <EditProductSection :item="item" v-else />

          <span class="md:text-lg text-gray-500 text-base">
            <span v-if="isStaff">opis: </span>
            <div v-if="!isStaff" v-html="item?.description?.replaceAll('\n', '<br />')"></div>
            <textarea class="block h-[200px]" v-else @input="saveDescription" v-model="item.description">{{ item.description }}</textarea>
          </span>

          <span class="text-lg" v-if="item.variation_group !== 'styropiany'">
            Ilość asortymentu wchodząca do jednej paczki: {{ item.assortment_quantity }}
          </span>
        </h3>

        <div v-if="item.meta_price" class="flex w-full justify-between">
          <form @submit.prevent="fastAddToCart" v-if="!item.hasChildren && item.variation_group !== 'styropiany' && subPage" class="text-lg">
            Szybkie dodawanie do koszyka:
            <br>
            <div class="flex w-fit items-center">
                <span class="text-6xl px-3" @click="decreaseFastAddToCartValue">
                  -
                </span>

              <input type="number" v-model="fastAddToCartValue" class="border mt-4 text-center w-[80%]">

              <span @click="fastAddToCartValue++" class="text-6xl px-3">
                  +
              </span>

              <SubmitButton class="mt-1">
                Dodaj do koszyka
              </SubmitButton>
            </div>
          </form>

          <div
            class="text-3xl font-bold"
            v-for="val in item.meta_price.split(`.`)"
          >
            {{ getPriceString(val) }}
            <div class="mt-4 text-lg" v-if="item.variation_group !== 'styropiany'">
              <div :class="daysOfStockColor">
                {{ daysOfStockText }}
              </div>
            </div>

            <div class="flex items-center mb-4">
              <div class="star-rating">
                <span v-for="i in 5" :key="i" class="star" v-bind:class="{ active: i <= item.meanOpinion ?? 0 }">
                  ★
                </span>
                <p>Ocena: {{ item.meanOpinion?.toFixed(1) ?? 0 }} / 5</p>
              </div>
            </div>
          </div>
        </div>

        <div
            :class="{ 'cursor-not-allowed filter': props.item.blured }"
            data-modal-target="calculatorModal"
            @click="() => handleShowModal(item)"
        >
          <NuxtLink v-if="!subPage" class="flex flex-col justify-center" v-tooltip.auto-start="props.item.variation_group !== 'styropiany' ? ShipmentCostItemsLeftText : ''" :href="`/singleProduct/${item.id}`">
            <SubmitButton>
              {{ props.subPage ? 'Kalkulator cenowy' : 'Zobacz szczegóły i dodaj do koszyka' }}
            </SubmitButton>
          </NuxtLink>
        </div>
        <div class="inline-flex rounded-md shadow-sm" role="group">
        </div>
      </div>
    <div class="md:w-2/3 md:mx-auto">
      <div
        v-for="subProduct in subProducts"
        class="py-4 cursor-pointer border-t border-md bg-slate-100 px-3"
      >
        <div
          class="flex flex-row justify-between"
          @click="() => handleShowModal(subProduct, true)"
          data-modal-target="calculatorModal"
        >
          <p class="text-sm pr-2">{{ subProduct.name }}</p>
          <p class="text-sm pr-2">
            Cena: {{ subProduct.gross_price_of_packing }}
            {{ subProduct.currency || "PLN" }}/{{ subProduct.unit_commercial }}
          </p>
<!--          <p class="text-sm">Symbol produktu: {{ subProduct.symbol }}</p>-->

          <submitButton>
            Wylicz ilość do zapytania
          </submitButton>
        </div>
      </div>
    </div>
</template>

<style scoped>
.star {
  color: #ccc; /* Gray by default */
  font-size: 24px;
}
.star.active {
  color: #f5d742; /* Gold for active stars */
}
</style>
