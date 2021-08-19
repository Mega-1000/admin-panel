/* eslint-disable @typescript-eslint/explicit-module-boundary-types */
/* eslint-disable @typescript-eslint/no-explicit-any */

import {
  TransactionsStore
} from '@/types/TransactionsTypes'

import {
  TRANSACTIONS_SET_CUSTOMER,
  TRANSACTIONS_SET_TRANSACTION,
  TRANSACTIONS_SET_IS_LOADING,
  TRANSACTIONS_SET_ERROR
} from '@/store/mutation-types'

import TransactionsRepository from '@/store/repositories/TransactionsRepository'

const namespaced = true

const state: TransactionsStore = {
  error: '',
  isLoading: false,
  customers: null
}

const getters = {
  isLoading: (state: TransactionsStore) => state.isLoading,
  error: (state: TransactionsStore) => state.error
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

        return data
      })
      .catch((error: any) => {
        commit(TRANSACTIONS_SET_ERROR, error.message)
      })
  }
}

const mutations = {

}

export default {
  namespaced,
  state,
  getters,
  actions,
  mutations
}
