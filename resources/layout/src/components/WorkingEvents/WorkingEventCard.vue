<template>
  <div class="modal-backdrop show c-workingEventCard modal-scrollbar-measure" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ card.title }}</h5>
          <button type="button" class="close" aria-label="Close">
            <span aria-hidden="true" @click="$emit('close')">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <ul>
            <li v-for="(content,index) in card.description" :key="index" v-html="content"></li>
          </ul>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="$emit('close')">Zamknij</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script lang="ts">
import { Vue, Prop, Component } from 'vue-property-decorator'
import { Card } from '@/types/WorkingEventsTypes'

@Component({
  components: {}
})
export default class WorkingEventCard extends Vue {
  @Prop() private card!: Card

  public async mounted (): Promise<void> {
    console.log(this.card)
  }
}
</script>

<style scoped lang="scss">
  @import "@/assets/styles/main";

  .c-workingEventCard {
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
    margin-left: 15px;
    margin-right: 15px;
  }

  .modal {
    display: block !important;
  }
</style>
