<template>
  <div class="v-setEdit">
    <a class="btn btn-info" :href="getSetsLink">Powrót do listy zestawów</a>
    <div class="error" v-if="message">
      <span @click="close()" class="close">X</span>
      <p>{{ message }}</p>
    </div>
    <EditForm @load-set="loadSet()"></EditForm>
    <SetProductsList @load-set="loadSet()"></SetProductsList>
    <ProductTable @load-set="loadSet()"></ProductTable>
  </div>
</template>
<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import ProductTable from '@/components/Sets/ProductTable.vue'
import EditForm from '@/components/Sets/EditForm.vue'
import SetProductsList from '@/components/Sets/SetProductsList.vue'
import { getFullUrl } from '@/helpers/urls'

@Component({
  components: {
    SetProductsList,
    EditForm,
    ProductTable
  }
})
export default class SetEdit extends Vue {
  public message = ''

  public async mounted (): Promise<void> {
    await this.loadSet()
  }

  public async loadSet (): Promise<void> {
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-ignore: Object is possibly 'null'.
    const id = window.location.pathname.split('/')[4] ?? 0
    if (id) {
      await this.$store?.dispatch('SetsService/loadSet', Number(id))
      this.message = this.error
    }
  }

  public get getSetsLink (): string {
    return getFullUrl('admin/products/sets')
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

  .btn-info {
    margin-bottom: 25px;
  }
</style>
