/* eslint-disable @typescript-eslint/explicit-module-boundary-types */
/* eslint-disable @typescript-eslint/no-explicit-any */

import setRepository from '@/store/repositories/SetsRepository'
import {
  Dates,
  OrdersStore,
  Acceptance
  // CreateSetParams, ProductStocks,
  // Set,
  // SetParams,
  // SetProduct,
  // SetProductParams,
  // SetsCount,
  // SetsProductParams,
  // SetsStore
} from '@/types/OrdersTypes'

import {
  ORDERS_SET_CUSTOMER_DATES,
  ORDERS_SET_CONSULTANT_DATES,
  ORDERS_SET_WAREHOUSE_DATES,
  ORDERS_SET_ACCEPTANCE,
} from '@/store/mutation-types'

const namespaced = true

const state: OrdersStore = {
  error: '',
  isLoading: false,
  customerDates: '',
}

const getters = {
  isLoading: (state: OrdersStore) => state.isLoading,
  error: (state: OrdersStore) => state.error,
  customerDates: (state: OrdersStore) => state.customerDates,
  consultantDates: (state: OrdersStore) => state.consultantDates,
  warehouseDates: (state: OrdersStore) => state.warehouseDates,
  acceptance: (state: OrdersStore) => state.acceptance
}

const actions = {
  loadDates({commit}: any) {
    commit(ORDERS_SET_CUSTOMER_DATES, true)
    commit(ORDERS_SET_CONSULTANT_DATES, true)
    commit(ORDERS_SET_WAREHOUSE_DATES, true)
    commit(ORDERS_SET_ACCEPTANCE, true)

    return setRepository
      .getSets()
      .then((data: any) => {
        commit(SETS_SET_IS_LOADING, false)
        if (data.error_code) {
          console.log(data)
          commit(SETS_SET_ERROR, data.error_message)
        }
        commit(SETS_SET_ALL, data)
        return data
      })
      .catch((error: any) => {
        commit(SETS_SET_ERROR, error.message)
      })
  },
  completing({commit}: any, set: SetsCount) {
    commit(SETS_SET_IS_LOADING, true)

    return setRepository
      .completingSets(set)
      .then((data: any) => {
        commit(SETS_SET_IS_LOADING, false)
        if (data.error_code) {
          console.log(data)
          commit(SETS_SET_ERROR, data.error_message)
        }
        return data
      })
      .catch((error: any) => {
        commit(SETS_SET_ERROR, error.message)
      })
  },
}

const mutations = {
  [ORDERS_SET_CUSTOMER_DATES](state: OrdersStore, customerDates: Dates) {
    state.customerDates = customerDates
  },
  [ORDERS_SET_CONSULTANT_DATES](state: OrdersStore, consultantDates: Dates) {
    state.consultantDates = consultantDates
  },
  [ORDERS_SET_WAREHOUSE_DATES](state: OrdersStore, warehouseDates: Dates) {
    state.warehouseDates = warehouseDates
  },
  [ORDERS_SET_ACCEPTANCE](state: OrdersStore, acceptance: Acceptance) {
    state.acceptance = acceptance
  }
}

export default {
  namespaced,
  state,
  getters,
  actions,
  mutations
}
