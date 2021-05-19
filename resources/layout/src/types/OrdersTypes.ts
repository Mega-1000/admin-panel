/* eslint-disable camelcase */

import {ProductStocks, Set, SetProduct} from "@/types/SetsTypes";

export interface Dates {
  shipment_date_from: string,
  shipment_date_to: string,
  delivery_date_from: string,
  delivery_date_to: string,
}

export interface Acceptance {
  customer_acceptance: boolean,
  consultant_acceptance: boolean,
  warehouse_acceptance: boolean,
}

export interface OrdersStore {
  error: string,
  isLoading: boolean,
  customerDates: Dates,
  consultantDates: Dates,
  warehouseDates: Dates,
  acceptance: Acceptance
}

/* eslint-enabled camelcase */
