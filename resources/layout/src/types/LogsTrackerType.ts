/* eslint-disable camelcase */

export interface LogItem {
  id: number,
  user_id: number,
  page: string,
  time: number,
  description: string,
  created_at: string
}

export interface LogsTrackerStore {
  error: string,
  isLoading: boolean,
  logs: LogItem[],
  log: LogItem | null
}

export interface addLogParam {
  time: number,
  page: string
}

export interface updateTimeLogParam {
  id: number,
  time: number
}

export interface updateDescriptionLogParam {
  id: number,
  description: string
}
