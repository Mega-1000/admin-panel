<template>
  <div class="v-setEdit">
    <Error></Error>
    <EditForm @load-set="loadSet()"></EditForm>
    <SetProductsList @load-set="loadSet()"></SetProductsList>
    <ProductTable @load-set="loadSet()"></ProductTable>
  </div>
</template>
<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import ProductTable from '@/components/Sets/ProductTable.vue'
import EditForm from '@/components/Sets/EditForm.vue'
import SetProductsList from '@/components/Sets/SetProductsList.vue'

@Component({
  components: {
    SetProductsList,
    EditForm,
    ProductTable
  }
})
export default class SetEdit extends Vue {
  public async mounted (): Promise<void> {
    await this.loadSet()
  }

  public async loadSet (): Promise<void> {
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-ignore: Object is possibly 'null'.
    const id = window.location.pathname.split('/')[4] ?? 0
    if (id) {
      await this.$store?.dispatch('SetsService/loadSet', Number(id))
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
