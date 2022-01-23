<template>
  <div class="c-customersList ">
    <div class="row">
      <div class="col-sm-5">
        <h4>Formularz wyszukiwania</h4>
      </div>
      <div class="col-sm-7 buttons">
        <button class="btn btn-success float-right ml-2" @click="$emit('add')"><i class="voyager-plus"></i> Dodaj
          Transakcje
        </button>
        <button class="btn btn-secondary float-right" @click="$emit('import')"><i class="voyager-file-text"></i>
          Import
        </button>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="allegroNick" class="col-sm-4">Nick allegro</label>
          <div class="col-sm-6">
            <input type="text" class="form-control" id="allegroNick" v-model="searchedAllegroNick">
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="phoneNumber" class="col-sm-4">Numer telefonu</label>
          <div class="col-sm-6">
            <input type="text" class="form-control" id="phoneNumber" v-model="searchedPhoneNumber">
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="nip" class="col-sm-4">NIP</label>
          <div class="col-sm-6">
            <input type="text" class="form-control" id="nip" v-model="searchedNIP">
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="email" class="col-sm-4">Adres email</label>
          <div class="col-sm-6">
            <input type="email" class="form-control" id="email" v-model="searchedEmail">
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <button class="btn btn-primary" @click="search">
          Wyszukaj
        </button>
        <button class="btn btn-secondary" @click="reset">
          Wyczyść
        </button>
      </div>
    </div>
    <div class="row table-customers">
      <div class="col-md-12">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
            <tr>
              <th scope="col" style="width: 5%;">Id</th>
              <th scope="col" style="width: 15%;">Login</th>
              <th scope="col" style="width: 10%;">Nick allegro</th>
              <th scope="col" style="width: 10%;">Imię</th>
              <th scope="col" style="width: 10%;">Nazwisko</th>
              <th scope="col" style="width: 15%;">Nazwa firmy</th>
              <th scope="col" style="width: 10%;">Numer telefonu</th>
              <th scope="col" style="width: 15%;">Adres</th>
              <th scope="col" style="width: 10%;" class="text-center">Akcje</th>
            </tr>
            </thead>
            <tbody v-if="isLoading">
            <div class="loader">Loading...</div>
            </tbody>
            <tbody v-else>
            <tr v-for="(customer,index) in customers" :key="index">
              <td style="width: 5%;">{{ customer.id }}</td>
              <td style="width: 15%;">{{ customer.login }}</td>
              <td style="width: 10%;">{{ customer.nickAllegro }}</td>
              <td style="width: 10%;">{{ customer.firstName }}</td>
              <td style="width: 10%;">{{ customer.lastName }}</td>
              <td style="width: 15%;">{{ customer.firmName }}</td>
              <td style="width: 10%;">{{ customer.phone }}</td>
              <td style="width: 15%;">{{ customer.address }}</td>
              <td class="text-center" style="width: 10%;">
                <button class="btn btn-primary" @click="setCustomer(customer)">
                  <span>Transakcje</span>
                </button>
              </td>
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
    <debugger :keepAlive="true" :components="$children"></debugger>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { Customer, searchCustomersParams } from '@/types/TransactionsTypes'

@Component({
  components: {}
})
export default class CustomersList extends Vue {
  public searchedAllegroNick = ''

  public searchedPhoneNumber = ''

  public searchedEmail = ''

  public searchedNIP = ''

  public page = 1

  public currentSort = 'id'
  public currentSortDir = 'asc'

  public get customers (): Customer[] {
    let customers: Customer[] = this.$store?.getters['TransactionsService/customers']
    if (customers === undefined) {
      return []
    }
    if (this.searchedAllegroNick !== '') {
      customers = customers.filter((customer) => {
        return (customer.nickAllegro !== null && customer.nickAllegro.toLowerCase().search(this.searchedAllegroNick.toLowerCase()) > -1)
      })
    }

    if (this.searchedPhoneNumber !== '') {
      customers = customers.filter((customer) => {
        return (customer.phone !== null && customer.phone.toLowerCase().search(this.searchedPhoneNumber.toLowerCase()) > -1)
      })
    }

    if (this.searchedEmail !== '') {
      customers = customers.filter((customer) => {
        return (customer.email !== null && customer.email.toLowerCase().search(this.searchedEmail.toLowerCase()) > -1)
      })
    }

    if (this.searchedNIP !== '') {
      customers = customers.filter((customer) => {
        return (customer.nip !== null && customer.nip.toLowerCase().search(this.searchedNIP.toLowerCase()) > -1)
      })
    }
    return customers
  }

  private async reset (): Promise<void> {
    this.searchedNIP = this.searchedAllegroNick = this.searchedEmail = this.searchedPhoneNumber = ''
    await this.search()
  }

  private async search (): Promise<void> {
    const params: searchCustomersParams = {
      page: '1',
      nip: this.searchedNIP,
      nickAllegro: this.searchedAllegroNick,
      email: this.searchedEmail,
      phone: this.searchedPhoneNumber
    }
    this.page = 1
    await this.$store?.dispatch('TransactionsService/loadTransactions', params)
  }

  private async setCustomer (customer: Customer): Promise<void> {
    localStorage.setItem('customer', JSON.stringify(customer))
    await this.$store?.dispatch('TransactionsService/setCustomer', customer)
  }

  public get pages (): number[] {
    return Array(this.$store?.getters['TransactionsService/pageCount']).fill(0).map((e, i) => i + 1)
  }

  public get isLoading (): boolean {
    return this.$store?.getters['TransactionsService/isLoading']
  }

  private async changePage (pageNumber: number): Promise<void> {
    this.page = pageNumber
    const params: searchCustomersParams = {
      page: String(this.page),
      nip: this.searchedNIP,
      nickAllegro: this.searchedAllegroNick,
      email: this.searchedEmail,
      phone: this.searchedPhoneNumber
    }
    await this.$store?.dispatch('TransactionsService/loadTransactions', params)
  }
}
</script>
<style scoped lang="scss">
  @import "@/assets/styles/main";

  .buttons {
    button {
      margin: 0.25rem 0.125rem;
    }
  }

  tr {
    width: 100%;
    display: inline-table;
    table-layout: fixed;
  }

  table {
    height: 550px; // <-- Select the height of the table
    display: block;
  }

  tbody {
    overflow-y: scroll;
    height: 500px; //  <-- Select the height of the body
    width: 98.3%;
    position: absolute;
  }

  .loader,
  .loader::before,
  .loader::after {
    border-radius: 50%;
  }

  .loader {
    color: $cl-whiteff;
    font-size: 11px;
    text-indent: -99999em;
    margin: 55px auto;
    position: relative;
    width: 10em;
    height: 10em;
    box-shadow: inset 0 0 0 1em;
    -webkit-transform: translateZ(0);
    -ms-transform: translateZ(0);
    transform: translateZ(0);
  }

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
