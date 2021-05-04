<template>
  <div class="v-setsList">
    <div class="error" v-if="message">
      <span @click="close()" class="close">X</span>
      <p>{{ message }}</p>
    </div>
    <NeedStockModal v-if="needStock.length > 0" :need-stock="needStock" :set="needStockSet" :count="needCount" @close="restoreNeedStock()"></NeedStockModal>
    <a class="btn btn-success" id="create__button" @click="toggleShowAddModal()">Stwórz zestaw</a>
    <AddSetModal v-if="showAddModal" @close="toggleShowAddModal()" @load-sets="loadSets()"></AddSetModal>
    <table class="table">
      <thead>
      <tr>
        <th>Id</th>
        <th>ID produktu</th>
        <th>Nazwa zestawu</th>
        <th>Numer wewnętrzny zestawu</th>
        <th>Ilość zestawów</th>
        <th>Lista produktów w zestawie</th>
        <th>Stwórz podaną ilość zestawów</th>
        <th>Zdekompletuj podaną liczbę zestawów</th>
        <th>Akcje</th>
      </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in sets" :key="index">
          <td>{{ index }}</td>
          <td> {{ item.set.product_id }} </td>
          <td> {{ item.set.name }} </td>
          <td> {{ item.set.number }} </td>
          <td> {{ item.set.stock }} </td>
          <td>
            <ul>
              <li v-for="product in item.products" :key="product.id">
                <b>{{ product.symbol }}</b> => {{ product.name }} <b>Ilość: {{ product.stock }}</b>
              </li>
            </ul>
          </td>
          <td>
            <div class="form-group">
              <label>Ilość zestawów</label>
              <input type="number" class="form-control" name="number" min="1" v-model="completingSet[index]">
            </div>
            <button class="btn btn-sm btn-primary" type="submit">
              <i class="voyager-double-up"></i>
              <span class="hidden-xs hidden-sm" @click="completing(item, completingSet[index])">Stwórz</span>
            </button>
          </td>
          <td>
            <div class="form-group">
              <label>Ilość zestawów</label>
              <input type="number" class="form-control"  name="number" min="1" v-model="disassemblySet[index]">
            </div>
            <button class="btn btn-sm btn-primary" type="submit">
              <i class="voyager-double-down"></i>
              <span class="hidden-xs hidden-sm" @click="disassembly(index, disassemblySet[index])">Zdekompletuj</span>
            </button>
          </td>
          <td>
            <a class="btn btn-sm btn-primary" :href="getSetEditLink(index)">
              <i class="voyager-pen"></i>
              <span class="hidden-xs hidden-sm">Edytuj</span>
            </a>
            <button class="btn btn-sm btn-danger" type="submit" @click="deleteSet(index)">
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
import { ProductStocks, Set } from '@/types/SetsTypes'
import { getFullUrl } from '@/helpers/urls'
import AddSetModal from '@/components/Sets/AddSetModal.vue'
import Error from '@/components/Error.vue'
import NeedStockModal from '@/components/Sets/NeedStockModal.vue'

@Component({
  components: {
    NeedStockModal,
    Error,
    AddSetModal
  }
})
export default class SetsList extends Vue {
  public completingSet: number[] = []

  public disassemblySet: number[] = []

  public needStock: number[] = []

  public needStockSet: Set | null = null

  public needCount = 0

  public showAddModal = false

  public message = ''

  public toggleShowAddModal ():void {
    this.showAddModal = !this.showAddModal
  }

  public get sets (): Set[] {
    return this.$store?.getters['SetsService/sets']
  }

  public getSetEditLink (setId: string): string {
    return getFullUrl('admin/products/sets/' + setId + '/edytuj')
  }

  public get addSetLink (): string {
    return getFullUrl('/admin/products/sets/nowy')
  }

  private get productsStocks (): ProductStocks[] {
    return this.$store?.getters['SetsService/productsStocks']
  }

  public async mounted (): Promise<void> {
    await this.$store?.dispatch('SetsService/loadSets')
  }

  public async completing (item: Set, count: number): Promise<void> {
    if (await this.checkStock(item, count)) {
      await this.$store.dispatch('SetsService/completing', { setId: item.set.id, count: count })
      this.message = this.error
      await this.loadSets()
    }
  }

  public async disassembly (setId: number, count: number): Promise<void> {
    await this.$store.dispatch('SetsService/disassembly', { setId: setId, count: count })
    this.message = this.error
    await this.loadSets()
  }

  public async deleteSet (setId: number): Promise<void> {
    await this.$store.dispatch('SetsService/delete', setId)
    this.message = this.error
    await this.loadSets()
  }

  private async loadSets (): Promise<void> {
    await this.$store?.dispatch('SetsService/loadSets')
    this.message = this.error
  }

  private async checkStock (item: Set, count: number): Promise<boolean> {
    await this.$store.dispatch('SetsService/getProductsStocks', item.set.id)
    this.message = this.error
    this.productsStocks.forEach((item) => {
      if (item.stocks.length === 0 || item.stocks[0]?.position_quantity < count) {
        this.needStock.push(item.id)
      }
    })
    if (this.needStock.length > 0) {
      this.needStockSet = item
      this.needCount = count
    }
    return (this.needStock.length === 0)
  }

  public restoreNeedStock (): void{
    this.needStockSet = null
    this.needStock = []
    this.needCount = 0
  }

  public get error (): string {
    return this.$store?.getters['SetsService/error']
  }

  public close (): void {
    this.message = ''
  }

  @Watch('error')
  private listenError () {
    this.message = this.error
    console.log('Error: ' + this.error)
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

  .close {
    position: absolute;
    right: 10px;
    top: 10px;
  }

  .error {
    position: fixed;
    top: 0;
    right: 0;
    background: $cl-rede4;
    border-radius: 10px;
    padding: 25px 30px 20px;
    z-index: $index-alert;
  }

  .btn-success {
    float: left;
  }
</style>
