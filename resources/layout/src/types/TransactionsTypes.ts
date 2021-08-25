/* eslint-disable camelcase */

export interface Transaction {
  id: number,
  postedInSystemDate: string,
  postedInBankDate: string,
  paymentId: string,
  kindOfOperation: string,
  orderId: number,
  operator: string,
  operation_value: number,
  balance: number,
  accounting_notes: string,
  transaction_notes: string
}

export interface Customer {
  id: number,
  type: string,
  login: string,
  nickAllegro: string | null,
  firstName: string | null,
  lastName: string | null,
  firmName: string | null,
  nip: string | null,
  address: string | null,
  flatNumber: number | null,
  city: string,
  postCode: string,
  email: string,
  status: string,
  transactions: Transaction[]
}

export interface TransactionsStore {
  error: string,
  isLoading: boolean,
  customers: Customer[] | null,
  customer: Customer | null
}

/* eslint-enabled camelcase */
