<template>
  <div class="w-[80%] mx-auto flex space-x-2 mt-6 overflow-x-auto py-2">
    <nuxt-link :href="`/newspaper/category/${category?.name}`" v-for="category in categories">
      <submit-button :disabled="$route?.params?.category === category?.name" v-if="category?.name">
        {{ category?.name }}
      </submit-button>
    </nuxt-link>
  </div>
</template>

<script setup lang="ts">
const { $shopApi: shopApi } = useNuxtApp();
const categories = ref([]);

onMounted(async () => {
  const { data } = await shopApi.get(`/api/discounts/get-categories`);
  categories.value = data;
});
</script>
