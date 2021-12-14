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
            <tbody>
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
    <debugger :keepAlive="true" :components="$children"></debugger>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { Customer } from '@/types/TransactionsTypes'

@Component({
  components: {}
})
export default class CustomersList extends Vue {
  public searchedAllegroNick = ''

  public searchedPhoneNumber = ''

  public searchedEmail = ''

  public searchedNIP = ''

  public currentSort = 'id'
  public currentSortDir = 'asc'

  public get customers (): Customer[] {
    let customers: Customer[] = this.$store?.getters['TransactionsService/customers']

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

  private async setCustomer (customer: Customer): Promise<void> {
    localStorage.setItem('customer', JSON.stringify(customer))
    await this.$store?.dispatch('TransactionsService/setCustomer', customer)
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

</style>
