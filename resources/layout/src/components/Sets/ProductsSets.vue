<template>
  <div class="c-productsSets" v-if="sets">
    <div v-for="(item, index) in sets" :key="index" :productId="item.set.product_id">
      <div v-if="Number(productId) === Number(item.set.product_id)">
        <p class="title">Produkty w 1 sztuce zestawu: </p>
        <div v-for="product in item.products" :key="product.id" class="list">
          <img class="image" :src="product.url_for_website">
          {{ product.name }} <span class="count">sztuk: {{ product.stock }}</span>
        </div>
      </div>
    </div>
    <debugger :keepAlive="true" :components="$children"></debugger>
  </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import { Set } from '@/types/SetsTypes'

@Component({
  components: {
  }
})
export default class ProductsSets extends Vue {
  @Prop() public productId!: number

  public get sets (): Set[] {
    return this.$store?.getters['SetsService/sets']
  }

  public async mounted (): Promise<void> {
    console.log('Produkt ID: ' + this.productId)
    await this.$store?.dispatch('SetsService/loadSets')
    console.log(this.sets)
  }

  public get error (): string {
    return this.$store?.getters['SetsService/error']
  }
}
</script>

<style scoped lang="scss">
  @import "@/assets/styles/main";

  .list {
    width: 80%;
    margin-left: 20%;
  }

  .title {
    font-size: 18px;
    font-weight: 500;
  }

  .count {
    font-weight: 600;
  }

  .image {
    width: 179px;
    height: 130px;
  }
</style>
