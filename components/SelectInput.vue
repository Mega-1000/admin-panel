<script setup lang="ts">
interface SelectInputProps {
  label: string;
  options: { label: string; value: string }[];
  modelValue: string;
  'onUpdate:modelValue': (value: string) => void;
}

const props = defineProps<SelectInputProps>();

const value = ref(props.modelValue);

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>();

function updateValue(event: Event) {
  const selectedValue = (event.target as HTMLSelectElement).value;
  value.value = selectedValue;

  emit('update:modelValue', selectedValue);
}
</script>

<template>
  <div class="flex flex-col">
    <label class="mb-1">{{ label }}</label>
    <select class="border rounded-md px-3 py-2" :value="value" @input="updateValue">
      <option v-for="option in options" :value="option.value" :key="option.value">{{ option.label }}</option>
    </select>
  </div>
</template>

<style scoped>
select:focus {
  outline: none;
  box-shadow: none;
}
</style>
