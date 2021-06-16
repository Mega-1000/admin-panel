<template>
  <div class="modal show c-modifyDatesModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Modyfikacja szczegółów zamówienia</h5>
          <button type="button" class="close" aria-label="Close">
            <span aria-hidden="true" @click="$emit('close')">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <h4>Preferowana data nadania</h4>
            </div>
            <div class="col-md-12">
              <div class="form-group row">
                <label for="shipment_date_from" class="col-md-3 col-form-label">Od</label>
                <div class="col-md-5">
                  <date-picker
                    name="shipment_date_from"
                    format="YYYY-MM-DD"
                    id="shipment_date_from"
                    type="date"
                    valueType="format"
                    v-model="shipmentDateFrom"
                  ></date-picker>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group row">
                <label for="shipment_date_to" class="col-md-3 col-form-label">Do</label>
                <div class="col-md-5">
                  <date-picker
                    name="shipment_date_to"
                    format="YYYY-MM-DD"
                    id="shipment_date_to"
                    type="date"
                    valueType="format"
                    v-model="shipmentDateTo"
                  ></date-picker>
                </div>
              </div>
            </div>

            <div class="col-md-12">
              <h4>Preferowana data dostawy</h4>
            </div>
            <div class="col-md-12">
              <div class="form-group row">
                <label for="delivery_date_from" class="col-md-3 col-form-label">Do</label>
                <div class="col-md-5">
                  <date-picker
                    name="delivery_date_from"
                    format="YYYY-MM-DD"
                    id="delivery_date_from"
                    type="date"
                    v-model="deliveryDateFrom"
                    valueType="format"
                  ></date-picker>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group row">
                <label for="delivery_date_to" class="col-md-3 col-form-label">Od</label>
                <div class="col-md-5">
                  <date-picker
                    name="delivery_date_to"
                    format="YYYY-MM-DD"
                    id="delivery_date_to"
                    type="date"
                    v-model="deliveryDateTo"
                    valueType="format"
                  ></date-picker>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="$emit('close')">Powrót</button>
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

  public shipmentDateFrom = ''
  public shipmentDateTo = ''
  public deliveryDateFrom = ''
  public deliveryDateTo = ''

  public async updateDates (): Promise<void> {
    const params: UpdateDatesParams = {
      type: this.type,
      orderId: this.orderId,
      shipmentDateFrom: this.shipmentDateFrom,
      shipmentDateTo: this.shipmentDateTo,
      deliveryDateFrom: this.deliveryDateFrom,
      deliveryDateTo: this.deliveryDateTo
    }

    await this.$store?.dispatch('OrdersService/updateDateParams', params)
    this.$emit('close')
  }

  public async mounted (): Promise<void> {
    const dates = this.$store?.getters['OrdersService/' + this.type + 'Dates']
    this.shipmentDateFrom = dates.shipment_date_from
    this.shipmentDateTo = dates.shipment_date_to
    this.deliveryDateFrom = dates.delivery_date_from
    this.deliveryDateTo = dates.delivery_date_to
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
</style>
