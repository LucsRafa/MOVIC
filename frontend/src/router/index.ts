import { createRouter, createWebHistory } from 'vue-router'
import AuthLayout from '../layouts/AuthLayout.vue'
import AppLayout from '../layouts/AppLayout.vue'
import Login from '../pages/Login.vue'
import Register from '../pages/Register.vue'
import ForgotPassword from '../pages/ForgotPassword.vue'
import TeacherDashboard from '../pages/Teacher/Dashboard.vue'
import TeacherStudents from '../pages/Teacher/Students.vue'
import TeacherExercises from '../pages/Teacher/Exercises.vue'
import TeacherPlans from '../pages/Teacher/Plans.vue'
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
      path: '/teacher',
      component: AppLayout,
      meta: { role: 'teacher' },
      children: [
        { path: 'dashboard', component: TeacherDashboard },
        { path: 'students', component: TeacherStudents },
        { path: 'exercises', component: TeacherExercises },
        { path: 'plans', component: TeacherPlans }
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
