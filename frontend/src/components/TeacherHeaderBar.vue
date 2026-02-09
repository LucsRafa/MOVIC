<template>
  <header class="flex items-center justify-between rounded-2xl bg-white/5 px-4 py-3">
    <div class="flex items-center gap-3">
      <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/10 text-sm font-semibold">
        MV
      </div>
      <div>
        <p class="text-sm font-semibold">Movic</p>
        <p class="text-xs text-emerald-400">Painel do Professor</p>
      </div>
    </div>
    <div class="flex items-center gap-3 text-sm">
      <span class="hidden text-white/70 sm:block">Bem-vindo, {{ name }}</span>
      <button
        class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-white/70 hover:text-white"
        @click="$emit('settings')"
      >
        ?
      </button>
      <button
        class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-white/70 hover:text-white"
        @click="$emit('logout')"
      >
        ?
      </button>
    </div>
  </header>
</template>

<script setup lang="ts">
defineProps<{ name: string }>()
</script>
