/* eslint-disable camelcase */

export interface Customer {
    id: number,
    nickAllegro: string | null,
    firstName: string | null,
    lastName: string | null,
    firmName: string | null,
    nip: string | null,
    address: string | null,
    flatNumber: number | null,
    city: string | null,
    postCode: string | null,
    email: string | null,
    phone: string | null,
    ordersIds: number[] | null,
}

export interface searchCustomerParams {
    firstName: string | null,
    lastName: string | null,
    email: string | null,
    phone: string | null,
    nickAllegro: string | null,
}

export interface CustomersStore {
    error: string,
    isLoading: boolean,
    customers: Customer[] | null
}

/* eslint-enabled camelcase */
