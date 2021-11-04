/* eslint-disable camelcase */

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

export interface CreateTransactionParams {
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
    customer: Customer | null,
    transaction: Transaction | null,
}

/* eslint-enabled camelcase */
