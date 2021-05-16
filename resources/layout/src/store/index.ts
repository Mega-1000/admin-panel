import Vue from 'vue'
import Vuex from 'vuex'
import SetsService from '@/store/services/SetsService'
import LogsTrackerService from '@/store/services/LogsTrackerService'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    SetsService,
    LogsTrackerService
  }
})
