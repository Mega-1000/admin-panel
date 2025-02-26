<script setup lang="ts">
  interface props {
    label: string;
    options: { label: string; value: string }[];
  }

  const props = defineProps<props>();
</script>

<template>
  <div v-for="(v, k) in options" class="bg-red-500 rounded p-2 my-2 text-white font-semibold shadow-lg">
    <label class="mb-1">{{ k }}</label>:    {{ v }}
  </div>
</template>
