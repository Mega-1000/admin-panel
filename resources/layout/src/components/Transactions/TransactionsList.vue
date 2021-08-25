<template>
  <div class="c-transactionsList">
    <table class="table">
      <thead>
      <tr>
        <th>Id</th>
        <th>Data zaksięgowania w systemie</th>
        <th>Data zaksięgowania w banku</th>
        <th>Identyfikator płatności</th>
        <th>Rodzaj operacji</th>
        <th>Identyfikator zamówienia</th>
        <th>Operator płatności</th>
        <th>Wartość operacji</th>
        <th>Saldo</th>
        <th>Notatki księgowe</th>
        <th>Notatki transakcyjne</th>
        <th class="text-center">Akcje</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(transaction,index) in customer.transactions" :key="index">
        <td>{{ transaction.id }}</td>
        <td>{{ transaction.posted_in_system_date }}</td>
        <td>{{ transaction.posted_in_bank_date }}</td>
        <td>{{ transaction.payment_id }}</td>
        <td>{{ transaction.kind_of_operation }}</td>
        <td>{{ transaction.order_id }}</td>
        <td>{{ transaction.operator }}</td>
        <td>{{ transaction.operation_value }}</td>
        <td>{{ transaction.balance }}</td>
        <td>{{ transaction.accounting_notes }}</td>
        <td>{{ transaction.transaction_notes }}</td>
        <td class="text-center">
          <button class="btn btn-primary" href="#">
            <span class="hidden-xs hidden-sm">Modyfikuj</span>
          </button>
          <button class="btn btn-danger" href="#">
            <span class="hidden-xs hidden-sm">Usuń</span>
          </button>
        </td>
      </tr>
      </tbody>
    </table>
    <debugger :keepAlive="true" :components="$children"></debugger>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { Customer } from '@/types/TransactionsTypes'

@Component({
  components: {}
})
export default class TransactionsList extends Vue {
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
