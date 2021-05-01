/* eslint-disable camelcase */

export interface SetItem {
  id: number,
  name: string,
  number: string,
  stock: number,
  created_at: string,
  updated_at: string,
}

export interface SetProduct {
  id: number,
  manufacturer: string,
  name: string,
  producent_override: string,
  product_id: number,
  stock: number,
  symbol: string
}

export interface Set {
  set: SetItem,
  products: SetProduct[]
}

export interface SetsStore {
  error: string,
  isLoading: boolean,
  sets: Set[],
  products: SetProduct[]
}

export interface SetsCount {
  setId: number,
  count: number
}

export interface SetsProductParams {
  name: string,
  symbol: string,
  manufacturer: string,
  word: string
}
/* eslint-enabled camelcase */
