<template>
  <!--
  IMPORTANT !!!
  tracker modal was disabled by changing <div id="actionTracker"></div> to <div id="disabled-actionTracker"></div>
  to related views
  -->
  <div class="c-actionTracker">
    <ModalActionTrackers v-if="showModal" @close="toggleShowModal()" :time="trackTime"></ModalActionTrackers>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import ModalActionTrackers from '@/components/ModalActionTrackers.vue'
import { addLogParam, LogItem, updateTimeLogParam } from '@/types/LogsTrackerType'

@Component({
  components: {
    ModalActionTrackers
  }
})
export default class ActionTrackers extends Vue {
  private trackTime = 0
  private finalTime = 3
  private timeInterval = 60000
  public showModal = false

  public get log (): LogItem {
    return this.$store?.getters['LogsTrackerService/log']
  }

  public mounted (): void {
    this.runTimer()
    this.trackClick()
    this.trackWrite()
  }

  private runTimer (): void {
    setInterval(async () => {
      this.trackTime++

      if ((this.trackTime >= this.finalTime) && (this.trackTime % this.finalTime === 0)) {
        this.setLog()
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

  private async setLog (): Promise<void> {
    if (this.log !== null) {
      const param: updateTimeLogParam = {
        id: this.log.id,
        time: this.trackTime
      }
      await this.$store?.dispatch('LogsTrackerService/updateTimeLog', param)
    } else {
      const param: addLogParam = {
        time: this.trackTime,
        page: window.location.href
      }
      await this.$store?.dispatch('LogsTrackerService/setLog', param)
    }
  }
}
</script>

<style scoped lang="scss">
  @import "../assets/styles/main";
</style>
