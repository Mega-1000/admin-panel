/* eslint-disable camelcase */

export interface User {
  id: number,
  firstname: string,
  lastname: string,
}

export interface Event {
  title: string,
  date: string,
  orderId: number,
  userId: number,
  content: string,
}

export interface Inactivity {
  title: string,
  date: string,
  content: string,
  userId: number,
  orderId: number | null,
}

export interface searchWorkingEventsParams {
  userId: number,
  date: string,
}

/**
 * Store Working events
 */
export interface WorkingEventsStore {
  error: string,
  isLoading: boolean,
  users: User[] | null,
  events: Event[] | null,
  inactivity: Inactivity[] | null
}

/* eslint-enabled camelcase */
