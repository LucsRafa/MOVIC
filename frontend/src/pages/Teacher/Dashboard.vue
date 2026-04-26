<template>
  <div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2">
      <div class="rounded-2xl bg-emerald-500/10 p-4">
        <p class="text-sm text-emerald-200">Total de Alunos</p>
        <p class="mt-2 text-2xl font-semibold">{{ cards.total_students ?? 0 }}</p>
      </div>
      <div class="rounded-2xl bg-sky-500/10 p-4">
        <p class="text-sm text-sky-200">Treinos Ativos</p>
        <p class="mt-2 text-2xl font-semibold">{{ cards.active_workouts ?? 0 }}</p>
      </div>
      <div class="rounded-2xl bg-emerald-500/10 p-4">
        <p class="text-sm text-emerald-200">Pagamentos em Dia</p>
        <p class="mt-2 text-2xl font-semibold">{{ cards.payments_ok ?? 0 }}</p>
      </div>
      <div class="rounded-2xl bg-fuchsia-500/10 p-4">
        <p class="text-sm text-fuchsia-200">Exercícios cadastrados</p>
        <p class="mt-2 text-2xl font-semibold">{{ cards.exercises_total ?? 0 }}</p>
      </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-[1.2fr_0.8fr]">
      <div class="rounded-2xl bg-white/5 p-4">
        <h3 class="text-lg font-semibold">Resumo</h3>
        <p class="mt-1 text-sm text-white/60">Acompanhe rapidamente os indicadores do seu painel.</p>
      </div>

      <RouterLink
        class="rounded-2xl border border-white/10 bg-white/5 p-4 transition hover:border-emerald-400/30 hover:bg-white/10"
        to="/teacher/requests"
      >
        <div class="flex items-start justify-between gap-3">
          <div>
            <p class="text-sm font-semibold text-white">Solicitações pendentes</p>
            <p class="mt-1 text-sm text-white/60">Aprove novos alunos e acompanhe os pedidos.</p>
          </div>
          <span class="rounded-full bg-red-500/15 px-3 py-1 text-sm font-semibold text-red-200">
            {{ requests }}
          </span>
        </div>
        <p class="mt-4 text-sm text-emerald-300">
          {{ requests > 0 ? 'Ver solicitações agora' : 'Nenhuma solicitação pendente' }}
        </p>
      </RouterLink>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useTeacherStore } from '../../stores/teacher'

const teacher = useTeacherStore()

const cards = computed(() => teacher.dashboard?.cards || {})
const requests = computed(() => teacher.dashboard?.badges?.requests ?? 0)

onMounted(async () => {
  await teacher.fetchDashboard()
})
</script>
