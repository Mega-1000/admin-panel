/* eslint @typescript-eslint/no-empty-function: ["error", { "allow": ["constructors"] }] */
/* eslint-disable no-useless-constructor */
/* eslint-disable @typescript-eslint/no-explicit-any */

import { getFullUrl } from '@/helpers/urls'
import { AcceptAsCustomerParams, AcceptDatesParams, UpdateDatesParams } from '@/types/OrdersTypes'

export default {
  async getDates (orderId: number): Promise<any> {
    return fetch(getFullUrl('api/orders/' + orderId + '/getDates/'), {
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
  async acceptDates (params: AcceptDatesParams): Promise<any> {
    return fetch(getFullUrl('api/orders/' + params.orderId + '/acceptDates'), {
      method: 'PUT',
      credentials: 'same-origin',
      headers: new Headers({
        'Content-Type': 'application/json; charset=utf-8',
        'X-Requested-Width': 'XMLHttpRequest'
      }),
      body: JSON.stringify({
        type: params.type,
        userType: params.userType
      })
    }).then((response) => {
      return response.json()
    })
  },

  async acceptDatesAsCustomer (params: AcceptAsCustomerParams): Promise<any> {
    return fetch(getFullUrl('api/orders/' + params.orderId + '/acceptDatesAsCustomer'), {
      method: 'PUT',
      credentials: 'same-origin',
      headers: new Headers({
        'Content-Type': 'application/json; charset=utf-8',
        'X-Requested-Width': 'XMLHttpRequest'
      }),
      body: JSON.stringify({
        orderId: params.orderId,
        chatId: params.chatId
      })
    }).then((response) => {
      return response.json()
    })
  },

  async updateDates (params: UpdateDatesParams): Promise<any> {
    return fetch(getFullUrl('api/orders/' + params.orderId + '/updateDates'), {
      method: 'PUT',
      credentials: 'same-origin',
      headers: new Headers({
        'Content-Type': 'application/json; charset=utf-8',
        'X-Requested-Width': 'XMLHttpRequest'
      }),
      body: JSON.stringify({
        type: params.type,
        shipmentDateFrom: params.shipmentDateFrom,
        shipmentDateTo: params.shipmentDateTo,
        deliveryDateFrom: params.deliveryDateFrom,
        deliveryDateTo: params.deliveryDateTo
      })
    }).then((response) => {
      return response.json()
    })
  }
}
