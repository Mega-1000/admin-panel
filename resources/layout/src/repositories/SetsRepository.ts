/* eslint @typescript-eslint/no-empty-function: ["error", { "allow": ["constructors"] }] */
/* eslint-disable no-useless-constructor */
import { Set } from '@/types/SetsTypes'
import { getFullUrl } from '@/helpers/urls'

export default {
  async getSets (): Promise<Set[]> {
    return fetch(getFullUrl('api/sets'), {
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
  }
}
