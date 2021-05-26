<template>
  <div class="modal show c-modifyDatesModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Modyfikacja szczegółów zamówienia</h5>
          <button type="button" class="close" aria-label="Close">
            <span aria-hidden="true" @click="$emit('close')">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group row">
            <label for="shipment_date" class="col-md-5 col-form-label">Preferowana data nadania</label>
            <div class="col-md-7">
              <date-picker
                name="shipment_date"
                format="YYYY-MM-DD HH:mm"
                show-hour id="shipment_date"
                show-time-header
                type="datetime"
                valueType="format"
                v-model="shipmentDate"
                confirm
                range></date-picker>
            </div>
          </div>
          <div class="form-group row">
            <label for="shipment_date" class="col-md-5 col-form-label">Preferowana data dostawy</label>
            <div class="col-md-7">
              <date-picker
                name="delivery_date"
                format="YYYY-MM-DD HH:mm"
                show-hour id="delivery_date"
                show-time-header
                type="datetime"
                v-model="deliveryDate"
                valueType="format"
                confirm
                range></date-picker>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="$emit('close')">Zamknij</button>
          <button type="button" class="btn btn-success" @click="updateDates">Zapisz</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import DatePicker from 'vue2-datepicker'
import 'vue2-datepicker/index.css'
import 'vue2-datepicker/locale/pl'
import { UpdateDatesParams } from '@/types/OrdersTypes'

@Component({
  components: {
    DatePicker
  }
})
export default class ModifyOrderDatesModal extends Vue {
  @Prop() public type!: string
  @Prop() public orderId!: number

  public shipmentDate: string[] = []
  public deliveryDate: string[] = []

  public async updateDates (): Promise<void> {
    const params: UpdateDatesParams = {
      type: this.type,
      orderId: this.orderId,
      shipmentDateFrom: this.shipmentDate[0],
      shipmentDateTo: this.shipmentDate[1],
      deliveryDateFrom: this.deliveryDate[0],
      deliveryDateTo: this.deliveryDate[1]
    }

    await this.$store?.dispatch('OrdersService/updateDateParams', params)
    this.$emit('close')
  }

  public async mounted (): Promise<void> {
    const dates = this.$store?.getters['OrdersService/' + this.type + 'Dates']
    this.shipmentDate = [dates.shipment_date_from, dates.shipment_date_to]
    this.deliveryDate = [dates.delivery_date_from, dates.delivery_date_to]
  }
}
</script>

<style scoped lang="scss">
  @import "@/assets/styles/main";

  .c-modifyDatesModal {
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
  }

  .close {
    position: absolute;
    right: 10px;
    top: 20px;
  }

  .overlay {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background: $cl-black00;
    opacity: 0.5;
  }

  span {
    font-weight: 600;
  }

  .return {
    position: absolute;
    left: 10px;
    top: 10px;
  }

  .modal-title {
    font-size: 18px;
    line-height: 30px;
    color: $cl-grey5;
    font-weight: 700;
    margin: 5px 0;
  }

  .btn-create {
    display: block;
    margin: 25px auto 0 auto;
  }

  .select {
    padding: 10px;
    border: 1px solid $cl-greye;
    word-wrap: break-word;
    white-space: -moz-pre-wrap;
    white-space: pre-wrap;
    max-width: 100%;
    display: inline-block;
  }
</style>
