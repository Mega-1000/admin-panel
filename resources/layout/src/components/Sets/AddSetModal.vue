<template>
  <div class="c-addSetModal">
    <div class="overlay" @click="$emit('close')"></div>
    <div class="c-modal">
      <div class="header">
          <p class="modal-title">Dodaj nowy zestaw</p>
          <span @click="$emit('close')" class="close">X</span>
      </div>
      <div class="content">
        <template v-if="buttonsVisible">
          <button class="btn btn-sm btn-success" @click="togggleCreateNewSet()">Stwórz nowy zestaw</button>
          <button class="btn btn-sm btn-warning" @click="togggleNewSetFromProduct()">Stwórz nowy zestaw z istniejącego produktu</button>
        </template>
        <template v-if="newSetFromProduct">
          <button class="btn btn-sm btn-info return" @click="togggleButtonVisible()">Powrót</button>
          <div class="form-group">
            <label>Wyszukaj produkt</label>
            <input type="text" class="form-control" v-on:keyup="searchProducts()" v-model="word">
          </div>
          <template v-if="products.length > 0">
            <select v-model="productId">
              <option v-for="(product, index) in filteredProducts" :key="index" :value="product.id">{{ product.symbol }} => {{ product.name }}</option>
            </select>
            <button @click="createSetFromProduct()" class="btn btn-success btn-create">Stwórz</button>
          </template>
        </template>
        <template v-if="createNewSet">
          <button @click="togggleButtonVisible()" class="btn btn-sm btn-info return">Powrót</button>
          <div class="product_stocks-general" id="general">
            <div class="form-group">
              <label for="name">Nazwa</label>
              <input type="text" class="form-control" id="name" v-model="name">
            </div>
            <div class="form-group">
              <label for="number">Symbol</label>
              <input type="text" class="form-control" id="number" v-model="symbol">
            </div>
            <div class="form-group">
              <label for="number">Cena(domyślna)</label>
              <input type="number" class="form-control" id="price" v-model="price">
            </div>
          </div>
          <button class="btn btn-sm btn-success" @click="createSet()">Stwórz</button>
        </template>
      </div>
    </div>
  </div>
</template>
<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { CreateSetParams, Set, SetProduct, SetsProductParams } from '@/types/SetsTypes'

@Component({
  components: {
  }
})
export default class AddSetModal extends Vue {
  public buttonsVisible = true
  public newSetFromProduct = false
  public createNewSet = false
  public word = ''
  public name = ''
  public symbol = ''
  public price = 0
  public productId = null

  public searchParams: SetsProductParams = {
    name: '',
    symbol: '',
    manufacturer: '',
    word: ''
  };

  public togggleButtonVisible (): void {
    this.buttonsVisible = true
    this.newSetFromProduct = false
    this.createNewSet = false
  }

  public togggleNewSetFromProduct (): void {
    this.buttonsVisible = false
    this.newSetFromProduct = true
  }

  public togggleCreateNewSet (): void {
    this.buttonsVisible = false
    this.createNewSet = true
  }

  public get products (): SetProduct[] {
    return this.$store?.getters['SetsService/products']
  }

  public get sets (): Set[] {
    return Object.values(this.$store?.getters['SetsService/sets'])
  }

  public get filteredProducts (): SetProduct[] {
    return this.products.filter((product) => {
      return (!this.findExistProductSet(product.id))
    })
  }

  private findExistProductSet (id: number): boolean {
    const result = this.sets.filter((set) => {
      return (set.set.product_id === id)
    })
    return (result.length > 0)
  }

  public async searchProducts (): Promise<void> {
    if (this.word.length > 2) {
      this.searchParams.word = this.word
      await this.$store.dispatch('SetsService/loadProducts', this.searchParams)
    }
  }

  public async createSetFromProduct (): Promise<void> {
    if (this.productId) {
      await this.$store.dispatch('SetsService/cerateSetFromProduct', this.productId)
      this.$emit('load-sets')
      this.$emit('close')
    }
  }

  public async createSet (): Promise<void> {
    if ((this.name !== '') && (this.symbol !== '') && (this.price > 0)) {
      const params: CreateSetParams = {
        name: this.name,
        symbol: this.symbol,
        price: this.price
      }
      await this.$store.dispatch('SetsService/cerateSet', params)
      this.$emit('load-sets')
      this.$emit('close')
    }
  }
}
</script>

<style scoped lang="scss">
  @import "@/assets/styles/main";

  .c-addSetModal {
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    z-index: $index-addSetModal;
  }

  .close {
    position: absolute;
    right: 10px;
    top: 10px;
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
    position: relative;
  }

  span {
    font-weight: 600;
  }

  .return {
    position: absolute;
    left: 10px;
    top: 10px;
  }

  .modal-title {
    font-size: 18px;
    line-height: 43px;
    color: $cl-grey5;
    font-weight: 700;
    margin: 15px 0;
  }

  .btn-create {
    display: block;
    margin: 25px auto 0 auto;
  }

  .select {
    padding: 10px;
    border: 1px solid $cl-greye;
    word-wrap: break-word;
    white-space: -moz-pre-wrap;
    white-space: pre-wrap;
    max-width: 100%;
    display: inline-block;
  }
</style>
