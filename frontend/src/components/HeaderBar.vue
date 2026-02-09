<template>
  <header class="flex items-center justify-between">
    <div class="flex items-center gap-3">
      <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-500/20 text-sm font-semibold text-emerald-200">
        MV
      </div>
      <div>
        <p class="text-sm text-white/80">Movic</p>
        <p class="text-sm text-emerald-400">Bem-vindo, {{ name }}!</p>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <button class="rounded-full bg-white/5 p-2 text-white/80 hover:text-white" @click="$emit('settings')">
        <span>CFG</span>
      </button>
      <button class="rounded-full bg-white/5 p-2 text-white/80 hover:text-white" @click="$emit('logout')">
        <span>OUT</span>
      </button>
    </div>
  </header>
</template>

<script setup lang="ts">
defineProps<{ name: string }>()
</script>
