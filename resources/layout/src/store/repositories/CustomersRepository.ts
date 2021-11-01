/* eslint @typescript-eslint/no-empty-function: ["error", { "allow": ["constructors"] }] */
/* eslint-disable no-useless-constructor */
/* eslint-disable @typescript-eslint/no-explicit-any */

import { getFullUrl } from '@/helpers/urls'
import { searchCustomerParams } from '@/types/CustomersTypes'

export default {
  async getCustomers (params: searchCustomerParams): Promise<any> {
    return fetch(getFullUrl('api/customers/getCustomers'), {
      method: 'POST',
      credentials: 'same-origin',
      headers: new Headers({
        'Content-Type': 'application/json; charset=utf-8',
        'X-Requested-Width': 'XMLHttpRequest'
      }),
      body: JSON.stringify(params)
    })
      .then((response) => {
        return response.json()
      })
  }
}
