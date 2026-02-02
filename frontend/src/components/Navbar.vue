<template>
  <header class="sticky top-0 z-20 border-b border-black/10 bg-mist/80 backdrop-blur">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
      <div class="flex items-center gap-3">
        <div class="h-9 w-9 rounded-xl bg-ember"></div>
        <div>
          <p class="text-sm uppercase tracking-[0.3em] text-black/60">Movic</p>
          <p class="text-lg font-semibold">Painel</p>
        </div>
      </div>
      <nav class="flex items-center gap-4 text-sm">
        <router-link v-if="role === 'teacher'" to="/teacher/dashboard">Dashboard</router-link>
        <router-link v-if="role === 'teacher'" to="/teacher/students">Alunos</router-link>
        <router-link v-if="role === 'teacher'" to="/teacher/exercises">Exercicios</router-link>
        <router-link v-if="role === 'teacher'" to="/teacher/plans">Planos</router-link>
        <router-link v-if="role === 'student'" to="/student/dashboard">Dashboard</router-link>
        <router-link v-if="role === 'student'" :to="`/student/workout/${today}`">Treino do dia</router-link>
        <button class="rounded-full border border-black/10 px-4 py-2" @click="logout">Sair</button>
      </nav>
    </div>
  </header>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const role = computed(() => auth.user?.role || localStorage.getItem('role'))
const today = new Date().getDay()

const logout = async () => {
  await auth.logout()
  window.location.href = '/login'
}
</script>

<style scoped>
a {
  @apply text-black/70 hover:text-black transition;
}
</style>
