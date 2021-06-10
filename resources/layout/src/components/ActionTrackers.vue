<template>
  <div class="c-actionTracker" v-if="enabled" @click="checkTime()">
    <!--
    IMPORTANT !!!
    tracker modal was disabled by changing <div id="actionTracker"></div> to <div id="disabled-actionTracker"></div>
    to related views
    -->
    <div class="c-actionTracker">
      <ModalActionTrackers v-if="showModal && time >= finalTime" @close="toggleShowModal()" :time="time"></ModalActionTrackers>
    </div>
  </div>
</template>

<script lang="ts">
import { Component, Vue, Prop } from 'vue-property-decorator'
import ModalActionTrackers from '@/components/ModalActionTrackers.vue'
import { addLogParam, LogItem, updateTimeLogParam } from '@/types/LogsTrackerType'

@Component({
  components: {
    ModalActionTrackers
  }
})
export default class ActionTrackers extends Vue {
  @Prop() private enabled!: boolean
  private time = 0
  private finalTime = 3
  private timeInterval = 60000
  private intervalId = 0
  public showModal = false

  public get log (): LogItem {
    return this.$store?.getters['LogsTrackerService/log']
  }

  public mounted (): void {
    this.$cookies.remove('tracker')
    this.$cookies.set('tracker-refresh', true)
    this.$cookies.set('tracker', 0)
    this.$cookies.refresh()
    this.time = 0
    this.intervalId = this.runTimer()
    this.trackClick()
    this.trackWrite()
  }

  public get trackTime (): number {
    this.$cookies.refresh()
    return this.$cookies.get('tracker')
  }

  private runTimer (): number {
    return setInterval(async () => {
      this.time++
      this.$cookies.refresh()
      this.incrementTrackerCookie()
      console.log(this.trackTime)
      if ((this.time >= this.finalTime) && (this.time % this.finalTime === 0)) {
        this.setLog()
        this.showModal = true
      }
    }, this.timeInterval)
  }

  public resetTimer (): void {
    this.$cookies.refresh()
    this.$cookies.set('tracker-refresh', true)
    this.setCookieTracker()
    this.$cookies.refresh()
  }

  private trackClick (): void {
    const elements = document.querySelectorAll('[track-click]')
    elements.forEach((element) => {
      element.addEventListener('click', () => {
        if (!this.showModal) {
          this.resetTimer()
        }
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
    this.setLog()
    this.resetTimer()
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

  private setCookieTracker (time = 0): void {
    this.$cookies.refresh()
    this.$cookies.set('tracker', time)
    this.$cookies.refresh()
  }

  private incrementTrackerCookie (): void {
    if (this.$cookies.get('tracker-refresh')) {
      this.setCookieTracker()
      this.time = 0
      clearInterval(this.intervalId)
      this.intervalId = this.runTimer()
      this.$cookies.set('tracker-refresh', false)
    } else {
      this.setCookieTracker(Number(this.$cookies.get('tracker')) + 1)
      this.time = this.trackTime
      clearInterval(this.intervalId)
      this.intervalId = this.runTimer()
    }
  }
}
</script>

<style scoped lang="scss">
  @import "../assets/styles/main";
</style>
