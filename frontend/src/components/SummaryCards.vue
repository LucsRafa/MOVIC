<template>
  <div class="grid max-w-full gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div
      v-for="card in cards"
      :key="card.label"
      class="max-w-full overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-4 shadow-inner"
    >
      <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/5">
          <component :is="card.icon" class="h-6 w-6" :class="card.iconColor" />
        </div>

        <div class="min-w-0">
          <p class="text-sm text-white/60">{{ card.label }}</p>
          <p class="mt-1 break-words text-xl font-semibold text-white">{{ card.value }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {
  CalendarDaysIcon,
  CheckCircleIcon,
  ClockIcon,
  FireIcon
} from '@heroicons/vue/24/outline'
import { computed } from 'vue'

const props = defineProps<{ summary: any }>()

const safeAverageMinutes = computed(() => {
  const rawValue = props.summary?.avg_minutes ?? props.summary?.avg_workout_minutes
  const value = Number(rawValue)

  return Number.isFinite(value) && value >= 0 ? Math.max(0, Math.round(value)) : null
})

const cards = computed(() => [
  {
    label: 'Treinos/Semana',
    value: props.summary.weekly_workouts_total ?? 0,
    icon: CalendarDaysIcon,
    iconColor: 'text-emerald-400'
  },
  {
    label: 'Sequência',
    value: props.summary.streak_days ? `${props.summary.streak_days} dias` : '0',
    icon: FireIcon,
    iconColor: 'text-orange-400'
  },
  {
    label: 'Exercícios hoje',
    value: `${props.summary.today_exercises_done ?? 0}/${props.summary.today_exercises_total ?? 0}`,
    icon: CheckCircleIcon,
    iconColor: 'text-emerald-400'
  },
  {
    label: 'Tempo médio',
    value: safeAverageMinutes.value !== null ? `${safeAverageMinutes.value} min` : '-',
    icon: ClockIcon,
    iconColor: 'text-fuchsia-400'
  }
])
</script>
