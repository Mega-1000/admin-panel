<template>
  <div class="c-setProductsList" v-if="set">
    <table class="table" v-if="products.length > 0">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nazwa</th>
          <th>Ilość produktu w zestawie</th>
          <th>Akcje</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(product, index) in products" :key="index">
          <td>
            {{ product.id }}
          </td>
          <td>
            {{ product.symbol }} => {{ product.name }}
          </td>
          <td>
              <div class="form-group">
                <input type="number" min="1" class="form-control" id="stock" name="stock" v-model="productsStock[product.id]">
              </div>
              <button id="store__packet" class="btn btn-primary" @click="updateProduct(product.id)">Zmień</button>
          </td>
          <td>
            <button class="btn btn-sm btn-danger" @click="deleteProduct(product.id)">
              <i class="voyager-trash"></i>
              <span class="hidden-xs hidden-sm">Usuń</span>
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import { Set, SetProduct, SetProductParams } from '@/types/SetsTypes'

@Component({
  components: {
  }
})
export default class SetProductsList extends Vue {
  public productsStock: number[] = [];

  private get set (): Set {
    return this.$store?.getters['SetsService/set']
  }

  public get products (): SetProduct[] {
    return this.set.products
  }

  public async updateProduct (id: number): Promise<void> {
    const params: SetProductParams = {
      id: id,
      setId: this.set.set.id,
      stock: this.productsStock[id]
    }
    await this.$store?.dispatch('SetsService/updateSetProduct', params)
    await this.$emit('load-set')
  }

  public async deleteProduct (id: number): Promise<void> {
    const params: SetProductParams = {
      id: id,
      setId: this.set.set.id,
      stock: 0
    }
    await this.$store?.dispatch('SetsService/deleteSetProduct', params)
    await this.$emit('load-set')
  }

  @Watch('set')
  private setOldValues (): void {
    if (this.products.length > 0) {
      this.products.map((product) => {
        this.productsStock[product.id] = product.stock
      })
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
