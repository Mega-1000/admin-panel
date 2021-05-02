<template>
  <div class="v-setsList">
    <a class="btn btn-success" id="create__button" @click="toggleShowAddModal()">Stwórz</a>
    <AddSetModal v-if="showAddModal" @close="toggleShowAddModal()"></AddSetModal>
    <table class="table">
      <thead>
      <tr>
        <th>Id</th>
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
          <td> {{ item.set[0].name }} </td>
          <td> {{ item.set[0].number }} </td>
          <td> {{ item.set[0].stock }} </td>
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
              <span class="hidden-xs hidden-sm" @click="completing(index, completingSet[index])">Stwórz</span>
            </button>
          </td>
          <td>
            <div class="form-group">
              <label>Ilość zestawów</label>
              <input type="number" class="form-control"  name="number" min="1" v-model="disassemblySet[index]">
            </div>
            <button class="btn btn-sm btn-primary" type="submit">
              <span class="hidden-xs hidden-sm" @click="disassembly(index, disassemblySet[index])">Zdekompletuj</span>
            </button>
          </td>
          <td>
            <a class="btn btn-sm btn-primary" :href="getSetEditLink(index)">
              <i class="voyager-trash"></i>
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
import { Component, Vue } from 'vue-property-decorator'
import { Set } from '@/types/SetsTypes'
import { getFullUrl } from '@/helpers/urls'
import AddSetModal from '@/components/Sets/AddSetModal.vue'

@Component({
  components: {
    AddSetModal
  }
})
export default class SetsList extends Vue {
  public completingSet: number[] = []

  public disassemblySet: number[] = []

  public showAddModal = false

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

  public async mounted (): Promise<void> {
    await this.$store?.dispatch('SetsService/loadSets')
  }

  public async completing (setId: number, count: number): Promise<void> {
    await this.$store.dispatch('SetsService/completing', { setId: setId, count: count })
    await this.loadSets()
  }

  public async disassembly (setId: number, count: number): Promise<void> {
    await this.$store.dispatch('SetsService/disassembly', { setId: setId, count: count })
    await this.loadSets()
  }

  public async deleteSet (setId: number): Promise<void> {
    await this.$store.dispatch('SetsService/delete', setId)
    await this.loadSets()
  }

  private async loadSets (): Promise<void> {
    await this.$store?.dispatch('SetsService/loadSets')
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
