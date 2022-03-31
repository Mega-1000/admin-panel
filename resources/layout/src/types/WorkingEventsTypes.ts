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

export interface WorkInfo {
  workingFrom: string,
  workingTo: string,
  uptimeInMinutes: number,
  idleTimeInMinutes: number
}

export interface Inactivity {
  id: number,
  title: string,
  date: string,
  content: string,
  description: string,
  page: string,
  time: number,
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
  inactivity: Inactivity[] | null,
  workingInfo: WorkInfo | null
}

/* eslint-enabled camelcase */
