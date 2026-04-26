<template>
  <header class="flex flex-col gap-4 rounded-2xl bg-white/5 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-3">
      <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-white/10 text-sm font-semibold text-white">
        <img v-if="resolvedAvatarUrl" :src="resolvedAvatarUrl" alt="Foto de perfil" class="h-full w-full object-cover" />
        <span v-else>{{ initials }}</span>
      </div>
      <div>
        <p class="text-sm font-semibold">Movic</p>
        <p class="text-xs text-emerald-400">Painel do Professor</p>
      </div>
    </div>
    <div class="flex w-full items-center justify-between gap-3 text-sm sm:w-auto sm:justify-end">
      <span class="hidden text-white/70 sm:block">Bem-vindo, {{ name }}</span>
      <button
        class="flex min-h-[40px] items-center justify-center rounded-full bg-white/10 px-4 text-white/70 hover:bg-white/15 hover:text-white"
        @click="$emit('settings')"
      >
        Ajustes
      </button>
      <button
        class="flex min-h-[40px] items-center justify-center rounded-full bg-white/10 px-4 text-white/70 hover:bg-white/15 hover:text-white"
        @click="$emit('logout')"
      >
        Sair
      </button>
    </div>
  </header>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { resolveMediaUrl } from '../utils/media'

const props = defineProps<{ name: string; avatarUrl?: string | null }>()

const resolvedAvatarUrl = computed(() => resolveMediaUrl(props.avatarUrl))

const initials = computed(() => {
  const parts = (props.name || '').trim().split(/\s+/).filter(Boolean)
  return parts.length ? (parts[0][0] + (parts[1]?.[0] || '')).toUpperCase() : 'MV'
})
</script>
