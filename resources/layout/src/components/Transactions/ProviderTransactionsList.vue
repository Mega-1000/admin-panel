<template>
  <div class="c-providerTransactionsList">
    <div class="row">
      <div class="col-md-10">
        <h4>Transakcje firm spedycyjnych</h4>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group"
             :class="[{'has-error': provider.error===true},{'has-success': provider.error===false && provider.value !== ''}]">
          <label for="provider" class="col-md-5 col-form-label">Dostawca</label>
          <div class="col-md-5">
            <select v-model="provider.value" required @change="change"
                    id="provider" class="form-control">
              <option selected value="">-- wybierz --</option>
              <option v-for="(provider,index) in providers" :key="index" :value="provider">
                {{ provider }}
              </option>
            </select>
            <span class="form-control-feedback">{{ provider.errorMessage }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-6" v-if="provider.value !== ''">
        <div class="d-flex justify-content-between">
          <span>Saldo:</span> {{ balance }}
        </div>
      </div>
    </div>
    <div class="row table-provider-transactions">
      <div class="col-md-12">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
            <tr>
              <th>Id</th>
              <th>Dostawca</th>
              <th>Numer listu przewozowego</th>
              <th>Numer faktury</th>
              <th>Identyfikator zamówienia</th>
              <th>Wartość pobrania</th>
              <th>Saldo dostawcy po operacji</th>
              <th>Saldo dostawcy na fakturze</th>
              <th>Transakcja</th>
            </tr>
            </thead>
            <tbody v-if="isLoading">
            <div class="loader">Loading...</div>
            </tbody>
            <tbody v-else>
            <tr v-for="(transaction,index) in transactions" :key="index">
              <td class="text-black-50">{{ transaction.id }}</td>
              <td>{{ transaction.provider }}</td>
              <td>{{ transaction.waybillNumber }}</td>
              <td>{{ transaction.invoiceNumber }}</td>
              <td>
                <a :href="getOrderLink(transaction.orderId)" target="_blank">
                  <span>{{ transaction.orderId }}</span>
                </a>
              </td>
              <td
                :class="[{'text-success': transaction.cashOnDelivery>0}, {'text-danger': transaction.cash_on_delivery<0}]">
                {{ transaction.cashOnDelivery.toString().replace('-', '') }}
              </td>
              <td
                :class="[{'text-success': transaction.providerBalance>0}, {'text-danger': transaction.provider_balance<0}]">
                {{ transaction.providerBalance.toString().replace('-', '') }}
              </td>
              <td
                :class="[{'text-success': transaction.providerBalanceOnInvoice>0}, {'text-danger': transaction.provider_balance_on_invoice<0}]">
                {{ transaction.providerBalanceOnInvoice.toString().replace('-', '') }}
              </td>
              <td>{{ transaction.transactionId }}</td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 float-right">
        <nav aria-label="Page navigation">
          <ul class="pagination">
            <li class="page-item">
              <a type="button" class="page-link" v-if="page !== 1" @click="changePage(--page)"> Poprzednia</a>
            </li>
            <li class="page-item" v-for="(pageNumber, index) in pages.slice(page-1, page+5)"
                :key="index" :class="{'active':pageNumber===page}">
              <a type="button" class="page-link"
                 @click="changePage(pageNumber)"> {{ pageNumber }}
              </a>
            </li>
            <li class="page-item">
              <a type="button" @click="changePage(++page)" v-if="page < pages.length" class="page-link"> Następna</a>
            </li>
          </ul>
        </nav>
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
import {
  ProviderTransactions,
  searchProvidersTransactionsParams
} from '@/types/TransactionsTypes'
import { getFullUrl } from '@/helpers/urls'

@Component({
  components: {}
})
export default class ProviderTransactionsList extends Vue {
  public page = 1

  private providers = {
    DPD: 'DPD',
    GLS: 'GLS',
    INPOST_KURIER: 'INPOST KURIER',
    INPOST_PACZKOMAT: 'INPOST PACZKOMAT',
    ALLEGRO_PACZKOMATY_INPOST: 'ALLEGRO PACZKOMATY INPOST',
    ALLEGRO_DPD: 'ALLEGRODPD',
    POCZTA_POLSKA: 'POCZTA POLSKA'
  }

  private provider = {
    value: '',
    error: false,
    errorMessage: ''
  }

  public get isLoading (): boolean {
    return this.$store?.getters['TransactionsService/isLoading']
  }

  public get transactions (): ProviderTransactions[] {
    return this.$store?.getters['TransactionsService/providers'].transactions
  }

  public get balance (): number {
    if (this.transactions.length === 0) {
      return 0
    } else {
      return this.transactions[this.transactions.length - 1].providerBalance
    }
  }

  public get pages (): number[] {
    return Array(this.$store?.getters['TransactionsService/providers'].pageCount).fill(0).map((e, i) => i + 1)
  }

  private async changePage (pageNumber: number): Promise<void> {
    this.page = pageNumber
    const params: searchProvidersTransactionsParams = {
      page: String(pageNumber),
      provider: this.provider.value
    }
    await this.$store?.dispatch('TransactionsService/loadProvidersTransactions', params)
  }

  public getOrderLink (orderID: string): string {
    return getFullUrl('admin/orders/' + orderID + '/edit')
  }

  public async change (): Promise<void> {
    const params: searchProvidersTransactionsParams = {
      page: '1',
      provider: this.provider.value
    }
    this.page = 1
    await this.$store?.dispatch('TransactionsService/loadProvidersTransactions', params)
  }

  public async mounted (): Promise<void> {
    const params: searchProvidersTransactionsParams = {
      page: '1',
      provider: this.provider.value
    }
    this.page = 1
    await this.$store?.dispatch('TransactionsService/loadProvidersTransactions', params)
  }
}
</script>

<style scoped lang="scss">
  @import "@/assets/styles/main";

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

  .loader,
  .loader::before,
  .loader::after {
    position: absolute;
    content: '';
  }

  .loader::before {
    width: 5.2em;
    height: 10.2em;
    background: $cl-blue2c;
    border-radius: 10.2em 0 0 10.2em;
    top: -0.1em;
    left: -0.1em;
    -webkit-transform-origin: 5.1em 5.1em;
    transform-origin: 5.1em 5.1em;
    -webkit-animation: load2 2s infinite ease 1.5s;
    animation: load2 2s infinite ease 1.5s;
  }

  .loader::after {
    width: 5.2em;
    height: 10.2em;
    background: $cl-blue2c;
    border-radius: 0 10.2em 10.2em 0;
    top: -0.1em;
    left: 4.9em;
    -webkit-transform-origin: 0.1em 5.1em;
    transform-origin: 0.1em 5.1em;
    -webkit-animation: load2 2s infinite ease;
    animation: load2 2s infinite ease;
  }

  @-webkit-keyframes load2 {
    0% {
      -webkit-transform: rotate(0deg);
      transform: rotate(0deg);
    }

    100% {
      -webkit-transform: rotate(360deg);
      transform: rotate(360deg);
    }
  }

  @keyframes load2 {
    0% {
      -webkit-transform: rotate(0deg);
      transform: rotate(0deg);
    }

    100% {
      -webkit-transform: rotate(360deg);
      transform: rotate(360deg);
    }
  }
</style>
