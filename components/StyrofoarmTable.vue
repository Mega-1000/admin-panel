<script setup lang="ts">
  import StyrofoarmTable from "~/components/StyrofoarmTable.vue";

  interface props {
    tableData: typeof StyrofoarmTable
  }

  const props = defineProps<{
    tableData: any[]
  }>()

  const emit = defineEmits<{
    (e: 'createChat', value: boolean): void
  }>()
</script>

<template>
  <div class="overflow-x-auto">
    <table class="table-auto">

    </table>
  </div>
</template>

<style>
.overflow-x-auto {
  width: 100%;
}
</style>


