import setRepository from '@/store/repositories/SetsRepository'
import { Set, SetsCount, SetsStore } from '@/types/SetsTypes'

import {
  SETS_SET_ALL,
  SETS_SET_ERROR,
  SETS_SET_IS_LOADING
} from '@/store/mutation-types'

const namespaced = true

const state: SetsStore = {
  error: '',
  isLoading: false,
  sets: []
}

const getters = {
  isLoading: (state: SetsStore) => state.isLoading,
  error: (state: SetsStore) => state.error,
  sets: (state: SetsStore) => state.sets
}

const actions = {
  loadSets ({ commit }: any) {
    commit(SETS_SET_IS_LOADING, true)

    return setRepository
      .getSets()
      .then((data: any) => {
        commit(SETS_SET_IS_LOADING, false)
        commit(SETS_SET_ALL, data)
        return data
      })
      .catch((error: any) => {
        commit(SETS_SET_ERROR, error.message)
      })
  },
  completing ({ commit }: any, set: SetsCount) {
    commit(SETS_SET_IS_LOADING, true)

    return setRepository
      .completingSets(set)
      .then((data: any) => {
        commit(SETS_SET_IS_LOADING, false)
        return data
      })
      .catch((error: any) => {
        commit(SETS_SET_ERROR, error.message)
      })
  },
  disassembly ({ commit }: any, set: SetsCount) {
    commit(SETS_SET_IS_LOADING, true)

    return setRepository
      .disassemblySets(set)
      .then((data: any) => {
        commit(SETS_SET_IS_LOADING, false)
        return data
      })
      .catch((error: any) => {
        commit(SETS_SET_ERROR, error.message)
      })
  },
  delete ({ commit }: any, id: number) {
    commit(SETS_SET_IS_LOADING, true)

    return setRepository
      .deleteSet(id)
      .then((data: any) => {
        commit(SETS_SET_IS_LOADING, false)
        return data
      })
      .catch((error: any) => {
        commit(SETS_SET_ERROR, error.message)
      })
  }
}

const mutations = {
  [SETS_SET_ALL] (state: SetsStore, sets: Set[]) {
    state.sets = sets
  },
  [SETS_SET_ERROR] (state: SetsStore, error: string) {
    state.error = error
  },
  [SETS_SET_IS_LOADING] (state: SetsStore, isLoading: boolean) {
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
