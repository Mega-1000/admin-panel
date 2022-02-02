/* eslint @typescript-eslint/no-empty-function: ["error", { "allow": ["constructors"] }] */
/* eslint-disable no-useless-constructor */
/* eslint-disable @typescript-eslint/no-explicit-any */

import { getFullUrl } from '@/helpers/urls'
import {
  CreateTransactionParams,
  Customer,
  ImportFileParams,
  searchCustomersParams, searchProvidersTransactionsParams,
  Transaction
} from '@/types/TransactionsTypes'

export default {
  async getTransactions (params: searchCustomersParams): Promise<any> {
    const urlSearchParams = new URLSearchParams(
      Object.fromEntries(Object.entries(params).filter((v) => v[1] !== ''))
    )
    return fetch(getFullUrl('api/transactions?' + urlSearchParams.toString()), {
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
  async getCustomerTransactions (customer?: Customer): Promise<any> {
    return fetch(getFullUrl('api/transactions/customer/' + customer?.id), {
      method: 'GET',
      credentials: 'same-origin',
      headers: new Headers({
        'Content-Type': 'application/json; charset=utf-8',
        'X-Requested-Width': 'XMLHttpRequest'
      })
    })
      .then((response) => {
        console.log(response)
        return response.json()
      })
  },
  /**
   * Zapis transakcji
   *
   * @param params:CreateTransactionParams
   */
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
  /**
   * Aktualizacja transakcji
   *
   * @param params:CreateTransactionParams
   */
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
  /**
   * Usunięcie transakcji
   *
   * @param transaction:Transaction
   */
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
  },
  /**
   * Import transakcji
   *
   * @param params:ImportFileParams
   */
  async importTransaction (params: ImportFileParams): Promise<any> {
    const formData = new FormData()
    formData.append('file', params.file)
    return fetch(getFullUrl('api/transactions/import/' + params.kind), {
      method: 'POST',
      credentials: 'same-origin',
      body: formData
    })
      .then((response) => {
        return response.json()
      })
  },
  async getProvidersTransactions (params: searchProvidersTransactionsParams): Promise<any> {
    const urlSearchParams = new URLSearchParams(
      Object.fromEntries(Object.entries(params).filter((v) => v[1] !== ''))
    )
    return fetch(getFullUrl('api/transactions/providers?' + urlSearchParams.toString()), {
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
