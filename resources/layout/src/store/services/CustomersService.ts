/* eslint-disable @typescript-eslint/explicit-module-boundary-types */
/* eslint-disable @typescript-eslint/no-explicit-any */

import {
  Customer,
  CustomersStore, searchCustomerParams
} from '@/types/CustomersTypes'

import {
  CUSTOMERS_SET_IS_LOADING,
  CUSTOMERS_SET_ERROR,
  CUSTOMERS_SET_ALL
} from '@/store/mutation-types'

import CustomersRepository from '@/store/repositories/CustomersRepository'

const namespaced = true

const state: CustomersStore = {
  error: '',
  isLoading: false,
  customers: []
}

const getters = {
  isLoading: (state: CustomersStore) => state.isLoading,
  error: (state: CustomersStore) => state.error,
  customers: (state: CustomersStore) => state.customers
}

const actions = {
  loadCustomers ({ commit }: any, params: searchCustomerParams) {
    commit(CUSTOMERS_SET_IS_LOADING, true)

    return CustomersRepository
      .getCustomers(params)
      .then((data: any) => {
        commit(CUSTOMERS_SET_IS_LOADING, false)
        if (data.error_code) {
          commit(CUSTOMERS_SET_ERROR, data.error_message)
        } else {
          commit(CUSTOMERS_SET_ERROR, '')
        }
        commit(CUSTOMERS_SET_ALL, data.customers)
        return data.customers
      })
      .catch((error: any) => {
        commit(CUSTOMERS_SET_ERROR, error.message)
      })
  }
}

const mutations = {
  [CUSTOMERS_SET_IS_LOADING] (state: CustomersStore, isLoading: boolean) {
    state.isLoading = isLoading
  },
  [CUSTOMERS_SET_ERROR] (state: CustomersStore, error: string) {
    state.error = error
  },
  [CUSTOMERS_SET_ALL] (state: CustomersStore, customers: Customer[]) {
    state.customers = customers
  }
}

export default {
  namespaced,
  state,
  getters,
  actions,
  mutations
}
