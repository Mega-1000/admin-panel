/* eslint @typescript-eslint/no-empty-function: ["error", { "allow": ["constructors"] }] */
/* eslint-disable no-useless-constructor */
/* eslint-disable @typescript-eslint/no-explicit-any */

import { getFullUrl } from '@/helpers/urls'
import { Inactivity, searchWorkingEventsParams } from '@/types/WorkingEventsTypes'

export default {
  async getWorkingEvents (params: searchWorkingEventsParams): Promise<any> {
    const urlSearchParams = new URLSearchParams(
      Object.fromEntries(Object.entries(params).filter((v) => v[1] !== ''))
    )
    return fetch(getFullUrl('api/working-events/events?' + urlSearchParams.toString()), {
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
  async getInactivity (params: searchWorkingEventsParams): Promise<any> {
    const urlSearchParams = new URLSearchParams(
      Object.fromEntries(Object.entries(params).filter((v) => v[1] !== ''))
    )
    return fetch(getFullUrl('api/working-events/inactivity?' + urlSearchParams.toString()), {
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
  async getWorkers (): Promise<any> {
    return fetch(getFullUrl('api/working-events/workers'), {
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
  /**
   * Mark inactivity as work
   *
   * @param inactivity:Inactivity
   */
  async markInactivity (inactivity: Inactivity): Promise<any> {
    return fetch(getFullUrl('api/working-events/' + inactivity.id), {
      method: 'DELETE',
      credentials: 'same-origin',
      headers: new Headers({
        'Content-Type': 'application/json; charset=utf-8',
        'X-Requested-Width': 'XMLHttpRequest'
      })
    })
      .then((response) => {
        return response.json()
      })
  }
}
