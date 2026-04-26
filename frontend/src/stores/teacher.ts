import { defineStore } from 'pinia'
import api from '../api/http'

export const useTeacherStore = defineStore('teacher', {
  state: () => ({
    loading: false,
    dashboard: null as any,
    requests: [] as any[],
    students: [] as any[],
    studentOverview: null as any,
    workouts: { plan: null as any, days: [] as any[], student: null as any },
    exercises: [] as any[],
    payments: [] as any[],
    history: [] as any[]
  }),
  actions: {
    async fetchDashboard() {
      this.loading = true
      try {
        const { data } = await api.get('/teacher/dashboard')
        this.dashboard = data.data
      } finally {
        this.loading = false
      }
    },
    async fetchRequests() {
      const { data } = await api.get('/teacher/requests')
      this.requests = data.requests
    },
    async approveRequest(studentId: number) {
      await api.post(`/teacher/requests/${studentId}/approve`)
      this.requests = this.requests.filter((r) => r.student_id !== studentId)
      if (this.dashboard?.badges?.requests !== undefined) {
        this.dashboard.badges.requests = Math.max(0, this.dashboard.badges.requests - 1)
      }
    },
    async rejectRequest(studentId: number) {
      await api.post(`/teacher/requests/${studentId}/reject`)
      this.requests = this.requests.filter((r) => r.student_id !== studentId)
      if (this.dashboard?.badges?.requests !== undefined) {
        this.dashboard.badges.requests = Math.max(0, this.dashboard.badges.requests - 1)
      }
    },
    async fetchStudents(search?: string) {
      const { data } = await api.get('/teacher/students', { params: { search } })
      this.students = data.students
    },
    async loadStudents(search?: string) {
      await this.fetchStudents(search)
    },
    async fetchStudentOverview(studentId: number) {
      const { data } = await api.get(`/teacher/students/${studentId}/overview`)
      this.studentOverview = data.overview
      return data.overview
    },
    async updateStudentStatus(studentId: number, status: string) {
      await api.patch(`/teacher/students/${studentId}/status`, { status })
    },
    async resetStudentPassword(studentId: number) {
      await api.post(`/teacher/students/${studentId}/reset-password`)
    },
    async removeStudent(studentId: number) {
      await api.delete(`/teacher/students/${studentId}`)
      this.students = this.students.filter((s) => s.id !== studentId)
    },
    async fetchWorkouts(studentId: number) {
      const { data } = await api.get('/teacher/workouts', { params: { student_id: studentId } })
      this.workouts = {
        plan: data.plan,
        days: data.days || [],
        student: data.student
      }
    },
    async createWorkoutDay(payload: any) {
      const { data } = await api.post('/teacher/workouts/days', payload)
      return data.day
    },
    async addWorkoutItem(dayId: number, payload: any) {
      const { data } = await api.post(`/teacher/workouts/days/${dayId}/items`, payload)
      return data.item
    },
    async updateWorkoutItem(itemId: number, payload: any) {
      const { data } = await api.put(`/teacher/workouts/items/${itemId}`, payload)
      return data.item
    },
    async deleteWorkoutItem(itemId: number) {
      await api.delete(`/teacher/workouts/items/${itemId}`)
    },
    async loadExercises() {
      const { data } = await api.get('/teacher/exercises')
      this.exercises = data.exercises
    },
    async createExercise(payload: any) {
      const { data } = await api.post('/teacher/exercises', payload)
      this.exercises.push(data.exercise)
      return data.exercise
    },
    async updateExercise(id: number, payload: any) {
      const { data } = await api.put(`/teacher/exercises/${id}`, payload)
      this.exercises = this.exercises.map((e) => (e.id === id ? data.exercise : e))
      return data.exercise
    },
    async deleteExercise(id: number) {
      await api.delete(`/teacher/exercises/${id}`)
      this.exercises = this.exercises.filter((e) => e.id !== id)
    },
    async fetchPayments() {
      const { data } = await api.get('/teacher/payments')
      this.payments = data.payments
    },
    async registerPayment(payload: any) {
      const { data } = await api.post('/teacher/payments/register', payload)
      return data.payment
    },
    async downloadReceipt(paymentId: number) {
      const { data } = await api.get(`/teacher/payments/${paymentId}/receipt.pdf`, { responseType: 'blob' })
      return data
    },
    async emailReceipt(paymentId: number, email?: string) {
      await api.post(`/teacher/payments/${paymentId}/send-receipt`, { email })
    },
    async fetchHistory() {
      const { data } = await api.get('/teacher/history')
      this.history = data.history
    },
    async createPlan(studentId: number, payload: any) {
      const { data } = await api.post(`/teacher/students/${studentId}/plans`, payload)
      return data.plan
    },
    async addDay(planId: number, payload: any) {
      const { data } = await api.post(`/teacher/plans/${planId}/days`, payload)
      return data.day
    },
    async addItem(dayId: number, payload: any) {
      const { data } = await api.post(`/teacher/days/${dayId}/items`, payload)
      return data.item
    }
  }
})
