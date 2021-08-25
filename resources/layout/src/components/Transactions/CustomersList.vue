<template>
  <div class="c-customersList">
    <table class="table">
      <thead>
      <tr>
        <th>Id</th>
        <th>Login</th>
        <th>Nick allegro</th>
        <th>Imię</th>
        <th>Nazwisko</th>
        <th>Nazwa firmy</th>
        <th>Numer telefonu</th>
        <th>Adres</th>
        <th>Email</th>
        <th class="text-center">Akcje</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(customer,index) in customers" :key="index">
        <td>{{ customer.id }}</td>
        <td>{{ customer.login }}</td>
        <td>{{ customer.nick_allegro }}</td>
        <td>{{ customer.addresses[0].firstname }}</td>
        <td>{{ customer.addresses[0].lastname }}</td>
        <td>{{ customer.addresses[0].firmname }}</td>
        <td>{{ customer.addresses[0].phone }}</td>
        <td>{{ customer.addresses[0].address }} {{ customer.addresses[0].flat_number }}
          {{ customer.addresses[0].postal_code }} {{ customer.addresses[0].city }}
        </td>
        <td>{{ customer.addresses[0].email }}</td>
        <td class="text-center">
          <button class="btn btn-primary" @click="setCustomer(customer)">
            <span class="hidden-xs hidden-sm">Transakcje</span>
          </button>
          <button class="btn btn-secondary" href="#">
            <span class="hidden-xs hidden-sm">Zamówienia</span>
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
export default class CustomersList extends Vue {
  public get customers (): Customer[] {
    return this.$store?.getters['TransactionsService/customers']
  }

  private async setCustomer (customer: Customer): Promise<void> {
    await this.$store?.dispatch('TransactionsService/setCustomer', customer)
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
