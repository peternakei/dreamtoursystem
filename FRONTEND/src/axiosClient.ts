import axios from 'axios'
export const api = axios.create({baseURL: import.meta.env.VITE_BASE_URL || '/backend', headers:{Accept:'application/json'}, withCredentials:true})
let csrf = ''
api.interceptors.request.use(config => { if (csrf) config.headers['X-CSRF-TOKEN'] = csrf; return config })
export function setCsrf(token: string) {csrf = token}
export function errorMessage(error: unknown): string {
 if (axios.isAxiosError(error)) return error.response?.data?.message || 'Unable to connect. Please try again.'
 return error instanceof Error ? error.message : 'Something went wrong.'
}
export default api
