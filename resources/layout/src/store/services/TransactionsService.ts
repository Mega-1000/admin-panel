/* eslint-disable @typescript-eslint/explicit-module-boundary-types */
/* eslint-disable @typescript-eslint/no-explicit-any */

import {
  Customer,
  TransactionsStore
} from '@/types/TransactionsTypes'

import {
  TRANSACTIONS_SET_IS_LOADING,
  TRANSACTIONS_SET_ERROR,
  TRANSACTIONS_SET_ALL,
  TRANSACTIONS_SET_CUSTOMER
} from '@/store/mutation-types'

import TransactionsRepository from '@/store/repositories/TransactionsRepository'

const namespaced = true

const state: TransactionsStore = {
  error: '',
  isLoading: false,
  customers: [],
  customer: null
}

const getters = {
  isLoading: (state: TransactionsStore) => state.isLoading,
  error: (state: TransactionsStore) => state.error,
  customers: (state: TransactionsStore) => state.customers,
  customer: (state: TransactionsStore) => state.customer
}

const actions = {
  loadTransactions ({ commit }: any) {
    commit(TRANSACTIONS_SET_IS_LOADING, true)

    return TransactionsRepository
      .getTransactions()
      .then((data: any) => {
        commit(TRANSACTIONS_SET_IS_LOADING, false)
        if (data.error_code) {
          commit(TRANSACTIONS_SET_ERROR, data.error_message)
        }
        commit(TRANSACTIONS_SET_ALL, data)
        return data
      })
      .catch((error: any) => {
        commit(TRANSACTIONS_SET_ERROR, error.message)
      })
  },
  setCustomer ({ commit }: any, customer: Customer) {
    commit(TRANSACTIONS_SET_IS_LOADING, true)
    commit(TRANSACTIONS_SET_CUSTOMER, customer)
    commit(TRANSACTIONS_SET_IS_LOADING, false)
  }
}

const mutations = {
  [TRANSACTIONS_SET_IS_LOADING] (state: TransactionsStore, isLoading: boolean) {
    state.isLoading = isLoading
  },
  [TRANSACTIONS_SET_ERROR] (state: TransactionsStore, error: string) {
    state.error = error
  },
  [TRANSACTIONS_SET_ALL] (state: TransactionsStore, customers: Customer[]) {
    state.customers = customers
  },
  [TRANSACTIONS_SET_CUSTOMER] (state: TransactionsStore, customer: Customer) {
    state.customer = customer
  }
}

export default {
  namespaced,
  state,
  getters,
  actions,
  mutations
}
