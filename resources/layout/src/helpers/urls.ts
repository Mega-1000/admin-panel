export function getFullUrl (path: string): string {
  return window.location.protocol + '//' + window.location.hostname + '/' + path
}
