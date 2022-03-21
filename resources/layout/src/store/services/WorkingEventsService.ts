/* eslint-disable @typescript-eslint/explicit-module-boundary-types */
/* eslint-disable @typescript-eslint/no-explicit-any */

import { Event, User, searchWorkingEventsParams, WorkingEventsStore, Inactivity } from '@/types/WorkingEventsTypes'
import {
  WORKING_EVENTS_SET_IS_LOADING,
  WORKING_EVENTS_SET_ERROR,
  WORKING_EVENTS_ALL,
  WORKING_EVENTS_SET_WORKERS,
  WORKING_EVENTS_SET_INACTIVITY
} from '@/store/mutation-types'
import WorkingEventsRepository from '@/store/repositories/WorkingEventsRepository'
const namespaced = true

const state: WorkingEventsStore = {
  error: '',
  isLoading: false,
  users: [],
  events: [],
  inactivity: []
}

const getters = {
  isLoading: (state: WorkingEventsStore) => state.isLoading,
  error: (state: WorkingEventsStore) => state.error,
  users: (state: WorkingEventsStore) => state.users,
  events: (state: WorkingEventsStore) => state.events,
  inactivity: (state: WorkingEventsStore) => state.inactivity
}

const actions = {
  loadWorkingEvents ({ commit }: any, params: searchWorkingEventsParams) {
    commit(WORKING_EVENTS_SET_IS_LOADING, true)
    return WorkingEventsRepository
      .getWorkingEvents(params)
      .then((data: any) => {
        commit(WORKING_EVENTS_SET_IS_LOADING, false)
        if (data.errorCode) {
          commit(WORKING_EVENTS_SET_ERROR, data.errorMessage)
        }
        commit(WORKING_EVENTS_ALL, data.workingEvents)
        return data.workingEvents
      })
      .catch((error: any) => {
        commit(WORKING_EVENTS_SET_ERROR, error.errorMessage)
      })
  },
  loadInactivity ({ commit }: any, params: searchWorkingEventsParams) {
    commit(WORKING_EVENTS_SET_IS_LOADING, true)
    return WorkingEventsRepository
      .getInactivity(params)
      .then((data: any) => {
        commit(WORKING_EVENTS_SET_IS_LOADING, false)
        if (data.errorCode) {
          commit(WORKING_EVENTS_SET_ERROR, data.errorMessage)
        }
        commit(WORKING_EVENTS_SET_INACTIVITY, data.inactivity)
        return data.workingEvents
      })
      .catch((error: any) => {
        commit(WORKING_EVENTS_SET_ERROR, error.errorMessage)
      })
  },
  loadWorkers ({ commit }: any) {
    commit(WORKING_EVENTS_SET_IS_LOADING, true)
    return WorkingEventsRepository
      .getWorkers()
      .then((data: any) => {
        commit(WORKING_EVENTS_SET_IS_LOADING, false)
        if (data.errorCode) {
          commit(WORKING_EVENTS_SET_ERROR, data.errorMessage)
        }
        commit(WORKING_EVENTS_SET_WORKERS, data.users)
        return data.workingEvents
      })
      .catch((error: any) => {
        commit(WORKING_EVENTS_SET_ERROR, error.errorMessage)
      })
  }
}

const mutations = {
  [WORKING_EVENTS_SET_IS_LOADING] (state: WorkingEventsStore, isLoading: boolean) {
    state.isLoading = isLoading
  },
  [WORKING_EVENTS_SET_ERROR] (state: WorkingEventsStore, error: string) {
    state.error = error
  },
  [WORKING_EVENTS_ALL] (state: WorkingEventsStore, events: Event[]) {
    state.events = events
  },
  [WORKING_EVENTS_SET_INACTIVITY] (state: WorkingEventsStore, inactivity: Inactivity[]) {
    state.inactivity = inactivity
  },
  [WORKING_EVENTS_SET_WORKERS] (state: WorkingEventsStore, users: User[]) {
    state.users = users
  }
}

export default {
  namespaced,
  state,
  getters,
  actions,
  mutations
}
