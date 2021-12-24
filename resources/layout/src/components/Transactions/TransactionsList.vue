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
    <div class="row table-transactions">
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
              <th scope="col">Saldo po operacji</th>
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
              <td>
                <a :href="getOrderLink(transaction.order_id)" target="_blank">
                  <span>{{ transaction.order_id }}</span>
                </a>
              </td>
              <td>{{ transaction.operator }}</td>
              <td :class="[{'text-success': transaction.operation_value>0}, {'text-danger': transaction.operation_value<0}]">
                {{ transaction.operation_value.toString().replace('-', '') }}
              </td>
              <td :class="[{'text-success': transaction.balance>0}, {'text-danger': transaction.balance<0}]">
                {{ transaction.balance.toString().replace('-', '') }}
              </td>
              <td>{{ transaction.accounting_notes }}</td>
              <td>{{ transaction.transaction_notes }}</td>
              <td class="text-center">
                <button @click="edit(transaction)" class="btn btn-primary" href="#">
                  <span>Modyfikuj</span>
                </button>
                <button @click="destroy(transaction)" class="btn btn-danger" href="#">
                  <span>Usuń</span>
                </button>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <button class="btn btn-light" @click="$emit('back')">Powrót</button>
      </div>
    </div>
    <debugger :keepAlive="true" :components="$children"></debugger>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { Customer, Transaction } from '@/types/TransactionsTypes'
import { getFullUrl } from '@/helpers/urls'

@Component({
  components: {}
})
export default class TransactionsList extends Vue {
  public get customer (): Customer {
    return this.$store?.getters['TransactionsService/customer']
  }

  public async destroy (transaction: Transaction): Promise<void> {
    await this.$store?.dispatch('TransactionsService/delete', transaction)
  }

  public async edit (transaction: any): Promise<void> {
    const editedTransaction: Transaction = {
      accountingNotes: transaction.accounting_notes,
      balance: transaction.balance,
      id: transaction.id,
      operationKind: transaction.kind_of_operation,
      operationValue: transaction.operation_value,
      operator: transaction.operator,
      orderId: transaction.order_id,
      paymentId: transaction.payment_id,
      registrationInBankDate: transaction.posted_in_bank_date,
      registrationInSystemDate: transaction.posted_in_system_date,
      transactionNotes: transaction.transaction_notes
    }
    await this.$store?.dispatch('TransactionsService/setTransaction', editedTransaction)
    this.$emit('edit')
  }

  public getOrderLink (orderID: string): string {
    return getFullUrl('admin/orders/' + orderID + '/edit')
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

  .table-transactions {
    max-height: 550px;
    overflow-y: auto;
  }

  td {
    margin: 1rem 2rem;

    a {
      --color: #646b8c;

      position: relative;
      text-decoration: none;
      color: var(--color);
      font-family: "Inter", sans-serif;
      padding: 0.2rem 0;

      &::before {
        --line-width: 115%;
        --line-height: 1px;
        --line-easing: ease;
        --line-transition-duration: 300ms;

        content: "";
        position: absolute;
        left: 0;
        bottom: 0;
        width: var(--line-width);
        height: var(--line-height);
        transform-origin: right;
        transform: scaleX(0);
        background: var(--color);
        transition: transform var(--line-transition-duration) var(--line-easing);
        z-index: $index-addSetModalContent;
      }

      &:hover {
        &::before {
          transform-origin: left;
          transform: scaleX(1);
        }

        span {
          --deg: -45deg;

          &::before {
            transform: rotate(var(--deg));
          }

          &::after {
            transform: translateX(-1px) rotate(var(--deg));
          }
        }
      }

      span {
        --line-arrow-width: 1px;
        --line-arrow-height: 6px;
        --line-arrow-easing: cubic-bezier(0.3, 1.5, 0.5, 1);
        --line-arrow-transition-duration: 200ms;
        --line-arrow-transition-delay: 240ms;

        &::before,
        &::after {
          content: "";
          position: absolute;
          right: -18%;
          bottom: 0;
          background: var(--color);
          transition: transform var(--line-arrow-transition-duration) var(--line-arrow-easing);
          transition-delay: var(--line-arrow-transition-delay);
          z-index: $index-2;
        }

        &::before {
          width: var(--line-arrow-width);
          height: var(--line-arrow-height);
          transform-origin: 0% 100%;
          transform: rotate(-90deg);
        }

        &::after {
          height: var(--line-arrow-width);
          width: var(--line-arrow-height);
          transform-origin: 100% 0%;
          transform: translateX(-1px) rotate(0deg);
        }
      }
    }
  }

</style>
