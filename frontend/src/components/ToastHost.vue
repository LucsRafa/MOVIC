<template>
  <div class="pointer-events-none fixed left-1/2 top-20 z-[999] flex w-[90%] max-w-sm -translate-x-1/2 flex-col gap-2 sm:left-auto sm:right-4 sm:top-4 sm:w-full sm:translate-x-0">
    <div
      v-for="toast in toasts"
      :key="toast.id"
      class="pointer-events-auto rounded-xl border px-4 py-3 text-sm shadow-lg break-words"
      :class="toastClass(toast.type)"
    >
      <div class="flex items-start justify-between gap-3">
        <span class="min-w-0 flex-1 break-words">{{ toast.message }}</span>
        <button class="text-xs text-white/70 hover:text-white" @click="remove(toast.id)">x</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useToastStore } from '../stores/toast'

const toastStore = useToastStore()
const toasts = computed(() => toastStore.items)

const toastClass = (type: string) => {
  if (type === 'success') return 'border-emerald-400/30 bg-emerald-500/10 text-emerald-100'
  if (type === 'error') return 'border-red-400/30 bg-red-500/10 text-red-100'
  return 'border-white/10 bg-white/10 text-white'
}

const remove = (id: number) => {
  toastStore.remove(id)
}
</script>
