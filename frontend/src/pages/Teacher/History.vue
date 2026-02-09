<template>
  <div class="space-y-4">
    <div class="rounded-2xl bg-white/5 p-4">
      <h3 class="text-lg font-semibold">Historico de Treinos</h3>
      <p class="text-sm text-white/60">Acompanhe o progresso dos seus alunos</p>
    </div>

    <div v-for="item in history" :key="item.student_id" class="rounded-2xl bg-white/5 p-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/10 text-sm font-semibold">
            {{ initials(item.name) }}
          </div>
          <div>
            <p class="font-semibold">{{ item.name }}</p>
            <p class="text-xs text-white/50">Ultima atividade: {{ item.last_activity || '-' }}</p>
          </div>
        </div>
        <span class="text-xs text-emerald-300">?</span>
      </div>
      <div class="mt-3 grid grid-cols-3 gap-2 text-xs">
        <div class="rounded-xl bg-white/5 p-3">
          <p class="text-white/50">Treinos/Semana</p>
          <p class="mt-1 text-sm font-semibold">{{ item.weekly_workouts }}</p>
        </div>
        <div class="rounded-xl bg-white/5 p-3">
          <p class="text-white/50">Frequencia</p>
          <p class="mt-1 text-sm font-semibold">{{ item.monthly_frequency_percent }}%</p>
        </div>
        <div class="rounded-xl bg-white/5 p-3">
          <p class="text-white/50">Total Treinos</p>
          <p class="mt-1 text-sm font-semibold">{{ item.total_completed }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useTeacherStore } from '../../stores/teacher'

const teacher = useTeacherStore()
const history = computed(() => teacher.history)

const initials = (name: string) => {
  const parts = name.split(' ')
  return parts.length ? (parts[0][0] + (parts[1]?.[0] || '')).toUpperCase() : 'U'
}

onMounted(async () => {
  await teacher.fetchHistory()
})
</script>
