export function getFullUrl (path: string): string {
  return window.location.protocol + '//' + window.location.hostname + ':8000/' + path
}
