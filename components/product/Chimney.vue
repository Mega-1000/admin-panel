<script setup lang="ts">
interface Props {
  attributes: any[];
}

const props = defineProps<Props>();

const loading = ref(false);

const { $shopApi: shopApi, $buildImgRoute: buildImgRoute } = useNuxtApp();

const state = ref<any | null>(null);

const handleSubmit = async (e: any) => {
  e.preventDefault();

  loading.value = true;

  const fields = Array.prototype.slice
    .call(e.target)
    .filter((el) => el.type !== "submit")
    .reduce(
      (form, el) => ({
        ...form,
        [el.id]: el.value,
      }),
      {}
    );

  try {
    const response = await shopApi.get("/api/products/chimney", {
      params: fields,
    });
    state.value = {
      items: response.data.products
        .map((item: any) => {
          item.quantity = Math.ceil(item.quantity);
          return item;
        })
        .filter((item: any) => {
          return item.optional === 0;
        }),
      additions: response.data.products.filter((item: any) => {
        return item.optional === 1;
      }),
      conversions: Object.values(response.data.replacements),
      conversionsItems: response.data.products_replace,
    };
  } catch (e: any) {
    console.log(e?.message);
  } finally {
    loading.value = false;
  }
};

const productsCart = useProductsCart();

const router = useRouter();

const addToCart = () => {
  state.value.items.map((item: any) => {
    productsCart.value.addToCart(item, item.quantity);
  });

  state.value.additions
    .filter((item: any) => {
      return item.isSelected;
    })
    .map((item: any) => {
      productsCart.value.addToCart(item, item.quantity);
    });

  router.push("/koszyk.html");
};

const replaceProducts = (item: any) => {
  var tmp1 = state.value.items.filter((el: any) => el.changer === item.id);
  var tmp2 = state.value.conversionsItems.filter(
    (el: any) => el.changer === item.id
  );

  state.value = {
    items: state.value.items
      .filter((el: any) => el.changer !== item.id)
      .concat(tmp2),
    conversionsItems: state.value.conversionsItems
      .filter((el: any) => el.changer !== item.id)
      .concat(tmp1),
    additions: state.value.additions,
    conversions: state.value.conversions,
  };
};

const config = useRuntimeConfig().public;
</script>

