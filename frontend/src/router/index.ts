import { createRouter, createWebHistory } from 'vue-router'
import AuthLayout from '../layouts/AuthLayout.vue'
import AppLayout from '../layouts/AppLayout.vue'
import TeacherLayout from '../layouts/TeacherLayout.vue'
import Login from '../pages/Login.vue'
import Register from '../pages/Register.vue'
import ForgotPassword from '../pages/ForgotPassword.vue'
import ResetPassword from '../pages/ResetPassword.vue'
import TeacherDashboard from '../pages/Teacher/Dashboard.vue'
import TeacherRequests from '../pages/Teacher/Requests.vue'
import TeacherStudents from '../pages/Teacher/Students.vue'
import TeacherWorkouts from '../pages/Teacher/Workouts.vue'
import TeacherExercises from '../pages/Teacher/Exercises.vue'
import TeacherPayments from '../pages/Teacher/Payments.vue'
import TeacherHistory from '../pages/Teacher/History.vue'
import StudentDashboard from '../pages/Student/Dashboard.vue'
import StudentWorkoutDay from '../pages/Student/WorkoutDay.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/login',
      component: AuthLayout,
      children: [{ path: '', component: Login }]
    },
    {
      path: '/register',
      component: AuthLayout,
      children: [{ path: '', component: Register }]
    },
    {
      path: '/forgot-password',
      component: AuthLayout,
      children: [{ path: '', component: ForgotPassword }]
    },
    {
      path: '/reset-password',
      component: AuthLayout,
      children: [{ path: '', component: ResetPassword }]
    },
    {
      path: '/teacher',
      component: TeacherLayout,
      meta: { role: 'teacher' },
      children: [
        { path: 'dashboard', component: TeacherDashboard },
        { path: 'requests', component: TeacherRequests },
        { path: 'students', component: TeacherStudents },
        { path: 'workouts', component: TeacherWorkouts },
        { path: 'exercises', component: TeacherExercises },
        { path: 'payments', component: TeacherPayments },
        { path: 'history', component: TeacherHistory }
      ]
    },
    {
      path: '/student',
      component: AppLayout,
      meta: { role: 'student' },
      children: [
        { path: 'dashboard', component: StudentDashboard },
        { path: 'workout/:weekday', component: StudentWorkoutDay }
      ]
    },
    { path: '/', redirect: '/login' }
  ]
})

router.beforeEach((to) => {
  const token = localStorage.getItem('token')
  const role = localStorage.getItem('role')

  if (to.path.startsWith('/teacher') || to.path.startsWith('/student')) {
    if (!token) {
      return '/login'
    }
    if (to.meta.role && role !== to.meta.role) {
      return role === 'teacher' ? '/teacher/dashboard' : '/student/dashboard'
    }
  }

  if ((to.path === '/login' || to.path === '/register') && token) {
    return role === 'teacher' ? '/teacher/dashboard' : '/student/dashboard'
  }

  return true
})

export default router
