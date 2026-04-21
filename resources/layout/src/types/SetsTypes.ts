/* eslint-disable camelcase */

export interface SetItem {
  id: number,
  name: string,
  number: string,
  stock: number,
  product_id: number,
  created_at: string,
  updated_at: string
}

export interface SetProduct {
  id: number,
  manufacturer: string,
  name: string,
  producent_override: string,
  product_id: number,
  stock: number,
  symbol: string,
  url_for_website: string
}

export interface Set {
  set: SetItem,
  products: SetProduct[]
}

export interface Stock {
  bookstand: string
  created_at: string
  id: number
  lane: string
  position: string
  position_quantity: number
  product_stock_id: number
  shelf: string
  updated_at: string
}

export interface ProductStocks {
  id: number,
  stocks: Stock[]
}

export interface SetsStore {
  error: string,
  isLoading: boolean,
  sets: Set[],
  products: SetProduct[],
  set: Set | null,
  productsStocks: ProductStocks[]
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

export interface SetParams {
  id: number,
  name: string,
  number: string,
}

export interface SetProductParams {
  id: number,
  setId: number,
  stock: number
}

export interface CreateSetParams {
  name: string,
  symbol: string,
  price: number
}

export interface Error {
  name: string,
  symbol: string,
  price: number
}
/* eslint-enabled camelcase */
