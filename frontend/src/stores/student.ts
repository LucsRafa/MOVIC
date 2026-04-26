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
    syncWorkoutItemState(workoutDayId: number, workoutItemId: number, completedAt: string | null) {
      const isChecked = Boolean(completedAt)

      const todayWorkout = this.dashboard?.today?.workout_day
      if (todayWorkout?.id === workoutDayId && Array.isArray(todayWorkout.items)) {
        const dashboardItem = todayWorkout.items.find((item: any) => item.workout_item_id === workoutItemId)
        if (dashboardItem) {
          dashboardItem.completed_at = completedAt
          dashboardItem.is_checked = isChecked
        }

        const total = todayWorkout.items.length
        const done = todayWorkout.items.filter((item: any) => item.is_checked).length
        todayWorkout.progress_percent = total ? Math.round((done / total) * 100) : 0
      }

      const planDay = this.activePlan?.days?.find((day: any) => day.id === workoutDayId)
      const planItem = planDay?.items?.find((item: any) => item.id === workoutItemId || item.workout_item_id === workoutItemId)
      if (planItem) {
        planItem.completed_at = completedAt
        planItem.is_checked = isChecked
      }
    },
    async loadActivePlan() {
      const { data } = await api.get('/student/plan/active')
      this.activePlan = data.plan
    },
    async fetchDashboard() {
      this.loading = true
      try {
        const { data } = await api.get('/student/dashboard')
        this.dashboard = data
        this.session = data.today?.session?.status === 'completed' ? null : data.today?.session || null
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

      this.session = data.session?.status === 'completed' ? null : data.session || this.session
      return data.check
    },
    async toggleWorkoutItem(workoutDayId: number, workoutItemId: number, sessionDate?: string) {
      const payload = sessionDate ? { session_date: sessionDate } : {}
      const { data } = await api.post(`/workout-items/${workoutItemId}/toggle`, payload, {
        skipErrorToast: true
      } as any)

      if (this.dashboard?.today?.workout_day?.id === workoutDayId) {
        this.dashboard.today.session = data.session || this.dashboard.today.session || null
      }

      this.session = data.session?.status === 'completed' ? null : data.session || this.session

      const completedAt = data.check?.completed_at ?? data.check?.checked_at ?? null
      this.syncWorkoutItemState(workoutDayId, workoutItemId, completedAt)

      return data.check
    },
    async finishSession(sessionId: number, options: { skipErrorToast?: boolean } = {}) {
      const config = options.skipErrorToast ? ({ skipErrorToast: true } as any) : undefined
      const { data } = await api.post(`/student/sessions/${sessionId}/finish`, {}, config)
      this.session = null
      return data
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
