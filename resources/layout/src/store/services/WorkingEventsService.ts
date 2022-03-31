/* eslint-disable @typescript-eslint/explicit-module-boundary-types */
/* eslint-disable @typescript-eslint/no-explicit-any */

import {
  Event,
  User,
  searchWorkingEventsParams,
  WorkingEventsStore,
  Inactivity,
  WorkInfo
} from '@/types/WorkingEventsTypes'
import {
  WORKING_EVENTS_SET_IS_LOADING,
  WORKING_EVENTS_SET_ERROR,
  WORKING_EVENTS_ALL,
  WORKING_EVENTS_SET_WORKERS,
  WORKING_EVENTS_SET_INACTIVITY,
  WORKING_EVENTS_MARK_INACTIVITY,
  WORKING_INFO, WORKING_EVENTS_SET_INACTIVITY_TIMES
} from '@/store/mutation-types'
import WorkingEventsRepository from '@/store/repositories/WorkingEventsRepository'

const namespaced = true

const state: WorkingEventsStore = {
  error: '',
  isLoading: false,
  users: [],
  events: [],
  inactivity: [],
  workingInfo: null
}

const getters = {
  isLoading: (state: WorkingEventsStore) => state.isLoading,
  error: (state: WorkingEventsStore) => state.error,
  users: (state: WorkingEventsStore) => state.users,
  events: (state: WorkingEventsStore) => state.events,
  inactivity: (state: WorkingEventsStore) => state.inactivity,
  workingInfo: (state: WorkingEventsStore) => state.workingInfo
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
        } else {
          commit(WORKING_EVENTS_ALL, data.workingEvents)
          commit(WORKING_INFO, data.workInfo)
          return data.workingEvents
        }
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
        } else {
          commit(WORKING_EVENTS_SET_INACTIVITY, data.inactivity)
          commit(WORKING_EVENTS_SET_INACTIVITY_TIMES, data.workInfo.idleTimeSummaryInMinutes)
          return data.workingEvents
        }
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
  },
  markInactivity ({ commit }: any, inactivity: Inactivity) {
    commit(WORKING_EVENTS_SET_IS_LOADING, true)

    return WorkingEventsRepository
      .markInactivity(inactivity)
      .then((data: any) => {
        commit(WORKING_EVENTS_SET_IS_LOADING, false)
        if (data.errorCode) {
          commit(WORKING_EVENTS_SET_ERROR, data.errorMessage)
        } else {
          commit(WORKING_EVENTS_MARK_INACTIVITY, inactivity)
        }
        return data
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
  [WORKING_EVENTS_MARK_INACTIVITY] (state: WorkingEventsStore, inactivity: Inactivity) {
    const inactivityList = state.inactivity
    if (inactivityList !== null && inactivityList.length > 0) {
      const index = inactivityList.indexOf(inactivity)
      inactivityList.splice(index, 1)
    }
  },
  [WORKING_EVENTS_SET_WORKERS] (state: WorkingEventsStore, users: User[]) {
    state.users = users
  },
  [WORKING_INFO] (state: WorkingEventsStore, workingInfo: WorkInfo) {
    state.workingInfo = workingInfo
  },
  [WORKING_EVENTS_SET_INACTIVITY_TIMES] (state: WorkingEventsStore, idleTimeSummaryInMinutes: number) {
    if (state.workingInfo !== null) {
      state.workingInfo.idleTimeInMinutes = idleTimeSummaryInMinutes
    }
  }
}

export default {
  namespaced,
  state,
  getters,
  actions,
  mutations
}
