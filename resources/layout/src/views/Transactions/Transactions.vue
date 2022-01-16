<template>
  <div class="v-transactions">
    <customers-list v-if="customer==null && !transactionForm" @add="transactionForm = true"
                    @import="toggleShowModal"></customers-list>
    <transactions-list @back="back" @add="transactionForm = true" @edit="edit"
                       v-if="customer !== null && !transactionForm"></transactions-list>
    <transactions-form v-if="transactionForm" @transactionAdded="transactionAdded"
                       @back="transactionForm=false"></transactions-form>
    <file-uploader :kinds="importKinds" v-if="showImportModal"
                   @close="toggleShowModal()">
      <template v-slot:header>Import transakcji</template>
    </file-uploader>
    <debugger :keepAlive="true" :components="$children"></debugger>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { Customer, searchCustomersParams } from '@/types/TransactionsTypes'
import CustomersList from '@/components/Transactions/CustomersList.vue'
import TransactionsList from '@/components/Transactions/TransactionsList.vue'
import TransactionsForm from '@/components/Transactions/TransactionsForm.vue'
import FileUploader from '@/components/common/FileUploader.vue'

@Component({
  components: { FileUploader, TransactionsForm, TransactionsList, CustomersList }
})
export default class Transactions extends Vue {
  public transactionForm = false
  private importKinds = {
    allegroPayIn: 'Wpłaty allegro',
    bankPayIn: 'Wpłaty bankowe'
  }

  private showImportModal = false

  public async mounted (): Promise<void> {
    await this.load()
  }

  public get customers (): Customer[] {
    return this.$store?.getters['TransactionsService/customers']
  }

  public get customer (): Customer {
    return this.$store?.getters['TransactionsService/customer']
  }

  public async back (): Promise<void> {
    await this.$store?.dispatch('TransactionsService/setCustomer', null)
    await this.$store?.dispatch('TransactionsService/setTransaction', null)
    localStorage.removeItem('customer')
    await this.load()
  }

  public async edit (): Promise<void> {
    this.transactionForm = true
  }

  public async transactionAdded (): Promise<void> {
    this.transactionForm = false
    localStorage.removeItem('customer')
    await this.load()
    if (this.customer !== null) {
      const customer = this.customers.filter((item) => {
        return this.customer.id === item.id
      })[0]
      await this.$store?.dispatch('TransactionsService/setCustomer', customer)
      localStorage.setItem('customer', JSON.stringify(customer))
    }
  }

  public async load (): Promise<void> {
    if (localStorage.getItem('customer') !== null) {
      const customer = localStorage.getItem('customer')
      await this.$store?.dispatch('TransactionsService/setCustomer', JSON.parse(customer ?? ''))
    } else {
      const params: searchCustomersParams = {
        page: '1',
        nip: '',
        nickAllegro: '',
        email: '',
        phone: ''
      }
      await this.$store?.dispatch('TransactionsService/loadTransactions', params)
    }
  }

  public toggleShowModal (): void {
    this.showImportModal = !this.showImportModal
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
