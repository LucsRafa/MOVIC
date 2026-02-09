<template>
  <nav class="mt-4 flex items-center gap-2 overflow-x-auto rounded-2xl bg-white/5 px-3 py-2 text-sm">
    <RouterLink :class="linkClass('/teacher/requests')" to="/teacher/requests">
      Pedidos
      <span v-if="requests" class="ml-2 rounded-full bg-red-500 px-2 text-xs">{{ requests }}</span>
    </RouterLink>
    <RouterLink :class="linkClass('/teacher/students')" to="/teacher/students">Alunos</RouterLink>
    <RouterLink :class="linkClass('/teacher/workouts')" to="/teacher/workouts">Treinos</RouterLink>
    <RouterLink :class="linkClass('/teacher/exercises')" to="/teacher/exercises">Exercicios</RouterLink>
    <RouterLink :class="linkClass('/teacher/payments')" to="/teacher/payments">Pagamentos</RouterLink>
    <RouterLink :class="linkClass('/teacher/history')" to="/teacher/history">Historico</RouterLink>
  </nav>
</template>

<script setup lang="ts">
import { useRoute } from 'vue-router'

const props = defineProps<{ requests: number }>()
const route = useRoute()

const linkClass = (path: string) => {
  const active = route.path.startsWith(path)
  return [
    'flex items-center gap-2 whitespace-nowrap rounded-full px-3 py-1.5 transition',
    active ? 'bg-emerald-500 text-white' : 'text-white/70 hover:bg-white/10'
  ]
}
</script>
