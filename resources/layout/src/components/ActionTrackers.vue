<template>
  <div class="c-actionTracker">
    <ModalActionTrackers v-if="showModal" @close="toggleShowModal()"></ModalActionTrackers>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import ModalActionTrackers from '@/components/ModalActionTrackers.vue'

@Component({
  components: {
    ModalActionTrackers
  }
})
export default class ActionTrackers extends Vue {
  private trackTime = 0
  private finalTime = 15
  private timeInterval = 1000
  public showModal = false

  public mounted (): void {
    this.runTimer()
    this.trackClick()
    this.trackWrite()
  }

  private runTimer (): void {
    setInterval(() => {
      this.trackTime++
      console.log(this.trackTime)

      if (this.trackTime === this.finalTime) {
        this.showModal = true
      }
    }, this.timeInterval)
  }

  public resetTimer (): void {
    this.trackTime = 0
  }

  private trackClick (): void {
    const elements = document.querySelectorAll('[track-click]')
    elements.forEach((element) => {
      element.addEventListener('click', () => {
        this.resetTimer()
      })
    })
  }

  private trackWrite (): void {
    const elements = document.querySelectorAll('[track-write]')
    elements.forEach((element) => {
      element.addEventListener('input', () => {
        this.resetTimer()
      })
    })
  }

  public toggleShowModal ():void {
    this.showModal = !this.showModal
  }
}
</script>

<style scoped lang="scss">
  @import "../assets/styles/main";
</style>
