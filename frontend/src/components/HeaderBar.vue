<template>
  <header class="flex items-center justify-between">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 overflow-hidden rounded-lg bg-white/10">
        <img alt="Movic" class="h-full w-full object-cover" src="/logo.png" onerror="this.style.display='none'" />
      </div>
      <div>
        <p class="text-sm text-white/80">Movic</p>
        <p class="text-sm text-emerald-400">Bem-vindo, {{ name }}!</p>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <button class="rounded-full bg-white/5 p-2 text-white/80 hover:text-white" @click="$emit('settings')">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
          <path d="M12 8.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Z" stroke="currentColor" stroke-width="1.5" />
          <path
            d="M19.4 15a7.96 7.96 0 0 0 .1-6l1.9-1.5-2-3.5-2.3.7a8.1 8.1 0 0 0-5.1-3l-.4-2.4h-4l-.4 2.4a8.1 8.1 0 0 0-5.1 3l-2.3-.7-2 3.5L4.5 9a7.96 7.96 0 0 0 .1 6l-1.9 1.5 2 3.5 2.3-.7a8.1 8.1 0 0 0 5.1 3l.4 2.4h4l.4-2.4a8.1 8.1 0 0 0 5.1-3l2.3.7 2-3.5-1.9-1.5Z"
            stroke="currentColor"
            stroke-width="1.5"
          />
        </svg>
      </button>
      <button class="rounded-full bg-white/5 p-2 text-white/80 hover:text-white" @click="$emit('logout')">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
          <path d="M9 7V5a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6a2 2 0 0 1-2-2v-2" stroke="currentColor" stroke-width="1.5" />
          <path d="M13 12H3m0 0 3-3m-3 3 3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </button>
    </div>
  </header>
</template>

<script setup lang="ts">
defineProps<{ name: string }>()
</script>
