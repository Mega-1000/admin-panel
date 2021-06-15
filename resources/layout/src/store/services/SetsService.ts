/* eslint-disable @typescript-eslint/explicit-module-boundary-types */
/* eslint-disable @typescript-eslint/no-explicit-any */

import setRepository from '@/store/repositories/SetsRepository'
import {
  CreateSetParams, ProductStocks,
  Set,
  SetParams,
  SetProduct,
  SetProductParams,
  SetsCount,
  SetsProductParams,
  SetsStore
} from '@/types/SetsTypes'

import {
  SETS_SET_ALL,
  SETS_SET_ERROR,
  SETS_SET_IS_LOADING,
  SETS_SET_PRODUCTS,
  SETS_SET_SETITEM,
  SETS_SET_PRODUCTS_STOCKS
} from '@/store/mutation-types'

const namespaced = true

const state: SetsStore = {
  error: '',
  isLoading: false,
  sets: [],
  products: [],
  set: null,
  productsStocks: []
}

const getters = {
  isLoading: (state: SetsStore) => state.isLoading,
  error: (state: SetsStore) => state.error,
  sets: (state: SetsStore) => state.sets,
  set: (state: SetsStore) => state.set,
  products: (state: SetsStore) => state.products,
  productsStocks: (state: SetsStore) => state.productsStocks
}

