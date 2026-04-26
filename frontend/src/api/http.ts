import axios from 'axios'
import { useToastStore } from '../stores/toast'
import { pinia } from '../stores/pinia'
import { extractApiErrorMessage } from '../utils/apiError'

const configuredBase = (import.meta.env.VITE_API_URL || '/api').trim()
const normalizedBase = configuredBase.replace(/\/+$/, '')
const baseURL = normalizedBase.endsWith('/api') ? normalizedBase : `${normalizedBase}/api`

const api = axios.create({
  baseURL,
  headers: {
    Accept: 'application/json'
  },
  timeout: 15000
})

const toast = useToastStore(pinia)

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  config.headers['X-Requested-With'] = 'XMLHttpRequest'
  if (!(config.data instanceof FormData)) {
    config.headers['Content-Type'] = 'application/json'
  }
  return config
})

api.interceptors.response.use(
  (response) => response,
  (error) => {
    const skipToast = Boolean((error?.config as any)?.skipErrorToast)
    const message = extractApiErrorMessage(error)

    if (!skipToast) {
      toast.push(message, 'error')
    }

    if (error.response?.status === 401) {
      localStorage.removeItem('token')
      localStorage.removeItem('role')
      if (window.location.pathname !== '/login') {
        window.location.href = '/login'
      }
    }

    return Promise.reject(error)
  }
)

export default api
