<template>
  <NewspaperNavbar />

  <div class="w-[80%] mx-auto mt-8">
    <div class="mb-4">
      Strona kategori {{ $route.params.category }}
      <nuxt-link class="text-blue-500 ml-2" href="/">
        Przejdź do sklepu głównego
      </nuxt-link>
    </div>

    <div class="flex flex-wrap gap-10 mb-20">
      <NewspaperItemCard
        :image="discount.product.url_for_website"
        :title="discount.product.name"
        :description="discount.description"
        :old-price="discount.old_amount"
        :new-price="discount.new_amount"
        v-for="discount in discounts"
      />
    </div>
  </div>

</template>

<script setup lang="ts">
const { $shopApi: shopApi } = useNuxtApp();

const config = useRuntimeConfig().public;
const discounts = ref([]);
const route = useRoute();
const loading = ref(false);
const router = useRouter();

onMounted(async () => {
  try {
    const { data: response } = await shopApi.get(`${config.baseUrl}/api/discounts/get-by-category/${route.params.category}`);
    discounts.value = response;
  } catch (e: any) {
    if (e.response.status === 404) {
      router.push('/');
    }
  } finally {
    loading.value = false;
  }
});
</script>
