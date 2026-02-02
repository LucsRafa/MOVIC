import { defineStore } from 'pinia'
import api from '../api/axios'

export type UserRole = 'teacher' | 'student'

export interface User {
  id: number
  name: string
  email: string
  phone?: string | null
  role: UserRole
}

interface AuthState {
  user: User | null
  token: string | null
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: null,
    token: localStorage.getItem('token')
  }),
  actions: {
    async register(payload: { name: string; email: string; password: string; role: UserRole; phone?: string }) {
      const { data } = await api.post('/auth/register', payload)
      this.user = data.user
      this.token = data.token
      localStorage.setItem('token', data.token)
      localStorage.setItem('role', data.user.role)
    },
    async login(payload: { email: string; password: string }) {
      const { data } = await api.post('/auth/login', payload)
      this.user = data.user
      this.token = data.token
      localStorage.setItem('token', data.token)
      localStorage.setItem('role', data.user.role)
    },
    async fetchMe() {
      const { data } = await api.get('/me')
      this.user = data.user
      if (data.user?.role) {
        localStorage.setItem('role', data.user.role)
      }
    },
    async logout() {
      await api.post('/auth/logout')
      this.user = null
      this.token = null
      localStorage.removeItem('token')
      localStorage.removeItem('role')
    }
  }
})
