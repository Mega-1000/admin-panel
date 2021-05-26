/* eslint-disable @typescript-eslint/explicit-module-boundary-types */
/* eslint-disable @typescript-eslint/no-explicit-any */

import {
  Dates,
  OrdersStore,
  Acceptance, AcceptDatesParams, UpdateDatesParams
} from '@/types/OrdersTypes'

import {
  ORDERS_DATES_SET_CUSTOMER,
  ORDERS_DATES_SET_CONSULTANT,
  ORDERS_DATES_SET_WAREHOUSE,
  ORDERS_DATES_SET_ACCEPTANCE,
  ORDERS_DATES_SET_IS_LOADING,
  ORDERS_DATES_SET_ERROR
} from '@/store/mutation-types'

import OrdersRepository from '@/store/repositories/OrdersRepository'

const namespaced = true

const state: OrdersStore = {
  error: '',
  isLoading: false,
  customerDates: null,
  consultantDates: null,
  warehouseDates: null,
  acceptance: null
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
  loadDates ({ commit }: any, orderId: number) {
    commit(ORDERS_DATES_SET_IS_LOADING, true)

    return OrdersRepository
      .getDates(orderId)
      .then((data: any) => {
        commit(ORDERS_DATES_SET_IS_LOADING, false)
        if (data.error_code) {
          commit(ORDERS_DATES_SET_ERROR, data.error_message)
        }

        commit(ORDERS_DATES_SET_CUSTOMER, data.customer)
        commit(ORDERS_DATES_SET_CONSULTANT, data.consultant)
        commit(ORDERS_DATES_SET_WAREHOUSE, data.warehouse)
        commit(ORDERS_DATES_SET_ACCEPTANCE, data.acceptance)
        return data
      })
      .catch((error: any) => {
        commit(ORDERS_DATES_SET_ERROR, error.message)
      })
  },

  saveAccept ({ commit }: any, params: AcceptDatesParams) {
    commit(ORDERS_DATES_SET_IS_LOADING, true)

    return OrdersRepository.acceptDates(params)
      .then((data: any) => {
        commit(ORDERS_DATES_SET_IS_LOADING, false)
        if (data.error_code) {
          commit(ORDERS_DATES_SET_ERROR, data.error_message)
        }

        commit(ORDERS_DATES_SET_ACCEPTANCE, data.acceptance)
        return data
      })
      .catch((error: any) => {
        commit(ORDERS_DATES_SET_ERROR, error.message)
      })
  },

  updateDateParams ({ commit }: any, params: UpdateDatesParams) {
    commit(ORDERS_DATES_SET_IS_LOADING, true)

    return OrdersRepository.updateDatesParams(params)
      .then((data: any) => {
        commit(ORDERS_DATES_SET_IS_LOADING, false)
        if (data.error_code) {
          commit(ORDERS_DATES_SET_ERROR, data.error_message)
        }

        commit(`ORDERS_DATES_SET_${params.type.toUpperCase()}`, data[params.type])
        commit(ORDERS_DATES_SET_ACCEPTANCE, data.acceptance)
        return data
      })
      .catch((error: any) => {
        commit(ORDERS_DATES_SET_ERROR, error.message)
      })
  }
}

const mutations = {
  [ORDERS_DATES_SET_CUSTOMER] (state: OrdersStore, customerDates: Dates) {
    state.customerDates = customerDates
  },
  [ORDERS_DATES_SET_CONSULTANT] (state: OrdersStore, consultantDates: Dates) {
    state.consultantDates = consultantDates
  },
  [ORDERS_DATES_SET_WAREHOUSE] (state: OrdersStore, warehouseDates: Dates) {
    state.warehouseDates = warehouseDates
  },
  [ORDERS_DATES_SET_ACCEPTANCE] (state: OrdersStore, acceptance: Acceptance) {
    state.acceptance = acceptance
  },
  [ORDERS_DATES_SET_IS_LOADING] (state: OrdersStore, isLoading: boolean) {
    state.isLoading = isLoading
  }
}

export default {
  namespaced,
  state,
  getters,
  actions,
  mutations
}
