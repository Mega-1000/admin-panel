<template>
  <div class="error" v-if="isError">
      <span @click="close()">X</span>
      <p>{{ message }}</p>
  </div>
</template>
<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'

@Component
export default class Error extends Vue {
  public isError = false
  public message = ''

  public close (): void {
    this.isError = false
  }

  public get error (): string {
    return this.$store?.getters['SetsService/error']
  }

  @Watch('error')
  private listenError () {
    this.message = this.error
    this.isError = true
  }
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
  }
</style>
