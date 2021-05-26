<template>
  <div>
    <dates-table
      @modify="toggleShowModifyDatesModal"
      @accept="saveAccept"
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
import { AcceptDatesParams } from '@/types/OrdersTypes'

@Component({
  components: {
    ModifyDateModal,
    DatesTable
  }
})
export default class OrderDates extends Vue {
  @Prop({ default: 15005 }) public orderId!: number

  public showModal = false;
  public type: string | null = null;

  public toggleShowModifyDatesModal (type: string): void {
    this.type = type
    this.showModal = !this.showModal
  }

  public async saveAccept (type: string): Promise<void> {
    const params: AcceptDatesParams = {
      type: type,
      orderId: this.orderId
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
}
</script>
