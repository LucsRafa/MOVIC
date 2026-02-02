import { defineStore } from 'pinia'
import api from '../api/axios'

export const useTeacherStore = defineStore('teacher', {
  state: () => ({
    students: [] as any[],
    exercises: [] as any[],
    plans: [] as any[]
  }),
  actions: {
    async loadStudents() {
      const { data } = await api.get('/teacher/students')
      this.students = data.students
    },
    async inviteStudent(email: string) {
      const { data } = await api.post('/teacher/students/invite', { email })
      return data.invite
    },
    async approveStudent(id: number) {
      const { data } = await api.post(`/teacher/students/${id}/approve`)
      return data.student
    },
    async loadExercises() {
      const { data } = await api.get('/teacher/exercises')
      this.exercises = data.exercises
    },
    async createExercise(payload: any) {
      const { data } = await api.post('/teacher/exercises', payload)
      this.exercises.push(data.exercise)
    },
    async updateExercise(id: number, payload: any) {
      const { data } = await api.put(`/teacher/exercises/${id}`, payload)
      this.exercises = this.exercises.map((e) => (e.id === id ? data.exercise : e))
    },
    async deleteExercise(id: number) {
      await api.delete(`/teacher/exercises/${id}`)
      this.exercises = this.exercises.filter((e) => e.id !== id)
    },
    async createPlan(studentId: number, payload: any) {
      const { data } = await api.post(`/teacher/students/${studentId}/plans`, payload)
      return data.plan
    },
    async activatePlan(planId: number) {
      const { data } = await api.patch(`/teacher/plans/${planId}/activate`)
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
