<template>
  <div class="c-modalActionTracker">
    <div class="overlay" @click="$emit('close')"></div>
    <div class="c-modal">
      <div class="header">
          <p class="text">Nie wykonywałeś żadnej akcji przez {{ time }} minut/y. Został wykonany zrzut ekranu.
          Proszę wytłumacz poniżej jaki był powód Twojej zwłoki</p>
          <span @click="$emit('close')" class="close">X</span>
      </div>
      <textarea class="textarea" rows="4" cols="50" v-model="description">
      </textarea>
      <button @click="sendDescription()" class="btn btn-sm btn-primary">Wyślij</button>
    </div>
  </div>
</template>
<script lang="ts">
import { Component, Vue, Prop } from 'vue-property-decorator'
import { LogItem, updateDescriptionLogParam } from '@/types/LogsTrackerType'

@Component({
  components: {
  }
})
export default class ModalActionTracker extends Vue {
  @Prop() public time!: number;

  public description = ''

  public get log (): LogItem {
    return this.$store?.getters['LogsTrackerService/log']
  }

  public async sendDescription (): Promise<void> {
    const param: updateDescriptionLogParam = {
      id: this.log.id,
      description: this.description,
      time: this.time
    }
    await this.$store?.dispatch('LogsTrackerService/updateDescriptionLog', param)
    this.$emit('close')
  }
}
</script>

<style scoped lang="scss">
  @import "@/assets/styles/main";

  .c-modalActionTracker {
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    z-index: $index-addSetModal;
  }

  .close {
    position: absolute;
    right: 10px;
    top: 10px;
  }

  .overlay {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background: $cl-black00;
    opacity: 0.5;
    z-index: $index-addSetModalOverlay;
  }

  .c-modal {
    max-width: 100%;
    width: 450px;
    margin: 150px auto;
    background: $cl-whiteff;
    padding: 50px;
    z-index: $index-addSetModalContent;
    position: relative;
  }

  .textarea,
  .text {
    max-width: 100%;
    margin-bottom: 20px;
  }

  .text {
    text-align: center;
  }

  .btn-primary {
    display: block;
    margin: 0 auto;
  }
</style>
