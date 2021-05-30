<template>
  <div>
    <div class="row">
      <div class="col-sm-12">
        <div class="alert alert-danger" v-if="error.length">{{ error }}</div>
        <div class="alert alert-info" v-if="acceptance.message.length" v-html="acceptance.message"></div>
      </div>
    </div>
    <dates-table
      @modify="toggleShowModifyDatesModal"
      @accept="saveAccept"
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
import { Acceptance, AcceptDatesParams, Dates } from '@/types/OrdersTypes'

@Component({
  components: {
    ModifyDateModal,
    DatesTable
  }
})
export default class OrderDates extends Vue {
  @Prop() public orderId!: number
  @Prop() public userType!: string

  public showModal = false;
  public type: string | null = null;

  public toggleShowModifyDatesModal (type: string): void {
    this.type = type
    this.showModal = !this.showModal
  }

  public get error (): Dates[] {
    return this.$store?.getters['OrdersService/error']
  }

  public get acceptance (): Acceptance[] {
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
    const USER_TYPES: any = {
      c: 'customer',
      e: 'warehouse',
      u: 'consultant'
    }
    return USER_TYPES[this.userType]
  }
}
</script>
