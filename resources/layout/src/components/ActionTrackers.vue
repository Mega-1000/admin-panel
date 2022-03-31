<template>
  <div class="c-actionTracker" v-if="enabled">
    <!--
    IMPORTANT !!!
    tracker modal was disabled by changing <div id="actionTracker"></div> to <div id="disabled-actionTracker"></div>
    to related views
    -->
    <div class="c-actionTracker">
      <ModalActionTrackers v-if="showModal && time >= finalTime" @close="toggleShowModal()"
                           :time="time"></ModalActionTrackers>
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
  @Prop() private user!: number
  private time = 0
  private finalTime = 3
  private timeInterval = 60000
  private intervalId = 0
  public showModal = false

  public get log (): LogItem {
    return this.$store?.getters['LogsTrackerService/log']
  }

  public mounted (): void {
    localStorage.removeItem('tracker')
    localStorage.setItem('tracker-refresh', 'true')
    localStorage.setItem('tracker', '0')
    this.time = 0
    this.intervalId = this.runTimer()
    this.trackClick()
    this.trackWrite()
  }

  public get trackTime (): number {
    return parseInt(localStorage.getItem('tracker') ?? '')
  }

  private runTimer (): number {
    return setInterval(async () => {
      this.time++
      this.incrementTrackerCookie()
      if ((this.time >= this.finalTime) && (this.time % this.finalTime === 0)) {
        this.setLog()
        this.showModal = true
      }
    }, this.timeInterval)
  }

  public resetTimer (): void {
    localStorage.setItem('tracker-refresh', 'true')
    this.setCookieTracker()
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

  public toggleShowModal (): void {
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
        userId: this.user,
        time: this.trackTime,
        page: window.location.href
      }
      await this.$store?.dispatch('LogsTrackerService/setLog', param)
    }
  }

  private setCookieTracker (time = 0): void {
    localStorage.setItem('tracker', time.toString())
  }

  private incrementTrackerCookie (): void {
    if (localStorage.getItem('tracker-refresh') === 'true') {
      this.setCookieTracker()
      this.time = 0
      clearInterval(this.intervalId)
      this.intervalId = this.runTimer()
      localStorage.setItem('tracker-refresh', 'false')
    } else {
      this.setCookieTracker(Number(localStorage.getItem('tracker')) + 1)
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
