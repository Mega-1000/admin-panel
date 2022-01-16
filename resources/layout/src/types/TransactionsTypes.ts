/* eslint-disable camelcase */

/**
 * Obiekt transakcji
 */
export interface Transaction {
  id: number,
  registrationInSystemDate: string,
  registrationInBankDate: string,
  paymentId: string,
  operationKind: string,
  orderId: number,
  operator: string,
  operationValue: number,
  balance: number,
  accountingNotes: string,
  transactionNotes: string
}

/**
 * Obiekt do utworzenia transakcji
 */
export interface CreateTransactionParams {
  id: number | null
  registrationInSystemDate: string,
  registrationInBankDate: string,
  paymentId: string,
  operationKind: string,
  customerId: number,
  orderId: string,
  operator: string,
  operationValue: string,
  accountingNotes: string,
  transactionNotes: string
}

/**
 * Obiekt klienta posiadającego transakcje
 */
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
  email: string | null,
  phone: string | null,
  status: string,
  transactions: Transaction[],
  orderIds: number[]
}

/**
 * Store transakcji
 */
export interface TransactionsStore {
  error: string,
  isLoading: boolean,
  importIsLoading: boolean,
  customers: Customer[] | null,
  customer: Customer | null,
  transaction: Transaction | null,
  transactions: Transaction[] | null,
  pageCount: number | null,
  currentPage: number | null
}

export interface ImportFileParams {
  kind: string,
  file: File
}

export interface searchCustomersParams {
  page: string,
  nickAllegro: string | null,
  phone: string | null,
  email: string | null,
  nip: string | null
}

/* eslint-enabled camelcase */
