<script setup>
import placeholder from "~~/mocks/placeholder";

const { data: content } = useLazyAsyncData(() => placeholder, {
  server: false,
});
</script>

<template>
  <div class="lg:pl-20 max-w-[90%] mb-40">
    <div :innerHTML="content"></div>
  </div>
</template>
