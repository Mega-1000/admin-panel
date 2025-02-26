<script lang="ts" setup>
import { getContent, getPages } from "~~/helpers/customPages";

const { params } = useRoute();

const { $shopApi: shopApi } = useNuxtApp();

const { data: content, pending } = await useAsyncData(async () => {
  try {
    const { allPages } = await getPages(shopApi);
    return getContent(allPages, params.pageId as string);
  } catch (e) {}
});
</script>

<template>
  <div class="lg:ml-10 max-w-[90%] mb-40">
    <p v-if="pending">Loading...</p>
    <div v-else :innerHTML="content"></div>
  </div>
</template>
