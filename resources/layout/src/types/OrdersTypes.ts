/* eslint-disable camelcase */

export interface Dates {
  shipment_date_from: string,
  shipment_date_to: string,
  delivery_date_from: string,
  delivery_date_to: string,
}

export interface Acceptance {
  customer: boolean,
  consultant: boolean,
  warehouse: boolean,
  message: string,
}

export interface OrdersStore {
  error: string,
  isLoading: boolean,
  customerDates: Dates | null,
  consultantDates: Dates | null,
  warehouseDates: Dates | null,
  acceptance: Acceptance | null
}

export interface AcceptDatesParams {
  type: string,
  orderId: number,
  userType: string
}

export interface UpdateDatesParams {
  type: string,
  orderId: number,
  shipmentDateFrom: string,
  shipmentDateTo: string,
  deliveryDateFrom: string,
  deliveryDateTo: string
}

/* eslint-enabled camelcase */
