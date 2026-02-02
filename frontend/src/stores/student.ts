import { defineStore } from 'pinia'
import api from '../api/axios'

export const useStudentStore = defineStore('student', {
  state: () => ({
    dashboard: null as any,
    activePlan: null as any,
    session: null as any,
    payments: [] as any[],
    paymentDetail: null as any,
    loading: false
  }),
  actions: {
    async loadActivePlan() {
      const { data } = await api.get('/student/plan/active')
      this.activePlan = data.plan
    },
    async fetchDashboard() {
      this.loading = true
      try {
        const { data } = await api.get('/student/dashboard')
        this.dashboard = data
        this.session = data.today?.session || null
      } finally {
        this.loading = false
      }
    },
    async startSession(workoutDayId: number, sessionDate?: string) {
      const { data } = await api.post('/student/sessions/start', {
        workout_day_id: workoutDayId,
        session_date: sessionDate
      })
      this.session = data.session
      return data.session
    },
    async checkItem(sessionId: number, workoutItemId: number, isChecked: boolean) {
      const { data } = await api.post(`/student/sessions/${sessionId}/check`, {
        workout_item_id: workoutItemId,
        is_checked: isChecked
      })
      return data.check
    },
    async finishSession(sessionId: number) {
      const { data } = await api.post(`/student/sessions/${sessionId}/finish`)
      this.session = data.session
      return data.session
    },
    async loadPayments() {
      const { data } = await api.get('/student/payments')
      this.payments = data.payments
    },
    async fetchPayment(id: number) {
      const { data } = await api.get(`/student/payments/${id}`)
      this.paymentDetail = data.payment
      return data.payment
    },
    async downloadPdf(id: number) {
      const response = await api.get(`/student/payments/${id}/pdf`, { responseType: 'blob' })
      return response.data
    },
    async emailReceipt(id: number, email?: string) {
      const { data } = await api.post(`/student/payments/${id}/email`, { email })
      return data
    },
    async createManualPayment(payload: any) {
      const { data } = await api.post('/student/payments/manual', payload)
      this.payments.push(data.payment)
      return data.payment
    }
  }
})
