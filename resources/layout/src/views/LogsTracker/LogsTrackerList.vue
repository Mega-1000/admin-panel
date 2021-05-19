<template>
  <div class="v-logsTrackerList">
    <table class="table">
      <thead>
        <tr>
          <th>Id</th>
          <th>Uzytkownik</th>
          <th>Data</th>
          <th>Strona</th>
          <th>Czas bezczynności</th>
          <th>Uzasadnienie</th>
        </tr>
      </thead>
      <tbody>
      <tr v-for="(item, index) in logs" :key="index">
        <td>{{ index }}</td>
        <td> {{ item.user_id }} </td>
        <td> {{ item.created_at}} </td>
        <td> {{ item.page}} </td>
        <td> {{ item.time }} min</td>
        <td> <p v-html="item.description"></p></td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { LogItem } from '@/types/LogsTrackerType'

@Component({
  components: {
  }
})
export default class LogsTrackerList extends Vue {
  public get logs (): LogItem[] {
    return this.$store?.getters['LogsTrackerService/logs']
  }

  private async loadLogs (): Promise<void> {
    await this.$store?.dispatch('LogsTrackerService/loadLogs')
  }

  public async mounted (): Promise<void> {
    await this.$store?.dispatch('LogsTrackerService/loadLogs')
  }
}
</script>
<style scoped lang="scss">
  @import "@/assets/styles/main";

</style>
