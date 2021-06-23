<template>
  <div>
    <div class="row">
      <div class="col-sm-12">
        <div class="alert alert-danger" v-if="error.length">{{ error }}</div>
        <div class="alert alert-info" v-html="getMessage()"></div>
      </div>
    </div>
    <dates-table
      @modify="toggleShowModifyDatesModal"
      @accept="saveAccept"
      @acceptAsCustomer="acceptAsCustomer"
      :user-type="getUserType()"
    ></dates-table>
    <modify-date-modal
      v-if="showModal"
      @close="toggleShowModifyDatesModal()"
      :type="type"
      :order-id="orderId"
      @loadDates="loadDates()"></modify-date-modal>
    <debugger :keepAlive="true" :components="$children"></debugger>
  </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import DatesTable from '@/components/Orders/Dates/DatesTable.vue'
import ModifyDateModal from '@/components/Orders/Dates/ModifyDateModal.vue'
import { Acceptance, AcceptAsCustomerParams, AcceptDatesParams, Dates } from '@/types/OrdersTypes'
import { USER_TYPES } from '@/constant/Orders'

@Component({
  components: {
    ModifyDateModal,
    DatesTable
  }
})
export default class OrderDates extends Vue {
  @Prop() public orderId!: number
  @Prop({ default: 'u' }) public userType!: string
  @Prop() public chatId!: number

  public showModal = false;
  public type: string | null = null;

  public toggleShowModifyDatesModal (type: string): void {
    this.type = type
    this.showModal = !this.showModal
  }

  public async acceptAsCustomer (): Promise<void> {
    const params: AcceptAsCustomerParams = {
      orderId: this.orderId,
      chatId: this.chatId
    }
    await this.$store?.dispatch('OrdersService/saveAcceptAsCustomer', params)
  }

  public get error (): Dates {
    return this.$store?.getters['OrdersService/error']
  }

  public get acceptance (): Acceptance {
    return this.$store?.getters['OrdersService/acceptance']
  }

  public async saveAccept (type: string): Promise<void> {
    const params: AcceptDatesParams = {
      type: type,
      orderId: this.orderId,
      userType: this.getUserType()
    }

    await this.$store?.dispatch('OrdersService/saveAccept', params)
  }

  private async loadDates (): Promise<void> {
    const param = {
      id: this.orderId
    }
    await this.$store?.dispatch('OrdersService/loadDates', param)
  }

  public async mounted (): Promise<void> {
    await this.$store?.dispatch('OrdersService/loadDates', this.orderId)
  }

  public getUserType (): string {
    return USER_TYPES[this.userType]
  }

  public getMessage () {
    return 'Aby zaakceptować daty wybranej strony transakcji kliknij przycisk <strong>Akceptuj</strong> w odpowiedniej kolumnie<br/>' +
      'Aby zaakceptować daty w imieniu klienta wybierz przycisk <strong>Akceptuj w imieniu klienta</strong><br/>' +
      'W celu modyfikacji dat wybierz przycisk <strong>Modyfikuj</strong> a następnie wypełnij formularz. <br>' + this.acceptance.message
  }
}
</script>
