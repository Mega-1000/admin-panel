/* eslint @typescript-eslint/no-empty-function: ["error", { "allow": ["constructors"] }] */
/* eslint-disable no-useless-constructor */
/* eslint-disable @typescript-eslint/no-explicit-any */

import { getFullUrl } from '@/helpers/urls'
import { addLogParam, LogItem, updateDescriptionLogParam, updateTimeLogParam } from '@/types/LogsTrackerType'

export default {
  async getLogs (): Promise<LogItem[]> {
    return fetch(getFullUrl('api/tracker/logs'), {
      method: 'GET',
      credentials: 'same-origin',
      headers: new Headers({
        'Content-Type': 'application/json; charset=utf-8',
        'X-Requested-Width': 'XMLHttpRequest'
      })
    })
      .then((response) => {
        return response.json()
      })
  },
  async newLog (params: addLogParam): Promise<LogItem> {
    return fetch(getFullUrl('api/tracker/logs'), {
      method: 'POST',
      credentials: 'same-origin',
      headers: new Headers({
        'Content-Type': 'application/json; charset=utf-8',
        'X-Requested-Width': 'XMLHttpRequest'
      }),
      body: JSON.stringify({
        user_id: params.userId,
        time: params.time,
        page: params.page
      })
    })
      .then((response) => {
        return response.json()
      })
  },
  async updateTimeLog (params: updateTimeLogParam): Promise<LogItem> {
    return fetch(getFullUrl('api/tracker/logs/' + params.id), {
      method: 'PUT',
      credentials: 'same-origin',
      headers: new Headers({
        'Content-Type': 'application/json; charset=utf-8',
        'X-Requested-Width': 'XMLHttpRequest'
      }),
      body: JSON.stringify({
        time: params.time
      })
    })
      .then((response) => {
        return response.json()
      })
  },
  async updateDescriptionLog (params: updateDescriptionLogParam): Promise<LogItem> {
    return fetch(getFullUrl('api/tracker/logs/' + params.id), {
      method: 'PUT',
      credentials: 'same-origin',
      headers: new Headers({
        'Content-Type': 'application/json; charset=utf-8',
        'X-Requested-Width': 'XMLHttpRequest'
      }),
      body: JSON.stringify({
        description: params.description,
        time: params.time
      })
    })
      .then((response) => {
        return response.json()
      })
  }
}
