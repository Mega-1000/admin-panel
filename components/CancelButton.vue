<script setup lang="ts">
defineProps<{
  disabled: boolean;
}>();

</script>

<template>
  <button class="bg-red-500 px-4 py-2 rounded text-white disabled:bg-slate-400" :disabled="disabled">
    <slot />
  </button>
</template>
