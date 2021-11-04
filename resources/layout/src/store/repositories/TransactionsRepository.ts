/* eslint @typescript-eslint/no-empty-function: ["error", { "allow": ["constructors"] }] */
/* eslint-disable no-useless-constructor */
/* eslint-disable @typescript-eslint/no-explicit-any */

import { getFullUrl } from '@/helpers/urls'
import { CreateTransactionParams, Transaction } from '@/types/TransactionsTypes'

export default {
  async getTransactions (): Promise<any> {
    return fetch(getFullUrl('api/transactions'), {
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
  async storeTransaction (params: CreateTransactionParams): Promise<any> {
    return fetch(getFullUrl('api/transactions'), {
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
  },
  async updateTransaction (params: CreateTransactionParams): Promise<any> {
    return fetch(getFullUrl('api/transactions/' + params.id), {
      method: 'PUT',
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
  },
  async deleteTransaction (transaction: Transaction): Promise<any> {
    return fetch(getFullUrl('api/transactions/' + transaction.id), {
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
