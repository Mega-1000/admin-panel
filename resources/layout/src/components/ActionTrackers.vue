<template>
  <div class="c-actionTracker">
    <button track-click>Reset</button>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'

@Component
export default class ActionTrackers extends Vue {
  private trackTime = 0
  private finalTime = 4
  private timeInterval = 1000

  public mounted (): void {
    this.runTimer()
    this.trackClick()
  }

  private runTimer (): void {
    setInterval(() => {
      this.trackTime++
      console.log(this.trackTime)
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
}
</script>

<style scoped lang="scss">
  @import "../assets/styles/main";
</style>
