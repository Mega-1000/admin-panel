<template>
  <div class="c-setEditForm" v-if="set">
    <div class="product_stocks-general" id="general">
      <div class="form-group">
        <label for="name">Nazwa</label>
        <input type="text" class="form-control" id="name" name="name" v-model="setName">
      </div>
      <div class="form-group">
        <label for="number">Symbol</label>
        <input type="text" class="form-control" id="number" name="number" v-model="setSymbol">
      </div>
    </div>
    <button id="store__packet" class="btn btn-primary" @click="updateSet()">Zapisz</button>
  </div>
</template>
<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import { Set, SetItem, SetParams } from '@/types/SetsTypes'

@Component({
  components: {
  }
})
export default class EditForm extends Vue {
  public setName = ''
  public setSymbol = ''

  private get set (): Set {
    return this.$store?.getters['SetsService/set']
  }

  public get setItem (): SetItem {
    return this.set.set
  }

  public async updateSet (): Promise<void> {
    const params: SetParams = {
      id: this.setItem.id,
      name: this.setName,
      number: this.setSymbol
    }
    await this.$store?.dispatch('SetsService/updateSet', params)
    await this.$emit('load-set')
  }

  public mounted (): void {
    if (this.set) {
      this.setName = this.setItem.name
      this.setSymbol = this.setItem.number
    }
  }

  @Watch('set')
  private setOldValues (): void {
    this.setName = this.setItem.name
    this.setSymbol = this.setItem.number
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
