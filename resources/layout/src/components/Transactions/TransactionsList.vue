<template>
  <div class="c-transactionsList">
    <div class="row">
      <div class="col-md-10">
        <h4>Transakcje klienta</h4>
      </div>
      <div class="col-sm-2">
        <button class="btn btn-success float-right" @click="$emit('add')"><i class="voyager-plus"></i> Dodaj
          Transakcje
        </button>
      </div>
    </div>
    <div class="row customer_data px-2">
      <div class="col-md-6">
        <div class="d-flex justify-content-between">
          <span>Login:</span> {{ customer.login }}
        </div>
        <div>
          <span>Nick allegro:</span> {{ customer.nickAllegro }}
        </div>
        <div>
          <span>Imię:</span> {{ customer.firstName }}
        </div>
        <div>
          <span>Nazwisko:</span> {{ customer.lastName }}
        </div>
        <div>
          <span>Adres:</span> {{ customer.address }}
        </div>
      </div>
      <div class="col-md-6">
        <div v-if="customer.firmName !== null">
          <span>Nazwa firmy:</span> {{ customer.firmName }}
        </div>
        <div v-if="customer.nip !== null">
          <span>NIP:</span> {{ customer.nip }}
        </div>
        <div>
          <span>Numer telefonu:</span> {{ customer.phone }}
        </div>
        <div>
          <span>Email:</span> {{ customer.email }}
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
            <tr>
              <th scope="col">Id</th>
              <th scope="col">Data zaksięgowania w systemie</th>
              <th scope="col">Data zaksięgowania w banku</th>
              <th scope="col">Identyfikator płatności</th>
              <th scope="col">Rodzaj operacji</th>
              <th scope="col">Identyfikator zamówienia</th>
              <th scope="col">Operator płatności</th>
              <th scope="col">Wartość operacji</th>
              <th scope="col">Saldo</th>
              <th scope="col">Notatki księgowe</th>
              <th scope="col">Notatki transakcyjne</th>
              <th scope="col" class="text-center">Akcje</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(transaction,index) in customer.transactions" :key="index">
              <td class="text-black-50">{{ transaction.id }}</td>
              <td>{{ transaction.posted_in_system_date }}</td>
              <td>{{ transaction.posted_in_bank_date }}</td>
              <td>{{ transaction.payment_id }}</td>
              <td>{{ transaction.kind_of_operation }}</td>
              <td>{{ transaction.order_id }}</td>
              <td>{{ transaction.operator }}</td>
              <td :class="[{'text-success': transaction.operation_value>0}, {'text-danger': transaction.operation_value<0}]">
                {{ transaction.operation_value }}
              </td>
              <td>{{ transaction.balance }}</td>
              <td>{{ transaction.accounting_notes }}</td>
              <td>{{ transaction.transaction_notes }}</td>
              <td class="text-center">
                <button class="btn btn-primary" href="#">
                  <span>Modyfikuj</span>
                </button>
                <button class="btn btn-danger" href="#">
                  <span>Usuń</span>
                </button>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
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

  .customer_data {
    & > div > div {
      margin-left: 0.1rem;
      margin-top: 0.5rem;
      margin-bottom: 0.5rem;
      border-bottom: 1px dotted;
      font-size: 16px;

      span {
        width: 30%;
        display: inline-block;
        font-weight: 600;
      }
    }
  }
</style>
