import api from '@/axiosClient'

// Laravel asset() sees the Vue host when requests pass through the dev proxy.
// Fetch application attachments through the same backend base URL as the API.
export function mediaUrl(value: string): string {
  if (!value) return ''
  try {
    const url = new URL(value, window.location.origin)
    if (url.pathname.startsWith('/storage/')) {
      return (api.defaults.baseURL || '/backend').replace(/\/$/, '') + url.pathname + url.search
    }
  } catch { /* Preserve non-URL values for the normal browser error handling. */ }
  return value
}
