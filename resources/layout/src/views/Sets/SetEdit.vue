<template>
  <div class="v-setEdit">
    <div class="form-group">
      <label>Wyszukaj produkct aby dodać do zestawu</label>
      <input type="text" class="form-control" v-on:keyup="searchProducts()" v-model="word">
    </div>
    <table id="dataTable" class="table table-hover">
      <thead>
      <tr>
        <th></th>
        <th>ID</th>
        <th>Nazwa produktu</th>
        <th>Symbol</th>
        <th>Producent</th>
        <th>Akcja</th>
      </tr>
      </thead>
      <tbody id="productTable" v-if="products.length > 0">
        <tr v-for="(product, index) in products" :key="index">
          <td></td>
          <td>{{ index }}</td>
          <td>{{ product.name }}</td>
          <td>{{ product.symbol }}</td>
          <td>{{ product.manufacturer }}</td>
          <td></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { SetProduct, SetsProductParams } from '@/types/SetsTypes'

@Component({
  components: {
  }
})
export default class SetEdit extends Vue {
  public word = '';

  public searchParams: SetsProductParams = {
    name: '',
    symbol: '',
    manufacturer: '',
    word: ''
  };

  public get products (): SetProduct[] {
    return this.$store?.getters['SetsService/products']
  }

  public async searchProducts (): Promise<void> {
    if (this.word.length > 2) {
      this.searchParams.word = this.word
      await this.$store.dispatch('SetsService/loadProducts', this.searchParams)
    }
  }
}
</script>

<style scoped lang="scss">
  @import "@/assets/styles/main";

  .v-setsList {
    font-family: $font-primary;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: $cl-blue2c;
    margin-top: 60px;
  }
</style>
