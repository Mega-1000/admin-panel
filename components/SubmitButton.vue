<script setup lang="ts">
  defineProps<{
    disabled: boolean;
  }>();

</script>

<template>
  <button class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center disabled:bg-gray-500" :disabled="disabled">
    <slot />
  </button>
</template>
