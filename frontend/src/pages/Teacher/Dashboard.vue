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
        <p class="text-sm text-fuchsia-200">Exercicios Cadastrados</p>
        <p class="mt-2 text-2xl font-semibold">{{ cards.exercises_total ?? 0 }}</p>
      </div>
    </div>

    <div class="rounded-2xl bg-white/5 p-4">
      <h3 class="text-lg font-semibold">Resumo</h3>
      <p class="mt-1 text-sm text-white/60">Acompanhe rapidamente os indicadores do seu painel.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useTeacherStore } from '../../stores/teacher'

const teacher = useTeacherStore()

const cards = computed(() => teacher.dashboard?.cards || {})

onMounted(async () => {
  await teacher.fetchDashboard()
})
</script>
