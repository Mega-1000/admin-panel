<template>
  <div class="v-setsList">
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

      </tbody>
    </table>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import SetsRepository from '@/repositories/SetsRepository'
import { Set } from '@/types/SetsTypes'

@Component({
  components: {
  }
})
export default class SetsList extends Vue {
  public async getSets (): Promise<Set[]> {
    const sets = await SetsRepository.getSets() ?? []
    return sets
  }

  public mounted (): void {
    console.log('MUST LOGIN')
    this.getSets()
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
