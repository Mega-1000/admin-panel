<template>
  <div class="v-transactions">
    <customers-list></customers-list>
    <transactions-list v-if="customer !== null"></transactions-list>
    <debugger :keepAlive="true" :components="$children"></debugger>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { Customer } from '@/types/TransactionsTypes'
import CustomersList from '@/components/Transactions/CustomersList.vue'
import TransactionsList from '@/components/Transactions/TransactionsList.vue'

@Component({
  components: { TransactionsList, CustomersList }
})
export default class Transactions extends Vue {
  public async mounted (): Promise<void> {
    await this.$store?.dispatch('TransactionsService/loadTransactions')
  }

  public get customers (): Customer[] {
    return this.$store?.getters['TransactionsService/customers']
  }

  public get customer (): Customer {
    return this.$store?.getters['TransactionsService/customer']
  }
}
</script>

<style scoped lang="scss">
  @import "@/assets/styles/main";

  .voyager-pen,
  .voyager-trash {
    @media (min-width: 992px) {
      margin-right: 7px;
    }
  }

  .voyager-double-down,
  .voyager-double-up {
    @media (min-width: 992px) {
      margin-right: 4px;
    }
  }
</style>
