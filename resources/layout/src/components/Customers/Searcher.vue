<template>
  <div class="modal-backdrop show c-customerSearcher modal-scrollbar-measure" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Wyszukiwanie klienta</h5>
          <button type="button" class="close" aria-label="Close">
            <span aria-hidden="true" @click="$emit('close')">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <div class="alert alert-danger" v-if="error.length">
                {{ error }}
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="nickAllegro">Nick allegro</label>
                <input type="text" @input="loadCustomers" id="nickAllegro" name="nickAllegro"
                       class="form-control" v-model="nickAllegro">
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="firstName">Imię</label>
                <input type="text" @input="loadCustomers" @keypress.enter="loadCustomers" id="firstName"
                       name="firstName"
                       class="form-control" v-model="firstName">
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="lastName">Nazwisko</label>
                <input type="text" @input="loadCustomers" @keypress.enter="loadCustomers" id="lastName" name="lastName"
                       class="form-control" v-model="lastName">
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="phone">Telefon</label>
                <input type="text" @input="loadCustomers" @keypress.enter="loadCustomers" id="phone" name="phone"
                       class="form-control" v-model="phone">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="email">Adres email</label>
                <input type="text" @input="loadCustomers" @keypress.enter="loadCustomers" id="email" name="email"
                       class="form-control" v-model="email">
              </div>
            </div>
          </div>
          <div class="row table-customers">
            <div class="col-md-12">
              <div class="table-responsive">
                <table class="table table-hover overflow-auto">
                  <thead>
                  <tr>
                    <th scope="col">Nick allegro</th>
                    <th scope="col">Imię</th>
                    <th scope="col">Nazwisko</th>
                    <th scope="col">Numer telefonu</th>
                    <th scope="col">Email</th>
                    <th scope="col">Akcje</th>
                  </tr>
                  </thead>
                  <tbody v-if="customers.length">
                  <tr v-for="(customer,index) in customers" :key="index">
                    <td>{{ customer.nickAllegro }}</td>
                    <td>{{ customer.firstName }}</td>
                    <td>{{ customer.lastName }}</td>
                    <td>{{ customer.phone }}</td>
                    <td>{{ customer.email }}</td>
                    <td>
                      <button class="btn btn-small btn-success" @click="$emit('selected',customer)">Wybierz
                      </button>
                    </td>
                  </tr>
                  </tbody>
                  <tbody v-else>
                  <tr>
                    <td class="text-center" colspan="6">{{ error }}</td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="$emit('close')">Powrót</button>
          <button type="reset" class="btn btn-danger" @click="reset">Wyczyść</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { searchCustomerParams } from '@/types/CustomersTypes'

@Component({
  components: {}
})
export default class Searcher extends Vue {
  public firstName = ''
  public lastName = ''
  public phone = ''
  public email = ''
  public nickAllegro = ''

  public get error (): string {
    return this.$store.getters['CustomersService/error']
  }

  public get isLoading (): boolean {
    return this.$store.getters['CustomersService/isLoading']
  }

  public get customers (): [] {
    return this.$store?.getters['CustomersService/customers']
  }

  public async mounted (): Promise<void> {
    await this.$store.dispatch('CustomersService/setCustomers', [])
  }

  public async reset (): Promise<void> {
    this.firstName = ''
    this.lastName = ''
    this.phone = ''
    this.email = ''
    this.nickAllegro = ''
    await this.$store.dispatch('CustomersService/setError', '')
  }

  public async loadCustomers (): Promise<void> {
    const searchedCustomer: searchCustomerParams = {
      firstName: this.firstName,
      lastName: this.lastName,
      phone: this.phone,
      email: this.email,
      nickAllegro: this.nickAllegro
    }
    if (this.firstName.length || this.lastName.length || this.phone.length || this.email.length || this.nickAllegro.length) {
      await this.$store.dispatch('CustomersService/loadCustomers', searchedCustomer)
    } else {
      await this.$store.dispatch('CustomersService/setCustomers', [])
      await this.$store.dispatch('CustomersService/setError', '')
    }
  }
}
</script>

<style scoped lang="scss">
  @import "@/assets/styles/main";

  .c-customerSearcher {
    position: fixed;
    width: 100%;
    height: 100%;
    top: 30px;
    left: 0;
    background: rgba(255, 255, 255, 0.7);
    z-index: $index-modal;
  }

  .close {
    position: absolute;
    right: 10px;
    top: 20px;
  }

  .modal-title {
    font-size: 18px;
    line-height: 30px;
    color: $cl-grey5;
    font-weight: 700;
    margin: 5px 0;
  }

  .modal-body {
    margin-left: 30px;
    margin-right: 30px;
  }

  .modal {
    display: block !important;
  }

  .table-customers {
    max-height: 550px;
    overflow-y: auto;
  }
</style>