<template>
  <div class="flex justify-center">
    <div
      class="w-screen max-w-sm md:max-w-md lg:max-w-2xl xl:max-w-4xl p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-6 md:p-8 my-7"
    >
      <form class="space-y-6" @submit="handleSubmit">
        <div v-for="attribute in props.attributes">
          <label
            :for="`attr[${attribute.id}]`"
            class="block mb-2 text-sm lg:text-lg font-medium text-gray-900"
            >{{ attribute.name.trim() }}</label
          >
          <select
            :id="`attr[${attribute.id}]`"
            v-if="attribute.options && attribute.options.length > 0"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
          >
            <option v-for="item in attribute.options" :value="item.id">
              {{ item.name }}
            </option>
          </select>
          <input
            v-else
            type="email"
            name="email"
            :id="`attr[${attribute.id}]`"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm lg:text-lg rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
            required
            :disabled="loading"
          />
        </div>
        <button
          class="w-full text-white bg-cyan-400 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm lg:text-lg px-5 py-2.5 text-center"
          :disabled="loading"
          type="submit"
        >
          Oblicz
        </button>
      </form>
    </div>
  </div>
  <div v-for="item in state?.items || []" class="my-8">
    <div
      class="relative flex flex-col md:flex-row md:space-x-5 space-y-3 md:space-y-0 rounded-xl shadow-lg p-3 w-[60vw] max-w-7xl mx-auto border border-white bg-white"
    >
      <div
        class="w-full md:w-1/3 bg-white grid place-items-center md:place-items-start"
      >
        <img
          :src="buildImgRoute(item?.url_for_website)"
          alt="Photo"
          class="rounded-xl"
        />
      </div>
      <div
        class="w-full md:w-2/3 bg-white flex flex-col space-y-2 p-3 grid md:place-items-end"
      >
        <h3 class="font-black text-gray-800 md:text-2xl text-xl">
          {{ item.name }}
        </h3>
        <p class="md:text-md text-gray-500 text-base">
          Symbol: {{ item?.symbol }}
        </p>
        <p class="md:text-md text-gray-500 text-base">
          Ilość: {{ item?.quantity }} {{ item?.unit_commercial }}
        </p>
        <p class="md:text-md text-gray-500 text-base">
          Cena netto:
          {{ parseFloat(item.net_selling_price_aggregate_unit).toFixed(2) }}
          {{ item.currency || "PLN" }}
        </p>
        <p class="md:text-md text-gray-500 text-base">
          Cena brutto:
          {{ parseFloat(item.gross_price_of_packing).toFixed(2) }}
          {{ item.currency || "PLN" }}
        </p>
        <p class="md:text-md text-gray-500 text-base">
          Wartość netto:
          {{
            (item.net_selling_price_aggregate_unit * item.quantity).toFixed(2)
          }}
          {{ item.currency || "PLN" }}
        </p>
        <p class="md:text-md text-gray-500 text-base">
          Wartość brutto:
          {{ (item.gross_price_of_packing * item.quantity).toFixed(2) }}
          {{ item.currency || "PLN" }}
        </p>
      </div>
    </div>
  </div>
  <hr />
  <div
    v-if="state?.additions && state.additions.length > 0"
    class="justify-center mt-4"
  >
    <h2 class="text-center font-bold text-4xl">Dodatki:</h2>
    <div v-for="item in state.additions" class="py-5">
      <div
        class="relative flex justify-between items-center flex-col md:flex-row md:space-x-5 space-y-3 md:space-y-0 rounded-xl shadow-lg p-3 w-[60vw] max-w-7xl mx-auto border border-white bg-white"
      >
        <div class="w-[60%]">
          <div class="flex items-center">
            <input
              type="checkbox"
              v-model="item.isSelected"
              class="w-4 h-4 text-blue-600 mt-1 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
            />
            <label
              for="default-checkbox"
              :class="`ml-2 text-lg font-medium text-gray-900`"
              >{{ item.name }}</label
            >
          </div>
          <p>Ilość: {{ item.quantity }} {{ item.unit_commercial }}</p>
        </div>
        <div>
          <img :src="buildImgRoute(item.url_for_website)" />
        </div>
        <div>
          <p>
            cena netto: {{ item.net_selling_price_commercial_unit }}
            {{ item.currency || "PLN" }}
          </p>
          <p>
            cena brutto: {{ item.gross_price_of_packing }}
            {{ item.currency || "PLN" }}
          </p>
        </div>
      </div>
    </div>
  </div>
  <hr />
  <div
    v-if="state?.conversions && state.conversions.length > 0"
    class="justify-center mt-4"
  >
    <h2 class="text-center font-bold text-4xl">Zamiany</h2>
    <div v-for="item in state.conversions" class="py-5">
      <div
        class="relative flex justify-between items-center flex-col md:flex-row md:space-x-5 space-y-3 md:space-y-0 rounded-xl shadow-lg p-3 w-[60vw] max-w-7xl mx-auto border border-white bg-white"
      >
        <div class="w-[60%]">
          <div class="flex items-center">
            <input
              type="checkbox"
              @change="replaceProducts(item)"
              class="w-4 h-4 text-blue-600 mt-1 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
            />
            <label
              for="default-checkbox"
              :class="`ml-2 text-lg font-medium text-gray-900`"
              >{{ item.description }}</label
            >
          </div>
        </div>
        <div class="w-[40%]">
          <img :src="buildImgRoute(item.img)" />
        </div>
      </div>
    </div>
  </div>
  <div
    v-if="state?.items && state.items.length > 0"
    class="flex justify-center my-10 pb-10"
  >
    <button
      class="w-[20%] text-white bg-cyan-400 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-md lg:text-xl px-5 py-2.5 text-center"
      :disabled="loading"
      type="button"
      @click="addToCart"
    >
      Dodaj do koszyka
    </button>
  </div>
</template>
