export function getFullUrl (path: string): string {
  let port = ''
  if (window.location.hostname.search('localhost')) {
    port = ':8000'
  }
  return window.location.protocol + '//' + window.location.hostname + port + '/' + path
}
