<script setup lang="ts">
  defineProps<{
    disabled: boolean;
  }>();
</script>

<template>
  <button
    class="disabled:bg-slate-400 w-full text-3xl text-white bg-cyan-400 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
    :disabled="disabled"
    type="submit"
  >
    <slot />
  </button>
</template>