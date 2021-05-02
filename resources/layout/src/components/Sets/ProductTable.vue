<template>
  <div class="c-productTable">
    <div class="form-group">
      <label>Wyszukaj produkct aby dodać do zestawu</label>
      <input type="text" class="form-control" v-on:keyup="searchProducts()" v-model="word">
    </div>
    <table id="dataTable" class="table table-hover">
      <thead>
      <tr>
        <th>ID</th>
        <th>
          Nazwa produktu
          <input v-if="existProduct" type="text" placeholder="Filtruj wyniki po nazwie" v-model="name">
        </th>
        <th>
          Symbol
          <input v-if="existProduct" type="text" placeholder="Filtruj wyniki po symbolu" v-model="symbol">
        </th>
        <th>
          Producent
          <input v-if="existProduct" type="text" placeholder="Filtruj wyniki po producencie" v-model="manufacturer">
        </th>
        <th>Akcja</th>
      </tr>
      </thead>
      <tbody id="productTable" v-if="existProduct">
      <tr v-for="(product, index) in filterProducts" :key="index">
        <td>{{ index }}</td>
        <td>{{ product.name }}</td>
        <td>{{ product.symbol }}</td>
        <td>{{ product.manufacturer }}</td>
        <td>
          <div class="form-group" v-if="!checkExistProduct(product.id)">
            <label>Ilość w zestawie</label>
            <input type="number" class="form-control" min="1" value="1" v-model="productCount[product.id]">
            <button class="btn btn-sm btn-primary" type="submit">
              <span class="hidden-xs hidden-sm" @click="addProduct(product.id)">Dodaj</span>
            </button>
          </div>
          <span v-else> Produkt już został dodany </span>
        </td>
      </tr>
      </tbody>
    </table>
  </div>
</template>
<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { SetProduct, SetsProductParams, Set, SetProductParams } from '@/types/SetsTypes'

@Component({
  components: {
  }
})
export default class ProductTable extends Vue {
  public word = ''
  public name = ''
  public symbol = ''
  public manufacturer = ''
  public productCount: number[] = []
  private url = ''

  public searchParams: SetsProductParams = {
    name: '',
    symbol: '',
    manufacturer: '',
    word: ''
  };

  public get products (): SetProduct[] {
    return this.$store?.getters['SetsService/products']
  }

  public get set (): Set {
    return this.$store?.getters['SetsService/set']
  }

  public get existProduct (): boolean {
    return (this.products.length > 0)
  }

  public async searchProducts (): Promise<void> {
    if (this.word.length > 2) {
      this.searchParams.word = this.word
      await this.$store.dispatch('SetsService/loadProducts', this.searchParams)
    }
  }

  public get filterProducts ():SetProduct[] {
    let tempPtoducts: SetProduct[] = this.products

    if (this.name !== '') {
      tempPtoducts = tempPtoducts.filter((product) => {
        return (product.name.toLowerCase().search(this.name.toLowerCase()) > -1)
      })
    }
    if (this.symbol !== '') {
      tempPtoducts = tempPtoducts.filter((product) => {
        return (product.symbol.toLowerCase().search(this.symbol.toLowerCase()) > -1)
      })
    }
    if (this.manufacturer !== '') {
      tempPtoducts = tempPtoducts.filter((product) => {
        return (product.manufacturer.toLowerCase().search(this.manufacturer.toLowerCase()) > -1)
      })
    }
    return tempPtoducts
  }

  public async addProduct (id: number): Promise<void> {
    const params: SetProductParams = {
      id: id,
      setId: this.set.set.id,
      stock: this.productCount[id]
    }
    await this.$store?.dispatch('SetsService/addSetProduct', params)
    await this.$emit('load-set')
  }

  public checkExistProduct (id: number): boolean {
    const tempArray = this.set.products.filter((product) => {
      return (product.product_id === id)
    })

    return (tempArray.length > 0)
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
