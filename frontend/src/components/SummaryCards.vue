<template>
  <div class="grid gap-4">
    <div v-for="card in cards" :key="card.label" class="rounded-2xl bg-white/5 p-4 shadow-inner">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm text-white/60">{{ card.label }}</p>
          <p class="mt-1 text-xl font-semibold text-white">{{ card.value }}</p>
        </div>
        <div class="text-2xl" :class="card.iconColor">{{ card.icon }}</div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{ summary: any }>()

const cards = computed(() => [
  { label: 'Treinos/Semana', value: props.summary.weekly_workouts_total ?? 0, icon: 'T', iconColor: 'text-emerald-400' },
  { label: 'Sequencia', value: props.summary.streak_days ? `${props.summary.streak_days} dias` : '0', icon: 'S', iconColor: 'text-sky-400' },
  {
    label: 'Exercicios Hoje',
    value: `${props.summary.today_exercises_done ?? 0}/${props.summary.today_exercises_total ?? 0}`,
    icon: 'E',
    iconColor: 'text-emerald-400'
  },
  {
    label: 'Tempo Medio',
    value: props.summary.avg_workout_minutes ? `${props.summary.avg_workout_minutes} min` : '-',
    icon: 'M',
    iconColor: 'text-fuchsia-400'
  }
])
</script>
