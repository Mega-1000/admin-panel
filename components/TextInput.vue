<script setup lang="ts">
  interface Props {
    placeholder: string;
    type: string;
    label: string;
    value: string;
  }

  const emit = defineEmits<{
    (e: 'input', value: string): void
  }>();

  const onInput = (e: any) => {
    emit('input', e.target.value);
  };

  defineProps<Props>();
</script>

<template>
  <div>
    <label class="block text-gray-700 text-sm font-bold mb-2">
      {{ label }}
    </label>
    <input :type="type" :value="value" @input="onInput($event)" :placeholder="placeholder" class="px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
  </div>
</template>
