<template>
  <div class="v-workingEventsPresentation">
    <div class="row">
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-12">
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
        </div>
        <div class="row">
          <div class="col-md-12">
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
      </div>
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-12" v-if="workingInfo !== null">
            <h4>Podsumowanie pracy</h4>
            <p>Rozpoczęcie pracy: {{ workingInfo.workingFrom }}</p>
            <p>Zakończenie pracy: {{ workingInfo.workingTo }}</p>
            <p>Czas pracy bez odliczeń: {{ convertTime(workingInfo.uptimeInMinutes) }}</p>
            <p>Czas bezczynności: {{ convertTime(workingInfo.idleTimeInMinutes) }}</p>
            <p>Czas pracy po odliczeniu bezczynności:
              {{ convertTime(workingInfo.uptimeInMinutes - workingInfo.idleTimeInMinutes) }}</p>
          </div>
        </div>
      </div>
    </div>
    <vue-horizontal-timeline v-if="items.length > 0" :items="items" :title-substr="30"/>
    <div class="row" v-if="inactivityList.length > 0">
      <div class="col-md-12">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
            <tr>
              <th scope="col">Id</th>
              <th scope="col">Data wystąpienia</th>
              <th scope="col">Powód bezczynności</th>
              <th scope="col">Czas bezczynności</th>
              <th scope="col">Strona</th>
              <th scope="col" class="text-center">Akcje</th>
            </tr>
            </thead>
            <tbody v-if="isLoading">
            <div class="loader">Loading...</div>
            </tbody>
            <tbody v-else>
            <tr v-for="(inactivity,index) in inactivityList" :key="index">
              <td>{{ inactivity.id }}</td>
              <td style="width: 15%;">{{ inactivity.date }}</td>
              <td v-html="inactivity.description"></td>
              <td>{{ inactivity.time }}</td>
              <td>{{ inactivity.page }}</td>
              <td class="text-center">
                <button class="btn btn-success" @click="markInactivity(inactivity)">
                  <span>Oznacz jako czas pracy</span>
                </button>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
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
import { Event, Inactivity, searchWorkingEventsParams, User, WorkInfo } from '@/types/WorkingEventsTypes'

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

    return (events.concat(inactivity)).sort(
      (objA: Event | Inactivity, objB: Event | Inactivity) => objB.date - objA.date
    )
  }

  public get inactivityList (): Inactivity[] {
    return this.$store?.getters['WorkingEventsService/inactivity']
  }

  public get isLoading (): boolean {
    return this.$store?.getters['WorkingEventsService/isLoading']
  }

  public get workingInfo (): WorkInfo {
    return this.$store?.getters['WorkingEventsService/workingInfo']
  }

  private async markInactivity (inactivity: Inactivity): Promise<void> {
    await this.$store?.dispatch('WorkingEventsService/markInactivity', inactivity)
    const params: searchWorkingEventsParams = {
      userId: parseInt(this.user.value),
      date: this.date.value
    }
    await this.$store?.dispatch('WorkingEventsService/loadInactivity', params)
  }

  private convertTime (timeInMinutes: number) {
    let hoursString = '00'
    let minutesString = '00'
    const hours = Math.trunc(timeInMinutes / 60)
    if (hours < 10) {
      hoursString = '0' + hours
    }
    const minutes = timeInMinutes % 60
    if (minutes < 10) {
      minutesString = '0' + hours
    }
    return hoursString + ':' + minutesString
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

  .loader,
  .loader::before,
  .loader::after {
    border-radius: 50%;
  }

  .loader {
    color: $cl-whiteff;
    font-size: 11px;
    text-indent: -99999em;
    margin: 55px auto;
    position: relative;
    width: 10em;
    height: 10em;
    box-shadow: inset 0 0 0 1em;
    -webkit-transform: translateZ(0);
    -ms-transform: translateZ(0);
    transform: translateZ(0);
  }

  .loader::before,
  .loader::after {
    position: absolute;
    content: '';
  }

  .loader::before {
    width: 5.2em;
    height: 10.2em;
    background: $cl-blue2c;
    border-radius: 10.2em 0 0 10.2em;
    top: -0.1em;
    left: -0.1em;
    -webkit-transform-origin: 5.1em 5.1em;
    transform-origin: 5.1em 5.1em;
    -webkit-animation: load2 2s infinite ease 1.5s;
    animation: load2 2s infinite ease 1.5s;
  }

  .loader::after {
    width: 5.2em;
    height: 10.2em;
    background: $cl-blue2c;
    border-radius: 0 10.2em 10.2em 0;
    top: -0.1em;
    left: 4.9em;
    -webkit-transform-origin: 0.1em 5.1em;
    transform-origin: 0.1em 5.1em;
    -webkit-animation: load2 2s infinite ease;
    animation: load2 2s infinite ease;
  }

  @-webkit-keyframes load2 {
    0% {
      -webkit-transform: rotate(0deg);
      transform: rotate(0deg);
    }

    100% {
      -webkit-transform: rotate(360deg);
      transform: rotate(360deg);
    }
  }

  @keyframes load2 {
    0% {
      -webkit-transform: rotate(0deg);
      transform: rotate(0deg);
    }

    100% {
      -webkit-transform: rotate(360deg);
      transform: rotate(360deg);
    }
  }
</style>