const actions = {
  loadSets ({ commit }: any) {
    commit(SETS_SET_IS_LOADING, true)

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
        console.log(error)
        commit(SETS_SET_ERROR, 'Wystąpił nieoczekiwany błąd. Spróbuj później.')
      })
  },
  completing ({ commit }: any, set: SetsCount) {
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
        console.log(error)
        commit(SETS_SET_ERROR, 'Wystąpił nieoczekiwany błąd. Spróbuj później.')
      })
  },
  disassembly ({ commit }: any, set: SetsCount) {
    commit(SETS_SET_IS_LOADING, true)

    return setRepository
      .disassemblySets(set)
      .then((data: any) => {
        commit(SETS_SET_IS_LOADING, false)
        if (data.error_code) {
          console.log(data)
          commit(SETS_SET_ERROR, data.error_message)
        }
        return data
      })
      .catch((error: any) => {
        console.log(error)
        commit(SETS_SET_ERROR, 'Wystąpił nieoczekiwany błąd. Spróbuj później.')
      })
  },
  delete ({ commit }: any, id: number) {
    commit(SETS_SET_IS_LOADING, true)

    return setRepository
      .deleteSet(id)
      .then((data: any) => {
        commit(SETS_SET_IS_LOADING, false)
        if (data.error_code) {
          console.log(data)
          commit(SETS_SET_ERROR, data.error_message)
        }
        return data
      })
      .catch((error: any) => {
        console.log(error)
        commit(SETS_SET_ERROR, 'Wystąpił nieoczekiwany błąd. Spróbuj później.')
      })
  },
  loadProducts ({ commit }: any, params: SetsProductParams) {
    commit(SETS_SET_IS_LOADING, true)

    return setRepository
      .products(params)
      .then((data: any) => {
        commit(SETS_SET_IS_LOADING, false)
        if (data.error_code) {
          console.log(data)
          commit(SETS_SET_ERROR, data.error_message)
        }
        commit(SETS_SET_PRODUCTS, data)
        return data
      })
      .catch((error: any) => {
        console.log(error)
        commit(SETS_SET_ERROR, 'Wystąpił nieoczekiwany błąd. Spróbuj później.')
      })
  },
  loadSet ({ commit }: any, id: number) {
    commit(SETS_SET_IS_LOADING, true)
    return setRepository
      .setItem(id)
      .then((data: any) => {
        commit(SETS_SET_IS_LOADING, false)
        if (data.error_code) {
          console.log(data)
          commit(SETS_SET_ERROR, data.error_message)
        }
        commit(SETS_SET_SETITEM, data)
        return data
      })
      .catch((error: any) => {
        console.log(error)
        commit(SETS_SET_ERROR, 'Wystąpił nieoczekiwany błąd. Spróbuj później.')
      })
  },
  updateSet ({ commit }: any, set: SetParams) {
    commit(SETS_SET_IS_LOADING, true)
    return setRepository
      .setItemUpdate(set)
      .then((data: any) => {
        commit(SETS_SET_IS_LOADING, false)
        if (data.error_code) {
          console.log(data)
          commit(SETS_SET_ERROR, data.error_message)
        }
        return data
      })
      .catch((error: any) => {
        console.log(error)
        commit(SETS_SET_ERROR, 'Wystąpił nieoczekiwany błąd. Spróbuj później.')
      })
  },
  cerateSetFromProduct ({ commit }: any, productId: number) {
    commit(SETS_SET_IS_LOADING, true)
    return setRepository
      .createSetFromProduct(productId)
      .then((data: any) => {
        commit(SETS_SET_IS_LOADING, false)
        if (data.error_code) {
          console.log(data)
          commit(SETS_SET_ERROR, data.error_message)
        }
        return data
      })
      .catch((error: any) => {
        console.log(error)
        commit(SETS_SET_ERROR, 'Wystąpił nieoczekiwany błąd. Spróbuj później.')
      })
  },
  cerateSet ({ commit }: any, params: CreateSetParams) {
    commit(SETS_SET_IS_LOADING, true)
    return setRepository
      .createSet(params)
      .then((data: any) => {
        commit(SETS_SET_IS_LOADING, false)
        if (data.error_code) {
          console.log(data)
          commit(SETS_SET_ERROR, data.error_message)
        }
        return data
      })
      .catch((error: any) => {
        console.log(error)
        commit(SETS_SET_ERROR, 'Wystąpił nieoczekiwany błąd. Spróbuj później.')
      })
  },
  addSetProduct ({ commit }: any, params: SetProductParams) {
    commit(SETS_SET_IS_LOADING, true)
    return setRepository
      .setProductAdd(params)
      .then((data: any) => {
        commit(SETS_SET_IS_LOADING, false)
        if (data.error_code) {
          console.log(data)
          commit(SETS_SET_ERROR, data.error_message)
        }
        return data
      })
      .catch((error: any) => {
        console.log(error)
        commit(SETS_SET_ERROR, 'Wystąpił nieoczekiwany błąd. Spróbuj później.')
      })
  },
  updateSetProduct ({ commit }: any, params: SetProductParams) {
    commit(SETS_SET_IS_LOADING, true)
    return setRepository
      .setProductUpdate(params)
      .then((data: any) => {
        commit(SETS_SET_IS_LOADING, false)
        if (data.error_code) {
          console.log(data)
          commit(SETS_SET_ERROR, data.error_message)
        }
        return data
      })
      .catch((error: any) => {
        console.log(error)
        commit(SETS_SET_ERROR, error)
      })
  },
  deleteSetProduct ({ commit }: any, params: SetProductParams) {
    commit(SETS_SET_IS_LOADING, true)
    return setRepository
      .setProductDelete(params)
      .then((data: any) => {
        commit(SETS_SET_IS_LOADING, false)
        if (data.error_code) {
          console.log(data)
          commit(SETS_SET_ERROR, data.error_message)
        }
        return data
      })
      .catch((error: any) => {
        console.log(error)
        commit(SETS_SET_ERROR, 'Wystąpił nieoczekiwany błąd. Spróbuj później.')
      })
  },
  getProductsStocks ({ commit }: any, setId: number) {
    commit(SETS_SET_IS_LOADING, true)
    return setRepository
      .productsStock(setId)
      .then((data: any) => {
        commit(SETS_SET_IS_LOADING, false)
        if (data.error_code) {
          console.log(data)
          commit(SETS_SET_ERROR, data.error_message)
        }
        commit(SETS_SET_PRODUCTS_STOCKS, data)
        return data
      })
      .catch((error: any) => {
        console.log(error)
        commit(SETS_SET_ERROR, 'Wystąpił nieoczekiwany błąd. Spróbuj później.')
      })
  }
}

const mutations = {
  [SETS_SET_ALL] (state: SetsStore, sets: Set[]) {
    state.sets = sets
  },
  [SETS_SET_SETITEM] (state: SetsStore, set: Set) {
    state.set = set
  },
  [SETS_SET_PRODUCTS] (state: SetsStore, products: SetProduct[]) {
    state.products = products
  },
  [SETS_SET_ERROR] (state: SetsStore, error: string) {
    state.error = error
  },
  [SETS_SET_IS_LOADING] (state: SetsStore, isLoading: boolean) {
    state.isLoading = isLoading
  },
  [SETS_SET_PRODUCTS_STOCKS] (state: SetsStore, productsStocks: ProductStocks[]) {
    state.productsStocks = productsStocks
  }
}

export default {
  namespaced,
  state,
  getters,
  actions,
  mutations
}
