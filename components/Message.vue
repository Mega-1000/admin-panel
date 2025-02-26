<script setup lang="ts">
const router = useRouter();
const message = ref(router.currentRoute.value.query.message);

router.afterEach((to, from) => {
  message.value = to.query.message;
});
</script>

<template>
  <div class="flex items-center bg-blue-500 text-white text-lg font-bold px-8 py-6 mx-4 mt-8 rounded" role="alert" v-if="message">
    <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
      <path d="M0 11l2-2 5 5L18 3l2 2L7 18z" />
    </svg>
    <p>
      {{ message }}
    </p>
  </div>
</template>
