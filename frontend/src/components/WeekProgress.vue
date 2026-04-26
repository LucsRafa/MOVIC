<template>
  <div class="max-w-full overflow-hidden rounded-2xl bg-white/5 p-4">
    <div class="mb-3">
      <h3 class="text-lg font-semibold text-white">Semana atual</h3>
      <p class="text-sm text-white/50">Seu progresso semanal</p>
    </div>

    <div class="space-y-3">
      <button
        v-for="day in days"
        :key="day.weekday"
        class="w-full max-w-full overflow-hidden rounded-xl border border-white/10 px-4 py-3 text-left hover:bg-white/10"
        :class="day.status === 'completed' ? 'border-emerald-400/30 bg-emerald-500/10' : day.status === 'missed' ? 'border-red-400/30 bg-red-500/10' : 'bg-white/5'"
        @click="$emit('select', day.weekday)"
      >
        <div class="flex items-center gap-3">
          <div
            class="flex h-8 w-8 items-center justify-center rounded-full border"
            :class="day.status === 'completed' ? 'border-emerald-400 text-emerald-300' : day.status === 'missed' ? 'border-red-400 text-red-300' : 'border-white/20 text-white/60'"
          >
            <span v-if="day.status === 'completed'">OK</span>
            <span v-else-if="day.status === 'missed'">x</span>
            <span v-else>o</span>
          </div>
          <div class="min-w-0">
            <p class="text-white">{{ weekdayLabel(day.weekday) }}</p>
            <p class="break-words text-xs text-white/60">{{ day.title }}</p>
          </div>
        </div>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{ days: any[] }>()

const labels = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab']
const weekdayLabel = (weekday: number) => labels[weekday] || ''
</script>
