<template>
  <div class="error">
      <span @click="$emit('close')">X</span>
      <p>{{ errorMessage }}</p>
  </div>
</template>
<script lang="ts">
import { Component, Vue, Prop } from 'vue-property-decorator'

@Component
export default class Error extends Vue {
  @Prop() public errorMessage!: string
}
</script>
<style scoped lang="scss">
  @import "../assets/styles/main";

  .error {
    position: fixed;
    top: 0;
    right: 0;
    background: $cl-red9c;
    border-radius: 20px;
    padding: 20px;
    z-index: $index-alert;
  }
</style>
