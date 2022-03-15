<template>
  <div class="v-workingEventsPresentation">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group"
             :class="[{'has-error': user.error===true},{'has-success': user.error===false && user.value !== ''}]">
          <label for="users" class="col-md-5 col-form-label">Użytkownik</label>
          <div class="col-md-5">
            <select v-model="user.value" required @change="loadActivity"
                    id="users" class="form-control">
              <option selected value="">-- wybierz --</option>
              <option v-for="(user,index) in users" :key="index" :value="user.id">
                {{ user.firstname }} {{ user.lastname }}
              </option>
            </select>
            <span class="form-control-feedback">{{ user.errorMessage }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group"
             :class="[{'has-error': date.error===true},{'has-success': date.error===false && date.value !== ''}]">
          <label for="date" class="col-md-5 col-form-label">Raport z dnia</label>
          <div class="col-md-7">
            <date-picker
              name="registrationInBankDate"
              format="YYYY-MM-DD"
              id="date"
              type="date"
              valueType="format"
              @change="loadActivity"
              v-model="date.value"
              width="100%"
            ></date-picker>
            <span class="form-control-feedback">{{ date.errorMessage }}</span>
          </div>
        </div>
      </div>
    </div>
    <vue-horizontal-timeline v-if="items.length > 0" :items="items" :title-substr="30"/>
    <debugger :keepAlive="true" :components="$children"></debugger>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { Timeline, TimelineTitle, TimelineItem } from 'vue-cute-timeline'
import 'vue-cute-timeline/dist/index.css'
import { VueHorizontalTimeline } from 'vue-horizontal-timeline'
import DatePicker from 'vue2-datepicker'
import 'vue2-datepicker/index.css'
import 'vue2-datepicker/locale/pl'
import { searchWorkingEventsParams, User } from '@/types/WorkingEventsTypes'

@Component({
  components: {
    Timeline,
    TimelineTitle,
    TimelineItem,
    VueHorizontalTimeline,
    DatePicker
  }
})
export default class WorkingEventsPresentation extends Vue {
  private user = {
    value: '',
    error: false,
    errorMessage: ''
  }

  private date = {
    value: (new Date()).toISOString(),
    error: false,
    errorMessage: ''
  }

  public async loadActivity (): Promise<void> {
    const params: searchWorkingEventsParams = {
      userId: parseInt(this.user.value),
      date: this.date.value
    }
    await this.$store?.dispatch('WorkingEventsService/loadWorkingEvents', params)
    await this.$store?.dispatch('WorkingEventsService/loadInactivity', params)
  }

  public async mounted (): Promise<void> {
    await this.$store?.dispatch('WorkingEventsService/loadWorkers')
  }

  public get users (): User[] {
    return this.$store?.getters['WorkingEventsService/users']
  }

  public get items (): Event[] {
    const events = this.$store?.getters['WorkingEventsService/events']
    const inactivity = this.$store?.getters['WorkingEventsService/inactivity']
    return events.concat(inactivity)
  }
}
</script>

<style scoped lang="scss">
  @import "@/assets/styles/main";

  .voyager-pen,
  .voyager-trash {
    @media (min-width: 992px) {
      margin-right: 7px;
    }
  }

  .voyager-double-down,
  .voyager-double-up {
    @media (min-width: 992px) {
      margin-right: 4px;
    }
  }
</style>
