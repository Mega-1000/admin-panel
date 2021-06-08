<template>
  <table class="table">
    <thead>
    <tr>
      <th scope="col"></th>
      <th scope="col" class="text-center"></th>
      <th scope="col" class="text-center">Klient</th>
      <th scope="col" class="text-center">Konsultant</th>
      <th scope="col" class="text-center">Magazyn</th>
    </tr>
    </thead>
    <tbody>
    <tr>
      <th scope="row" rowspan="2" class="v-align-middle">Preferowana data nadania</th>
      <th scope="row">Od</th>
      <td>{{ customerDates.shipment_date_from }}</td>
      <td>{{ consultantDates.shipment_date_from }}</td>
      <td>{{ warehouseDates.shipment_date_from }}</td>
    </tr>
    <tr>
      <th scope="row">Do</th>
      <td>{{ customerDates.shipment_date_to }}</td>
      <td>{{ consultantDates.shipment_date_to }}</td>
      <td>{{ warehouseDates.shipment_date_to }}</td>
    </tr>
    <tr>
      <th scope="row" rowspan="2" class="v-align-middle">Preferowana data dostawy</th>
      <th scope="row">Od</th>
      <td>{{ customerDates.delivery_date_from }}</td>
      <td>{{ consultantDates.delivery_date_from }}</td>
      <td>{{ warehouseDates.delivery_date_from }}</td>
    </tr>
    <tr>
      <th scope="row">Do</th>
      <td>{{ customerDates.delivery_date_to }}</td>
      <td>{{ consultantDates.delivery_date_to }}</td>
      <td>{{ warehouseDates.delivery_date_to }}</td>
    </tr>
    <tr>
      <th scope="row" colspan="2" class="text-center">Akceptacja</th>
      <td class="text-center">
        <span :class="getGlyphiconClass(acceptance.customer)" class="glyphicon"></span>
      </td>
      <td class="text-center">
        <span :class="getGlyphiconClass(acceptance.consultant)" class="glyphicon"></span>
      </td>
      <td class="text-center">
        <span :class="getGlyphiconClass(acceptance.warehouse)" class="glyphicon"></span>
      </td>
    </tr>
    <tr>
      <th scope="row" colspan="2" class="text-center">Akcje</th>
      <td>
        <div class="row">
          <div class="btn-group mb-5" role="group">
            <a class="btn btn-sm btn-primary" :class="{ disabled: userType!=='customer'}"
               @click="$emit('modify','customer')">Modyfikuj</a>
            <a class="btn btn-sm btn-success" :class="{ disabled: !canAccept('customer')}"
               @click="$emit('accept','customer')">Akceptuj</a>
          </div>
        </div>
      </td>
      <td>
        <div class="row text-center">
          <div class="btn-group" role="group">
            <a class="btn btn-sm btn-primary" :class="{ disabled: userType!=='consultant'}"
               @click="$emit('modify','consultant')">Modyfikuj</a>
            <a class="btn btn-sm btn-success" :class="{ disabled: !canAccept('consultant') }"
               @click="$emit('accept','consultant')">Akceptuj</a>
          </div>
        </div>
        <div class="row text-center mt-5">
          <div class="btn-group" role="group">
            <a class="btn btn-sm btn-info"
               @click="$emit('acceptAsCustomer')">Akceptuj w imieniu klienta</a>
          </div>
        </div>
      </td>
      <td>
        <div class="btn-group" role="group">
          <a class="btn btn-sm btn-primary" :class="{ disabled: userType!=='warehouse'}"
             @click="$emit('modify','warehouse')">Modyfikuj</a>
          <a class="btn btn-sm btn-success" :class="{ disabled: !canAccept('warehouse') }"
             @click="$emit('accept','warehouse')">Akceptuj</a>
        </div>
      </td>
    </tr>
    </tbody>
  </table>
</template>
<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import { Acceptance, Dates } from '@/types/OrdersTypes'

@Component({
  components: {}
})
export default class DatesTable extends Vue {
  @Prop() public orderId!: number
  @Prop() public userType!: string

  public get customerDates (): Dates[] {
    return this.$store?.getters['OrdersService/customerDates']
  }

  public get consultantDates (): Dates[] {
    return this.$store?.getters['OrdersService/consultantDates']
  }

  public get warehouseDates (): Dates[] {
    return this.$store?.getters['OrdersService/warehouseDates']
  }

  public get acceptance (): Acceptance[] {
    return this.$store?.getters['OrdersService/acceptance']
  }

  public getGlyphiconClass (accept: boolean): string {
    return 'glyphicon-' + (accept ? 'ok text-success' : 'remove text-danger')
  }

  public canAccept (userType: string): boolean {
    if (userType === this.userType) {
      return false
    }
    const dates = this.$store?.getters['OrdersService/' + userType + 'Dates']
    return Object.values(dates).some(date => (date !== null))
  }
}
</script>
<style scoped lang="scss">
  @import "@/assets/styles/main";

  .v-align-middle {
    vertical-align: middle;
  }

  .mb-5 {
    margin-bottom: 5px;
  }

  .mt-5 {
    margin-top: 5px;
  }
</style>
