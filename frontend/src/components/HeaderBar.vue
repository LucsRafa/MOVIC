<template>
  <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-3">
      <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-emerald-500/20 text-sm font-semibold text-emerald-200">
        <img v-if="resolvedAvatarUrl" :src="resolvedAvatarUrl" alt="Foto de perfil" class="h-full w-full object-cover" />
        <span v-else>{{ initials }}</span>
      </div>
      <div>
        <p class="text-sm text-white/80">Movic</p>
        <p class="text-sm text-emerald-400">Bem-vindo, {{ name }}!</p>
      </div>
    </div>

    <div class="flex w-full items-center justify-between gap-3 sm:w-auto sm:justify-end">
      <button class="rounded-full bg-white/5 px-4 py-2 text-white/80 hover:bg-white/10 hover:text-white" @click="$emit('settings')">
        <span>Ajustes</span>
      </button>
      <button class="rounded-full bg-white/5 px-4 py-2 text-white/80 hover:bg-white/10 hover:text-white" @click="$emit('logout')">
        <span>Sair</span>
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
