<template>
  <div class="c-needStockModal">
    <div class="overlay" @click="$emit('close')"></div>
    <div class="c-modal">
      <div class="header">
        <p>Brakuje towaru na półce. Przenieś asortyment aby móc stworzyć zestaw</p>
        <span @click="$emit('close')">X</span>
      </div>
      <div class="content">
        <ul>
          <li v-for="product in products" :key="product.id">
            <b>{{ product.symbol }}</b> => {{ product.name }} <b>Wymagana Ilość: {{ product.stock*count }}</b>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>
<script lang="ts">
import { Component, Vue, Prop } from 'vue-property-decorator'
import { SetProduct, Set } from '@/types/SetsTypes'

@Component({
  components: {
  }
})
export default class NeedStockModal extends Vue {
  @Prop() private needStock!: number[]
  @Prop() private set!: Set
  @Prop() public count!: number

  // public get products (): SetProduct[] {
  //   return this.set.products.filter((product) => this.needStock.find((number) => number === product.id))
  // }
  public get products (): SetProduct[] {
    return this.set.products
  }

  public mounted (): void {
    console.log(this.needStock)
    console.log(this.set)
    console.log(this.count)
  }
}
</script>

<style scoped lang="scss">
  @import "@/assets/styles/main";

  .c-needStockModal {
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    z-index: $index-addSetModal;
  }

  .overlay {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background: $cl-black00;
    opacity: 0.5;
    z-index: $index-addSetModalOverlay;
  }

  .c-modal {
    max-width: 100%;
    width: 450px;
    margin: 150px auto;
    background: $cl-whiteff;
    padding: 50px;
    z-index: $index-addSetModalContent;
  }
</style>
