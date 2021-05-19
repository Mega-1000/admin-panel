/* eslint @typescript-eslint/no-empty-function: ["error", { "allow": ["constructors"] }] */
/* eslint-disable no-useless-constructor */
/* eslint-disable @typescript-eslint/no-explicit-any */

import {CreateSetParams, Set, SetParams, SetProductParams, SetsCount, SetsProductParams} from '@/types/SetsTypes'
import {getFullUrl} from '@/helpers/urls'

export default {
  async setItem(id: number): Promise<any> {
    return fetch(getFullUrl('api/sets/' + id), {
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
  async setItemUpdate(item: SetParams): Promise<any> {
    return fetch(getFullUrl('api/sets/' + item.id), {
      method: 'PUT',
      credentials: 'same-origin',
      headers: new Headers({
        'Content-Type': 'application/json; charset=utf-8',
        'X-Requested-Width': 'XMLHttpRequest'
      }),
      body: JSON.stringify({
        name: item.name,
        number: item.number
      })
    })
      .then((response) => {
        return response.json()
      })
  },
  async setProductAdd(item: SetProductParams): Promise<any> {
    return fetch(getFullUrl('api/sets/' + item.setId + '/products/'), {
      method: 'POST',
      credentials: 'same-origin',
      headers: new Headers({
        'Content-Type': 'application/json; charset=utf-8',
        'X-Requested-Width': 'XMLHttpRequest'
      }),
      body: JSON.stringify({
        product_id: item.id,
        stock: item.stock
      })
    })
      .then((response) => {
        return response.json()
      })
  },
}
