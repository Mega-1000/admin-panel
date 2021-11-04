/* eslint-disable @typescript-eslint/explicit-module-boundary-types */
/* eslint-disable @typescript-eslint/no-explicit-any */

import {
  CreateTransactionParams,
  Customer, Transaction,
  TransactionsStore
} from '@/types/TransactionsTypes'

import {
  TRANSACTIONS_SET_IS_LOADING,
  TRANSACTIONS_SET_ERROR,
  TRANSACTIONS_SET_ALL,
  TRANSACTIONS_SET_CUSTOMER, TRANSACTIONS_DELETE, TRANSACTIONS_SET_TRANSACTION
} from '@/store/mutation-types'

import TransactionsRepository from '@/store/repositories/TransactionsRepository'

const namespaced = true

const state: TransactionsStore = {
  error: '',
  isLoading: false,
  customers: [],
  customer: null,
  transaction: null
}

const getters = {
  isLoading: (state: TransactionsStore) => state.isLoading,
  error: (state: TransactionsStore) => state.error,
  customers: (state: TransactionsStore) => state.customers,
  customer: (state: TransactionsStore) => state.customer,
  transaction: (state: TransactionsStore) => state.transaction
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
        commit(TRANSACTIONS_SET_ALL, data.customers)
        return data.customers
      })
      .catch((error: any) => {
        commit(TRANSACTIONS_SET_ERROR, error.message)
      })
  },
  setCustomer ({ commit }: any, customer: Customer) {
    commit(TRANSACTIONS_SET_IS_LOADING, true)
    commit(TRANSACTIONS_SET_CUSTOMER, customer)
    commit(TRANSACTIONS_SET_IS_LOADING, false)
  },
  setTransaction ({ commit }: any, transaction: any) {
    commit(TRANSACTIONS_SET_IS_LOADING, true)
    commit(TRANSACTIONS_SET_TRANSACTION, transaction)
    commit(TRANSACTIONS_SET_IS_LOADING, false)
  },
  storeTransaction ({ commit }: any, params: CreateTransactionParams) {
    commit(TRANSACTIONS_SET_IS_LOADING, true)

    return TransactionsRepository
      .storeTransaction(params)
      .then((data: any) => {
        commit(TRANSACTIONS_SET_IS_LOADING, false)
        if (data.errorCode) {
          commit(TRANSACTIONS_SET_ERROR, data.errorMessage)
        }
        return data
      })
      .catch((error: any) => {
        commit(TRANSACTIONS_SET_ERROR, error.message)
      })
  },
  delete ({ commit }: any, transaction: Transaction) {
    commit(TRANSACTIONS_SET_IS_LOADING, true)

    return TransactionsRepository
      .deleteTransaction(transaction)
      .then((data: any) => {
        commit(TRANSACTIONS_SET_IS_LOADING, false)
        if (data.errorCode) {
          commit(TRANSACTIONS_SET_ERROR, data.errorMessage)
        } else {
          commit(TRANSACTIONS_DELETE, transaction)
        }
        return data
      })
      .catch((error: any) => {
        commit(TRANSACTIONS_SET_ERROR, error.message)
      })
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
  },
  [TRANSACTIONS_DELETE] (state: TransactionsStore, transaction: Transaction) {
    const transactions = state.customer?.transactions
    if (transactions !== undefined && transactions.length > 0) {
      const index = transactions.indexOf(transaction)
      transactions.splice(index, 1)
    }
  },
  [TRANSACTIONS_SET_TRANSACTION] (state: TransactionsStore, transaction: Transaction) {
    state.transaction = transaction
  }
}

export default {
  namespaced,
  state,
  getters,
  actions,
  mutations
}
