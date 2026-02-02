<template>
  <div
    class="rounded-2xl border border-white/10 bg-white/5 p-4"
    :class="item.is_checked ? 'bg-emerald-500/10 border-emerald-400/30' : ''"
  >
    <div class="flex items-start gap-4">
      <div
        class="flex h-9 w-9 items-center justify-center rounded-full border"
        :class="item.is_checked ? 'border-emerald-400 text-emerald-300' : 'border-white/20 text-white/60'"
      >
        <span class="text-sm">{{ index + 1 }}</span>
      </div>
      <div class="flex-1">
        <div class="flex items-center justify-between">
          <p class="text-white" :class="item.is_checked ? 'line-through text-white/50' : ''">
            {{ item.exercise.name }}
          </p>
          <button
            class="rounded-lg bg-white/10 px-3 py-1 text-xs text-sky-300 hover:bg-white/20"
            @click="$emit('video', item)"
          >
            Ver Video
          </button>
        </div>
        <p class="mt-1 text-xs text-white/60">
          {{ item.sets }}x{{ item.reps }} · Descanso: {{ item.rest_seconds || 0 }}s
        </p>
      </div>
      <button
        class="mt-1 h-6 w-6 rounded-full border"
        :class="item.is_checked ? 'border-emerald-400 bg-emerald-400/20' : 'border-white/30'"
        @click="$emit('toggle', item)"
      ></button>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{ item: any; index: number }>()
</script>
