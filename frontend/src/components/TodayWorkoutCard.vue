<template>
  <div class="rounded-2xl bg-white/5 p-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div class="min-w-0">
        <p class="text-sm text-white/70">{{ dayName }}</p>
        <h3 class="text-lg font-semibold text-white">{{ title }}</h3>
        <p class="text-xs text-white/50" v-if="subtitle">{{ subtitle }}</p>
      </div>
      <span class="w-fit rounded-full bg-sky-500/20 px-3 py-1 text-xs text-sky-200">
        {{ items.length }} exercícios
      </span>
    </div>

    <div class="mt-4">
      <div class="flex items-center justify-between text-xs text-white/60">
        <span>Progresso do Treino</span>
        <span>{{ progress }}%</span>
      </div>
      <div class="mt-2 h-2 w-full rounded-full bg-white/10">
        <div class="h-2 rounded-full bg-emerald-400" :style="{ width: `${progress}%` }"></div>
      </div>
    </div>

    <div class="mt-4 space-y-3">
      <WorkoutItemRow
        v-for="(item, idx) in items"
        :key="item.workout_item_id"
        :item="item"
        :index="idx"
        :disabled="disableActions"
        @toggle="$emit('toggle', item)"
        @video="$emit('video', item)"
      />
    </div>

    <button
      v-if="showFinish"
      class="mt-4 w-full rounded-xl bg-emerald-500 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-400 disabled:hover:bg-emerald-500"
      :disabled="isFinishing || disableActions"
      @click="$emit('finish')"
    >
      {{ isFinishing ? 'Finalizando...' : 'Finalizar treino' }}
    </button>
  </div>
</template>

<script setup lang="ts">
import WorkoutItemRow from './WorkoutItemRow.vue'

defineProps<{
  dayName: string
  title: string
  subtitle?: string
  items: any[]
  progress: number
  showFinish?: boolean
  isFinishing?: boolean
  disableActions?: boolean
}>()
</script>
