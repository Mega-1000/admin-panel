/* eslint-disable @typescript-eslint/explicit-module-boundary-types */
/* eslint-disable @typescript-eslint/no-explicit-any */

import LogsTrackerRepository from '@/store/repositories/LogsTrackerRepository'

import {
  LOGS_TRACKER_SET_LOGS,
  LOGS_TRACKER_SET_LOG,
  LOGS_TRACKER_SET_ERROR,
  LOGS_TRACKER_SET_IS_LOADING
} from '@/store/mutation-types'
import {
  addLogParam,
  LogItem,
  LogsTrackerStore,
  updateDescriptionLogParam,
  updateTimeLogParam
} from '@/types/LogsTrackerType'

const namespaced = true

const state: LogsTrackerStore = {
  error: '',
  isLoading: false,
  logs: [],
  log: null
}

const getters = {
  isLoading: (state: LogsTrackerStore) => state.isLoading,
  error: (state: LogsTrackerStore) => state.error,
  logs: (state: LogsTrackerStore) => state.logs,
  log: (state: LogsTrackerStore) => state.log
}

const actions = {
  loadLogs ({ commit }: any) {
    commit(LOGS_TRACKER_SET_IS_LOADING, true)

    return LogsTrackerRepository
      .getLogs()
      .then((data: any) => {
        commit(LOGS_TRACKER_SET_IS_LOADING, false)
        if (data.error_code) {
          commit(LOGS_TRACKER_SET_ERROR, data.error_message)
        }
        commit(LOGS_TRACKER_SET_LOGS, data)
        return data
      })
      .catch((error: any) => {
        commit(LOGS_TRACKER_SET_ERROR, error.message)
      })
  },
  setLog ({ commit }: any, param: addLogParam) {
    commit(LOGS_TRACKER_SET_IS_LOADING, true)

    return LogsTrackerRepository
      .newLog(param)
      .then((data: any) => {
        commit(LOGS_TRACKER_SET_IS_LOADING, false)
        if (data.error_code) {
          commit(LOGS_TRACKER_SET_ERROR, data.error_message)
        }
        commit(LOGS_TRACKER_SET_LOG, data)
        return data
      })
      .catch((error: any) => {
        commit(LOGS_TRACKER_SET_ERROR, error.message)
      })
  },
  updateTimeLog ({ commit }: any, param: updateTimeLogParam) {
    commit(LOGS_TRACKER_SET_IS_LOADING, true)

    return LogsTrackerRepository
      .updateTimeLog(param)
      .then((data: any) => {
        commit(LOGS_TRACKER_SET_IS_LOADING, false)
        if (data.error_code) {
          commit(LOGS_TRACKER_SET_ERROR, data.error_message)
        }
        commit(LOGS_TRACKER_SET_LOG, data)
        return data
      })
      .catch((error: any) => {
        commit(LOGS_TRACKER_SET_ERROR, error.message)
      })
  },
  updateDescriptionLog ({ commit }: any, param: updateDescriptionLogParam) {
    commit(LOGS_TRACKER_SET_IS_LOADING, true)

    return LogsTrackerRepository
      .updateDescriptionLog(param)
      .then((data: any) => {
        commit(LOGS_TRACKER_SET_IS_LOADING, false)
        if (data.error_code) {
          commit(LOGS_TRACKER_SET_ERROR, data.error_message)
        }
        commit(LOGS_TRACKER_SET_LOG, data)
        return data
      })
      .catch((error: any) => {
        commit(LOGS_TRACKER_SET_ERROR, error.message)
      })
  }
}

const mutations = {
  [LOGS_TRACKER_SET_ERROR] (state: LogsTrackerStore, error: string) {
    state.error = error
  },
  [LOGS_TRACKER_SET_IS_LOADING] (state: LogsTrackerStore, isLoading: boolean) {
    state.isLoading = isLoading
  },
  [LOGS_TRACKER_SET_LOGS] (state: LogsTrackerStore, logs: LogItem[]) {
    state.logs = logs
  },
  [LOGS_TRACKER_SET_LOG] (state: LogsTrackerStore, log: LogItem) {
    state.log = log
  }
}

export default {
  namespaced,
  state,
  getters,
  actions,
  mutations
}
