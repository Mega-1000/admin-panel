import Vue from 'vue'
import Vuex from 'vuex'
import SetsService from '@/store/services/SetsService'
import OrdersService from '@/store/services/OrdersService'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    SetsService,
    OrdersService
  }
})
