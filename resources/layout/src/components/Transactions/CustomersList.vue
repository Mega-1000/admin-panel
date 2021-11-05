<template>
  <div class="c-customersList ">
    <div class="row">
      <div class="col-sm-10">
        <h4>Formularz wyszukiwania</h4>
      </div>
      <div class="col-sm-2">
        <button class="btn btn-success float-right" @click="$emit('add')"><i class="voyager-plus"></i> Dodaj
          Transakcje
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
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
            <tr>
              <th scope="col">Id</th>
              <th scope="col">Login</th>
              <th scope="col">Nick allegro</th>
              <th scope="col">Imię</th>
              <th scope="col">Nazwisko</th>
              <th scope="col">Nazwa firmy</th>
              <th scope="col">Numer telefonu</th>
              <th scope="col">NIP</th>
              <th scope="col">Adres</th>
              <th scope="col">Email</th>
              <th scope="col" class="text-center">Akcje</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(customer,index) in customers" :key="index">
              <th scope="col">{{ customer.id }}</th>
              <td>{{ customer.login }}</td>
              <td>{{ customer.nickAllegro }}</td>
              <td>{{ customer.firstName }}</td>
              <td>{{ customer.lastName }}</td>
              <td>{{ customer.firmName }}</td>
              <td>{{ customer.phone }}</td>
              <td>{{ customer.nip }}</td>
              <td>{{ customer.address }}</td>
              <td>{{ customer.email }}</td>
              <td class="text-center">
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
</style>
