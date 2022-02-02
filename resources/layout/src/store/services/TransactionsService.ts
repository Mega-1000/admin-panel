/* eslint-disable @typescript-eslint/explicit-module-boundary-types */
/* eslint-disable @typescript-eslint/no-explicit-any */

import {
  CreateTransactionParams,
  Customer,
  ImportFileParams,
  ProviderTransactions,
  searchCustomersParams,
  searchProvidersTransactionsParams,
  Transaction,
  TransactionsStore
} from '@/types/TransactionsTypes'

import {
  TRANSACTIONS_SET_IS_LOADING,
  TRANSACTIONS_SET_ERROR,
  TRANSACTIONS_SET_ALL,
  TRANSACTIONS_SET_CUSTOMER,
  TRANSACTIONS_DELETE,
  TRANSACTIONS_SET_TRANSACTION,
  IMPORT_TRANSACTIONS_SET_IS_LOADING,
  TRANSACTIONS_SET_TRANSACTIONS, TRANSACTIONS_SET_PROVIDERS_TRANSACTIONS
} from '@/store/mutation-types'

import TransactionsRepository from '@/store/repositories/TransactionsRepository'

const namespaced = true

const state: TransactionsStore = {
  error: '',
  isLoading: false,
  importIsLoading: false,
  customers: [],
  customer: null,
  transaction: null,
  transactions: [],
  providersTransactions: [],
  pageCount: null,
  currentPage: 1
}

const getters = {
  isLoading: (state: TransactionsStore) => state.isLoading,
  importIsLoading: (state: TransactionsStore) => state.importIsLoading,
  error: (state: TransactionsStore) => state.error,
  customers: (state: TransactionsStore) => state.customers,
  customer: (state: TransactionsStore) => state.customer,
  transaction: (state: TransactionsStore) => state.transaction,
  transactions: (state: TransactionsStore) => state.transactions,
  providersTransactions: (state: TransactionsStore) => state.providersTransactions,
  pageCount: (state: TransactionsStore) => state.pageCount,
  currentPage: (state: TransactionsStore) => state.currentPage
}

const actions = {
  loadTransactions ({ commit }: any, params: searchCustomersParams) {
    commit(TRANSACTIONS_SET_IS_LOADING, true)
    return TransactionsRepository
      .getTransactions(params)
      .then((data: any) => {
        commit(TRANSACTIONS_SET_IS_LOADING, false)
        if (data.errorCode) {
          commit(TRANSACTIONS_SET_ERROR, data.errorMessage)
        }
        commit(TRANSACTIONS_SET_ALL, data.customers)
        state.currentPage = data.currentPage
        state.pageCount = data.lastPage
        return data.customers
      })
      .catch((error: any) => {
        commit(TRANSACTIONS_SET_ERROR, error.errorMessage)
      })
  },
  loadCustomerTransactions ({ commit }: any) {
    commit(TRANSACTIONS_SET_IS_LOADING, true)

    return TransactionsRepository
      .getCustomerTransactions(state.customer ?? undefined)
      .then((data: any) => {
        commit(TRANSACTIONS_SET_IS_LOADING, false)
        if (data.errorCode) {
          commit(TRANSACTIONS_SET_ERROR, data.errorMessage)
        }
        commit(TRANSACTIONS_SET_TRANSACTIONS, data.transactions)

        return data.transactions
      })
      .catch((error: any) => {
        commit(TRANSACTIONS_SET_ERROR, error.errorMessage)
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
        commit(TRANSACTIONS_SET_ERROR, error.errorMessage)
      })
  },
  updateTransaction ({ commit }: any, params: CreateTransactionParams) {
    commit(TRANSACTIONS_SET_IS_LOADING, true)

    return TransactionsRepository
      .updateTransaction(params)
      .then((data: any) => {
        commit(TRANSACTIONS_SET_IS_LOADING, false)
        if (data.errorCode) {
          commit(TRANSACTIONS_SET_ERROR, data.errorMessage)
        }
        return data
      })
      .catch((error: any) => {
        commit(TRANSACTIONS_SET_ERROR, error.errorMessage)
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
        commit(TRANSACTIONS_SET_ERROR, error.errorMessage)
      })
  },
  import ({ commit }: any, params: ImportFileParams) {
    commit(IMPORT_TRANSACTIONS_SET_IS_LOADING, true)

    return TransactionsRepository
      .importTransaction(params)
      .then((data: any) => {
        commit(IMPORT_TRANSACTIONS_SET_IS_LOADING, false)
        if (data.errorCode) {
          commit(TRANSACTIONS_SET_ERROR, data.errorMessage)
        }
        return data
      })
      .catch((error: any) => {
        commit(IMPORT_TRANSACTIONS_SET_IS_LOADING, error.errorMessage)
      })
  },
  setErrorMessage ({ commit }: any, errorMessage: string) {
    commit(TRANSACTIONS_SET_IS_LOADING, true)
    commit(TRANSACTIONS_SET_ERROR, errorMessage)
    commit(TRANSACTIONS_SET_IS_LOADING, false)
  },
  loadProvidersTransactions ({ commit }: any, params: searchProvidersTransactionsParams) {
    commit(TRANSACTIONS_SET_IS_LOADING, true)

    return TransactionsRepository
      .getProvidersTransactions(params)
      .then((data: any) => {
        commit(TRANSACTIONS_SET_IS_LOADING, false)
        if (data.errorCode) {
          commit(TRANSACTIONS_SET_ERROR, data.errorMessage)
        }
        commit(TRANSACTIONS_SET_PROVIDERS_TRANSACTIONS, data.transactions ?? [])
        state.currentPage = data.currentPage
        state.pageCount = data.lastPage
        return data.transactions
      })
      .catch((error: any) => {
        commit(TRANSACTIONS_SET_ERROR, error.errorMessage)
      })
  }
}

const mutations = {
  [TRANSACTIONS_SET_IS_LOADING] (state: TransactionsStore, isLoading: boolean) {
    state.isLoading = isLoading
  },
  [IMPORT_TRANSACTIONS_SET_IS_LOADING] (state: TransactionsStore, importIsLoading: boolean) {
    state.importIsLoading = importIsLoading
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
  },
  [TRANSACTIONS_SET_TRANSACTIONS] (state: TransactionsStore, transactions: Transaction[]) {
    state.transactions = transactions
  },
  [TRANSACTIONS_SET_PROVIDERS_TRANSACTIONS] (state: TransactionsStore, transactions: ProviderTransactions[]) {
    state.providersTransactions = transactions
  }
}

export default {
  namespaced,
  state,
  getters,
  actions,
  mutations
}
