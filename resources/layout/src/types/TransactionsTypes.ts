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

export interface CreateTransactionParams {
    postedInSystemDate: string,
    postedInBankDate: string,
    paymentId: string,
    kindOfOperation: string,
    orderId: string,
    operator: string,
    operation_value: string,
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
    email: string | null,
    phone: string | null,
    status: string,
    transactions: Transaction[],
    orderIds: number[]
}

export interface TransactionsStore {
    error: string,
    isLoading: boolean,
    customers: Customer[] | null,
    customer: Customer | null
}

/* eslint-enabled camelcase */
